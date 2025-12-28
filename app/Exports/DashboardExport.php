<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCharts;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title as ChartTitle;

class DashboardExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new SummarySheet($this->data),
            new MonthlyExpenseSheet($this->data['pengeluaranBulanan']),
            new CategorySaldoSheet($this->data['saldoPerKategori']),
        ];
    }
}

class SummarySheet implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect([
            ['Total Saldo', 'Rp ' . number_format($this->data['totalSaldo'], 0, ',', '.')],
            ['Total Pengeluaran', 'Rp ' . number_format($this->data['totalPengeluaran'], 0, ',', '.')],
            ['Sisa Saldo', 'Rp ' . number_format($this->data['sisaSaldo'], 0, ',', '.')],
            ['Jumlah Transaksi', $this->data['jumlahTransaksi']],
        ]);
    }

    public function headings(): array
    {
        return ['Keterangan', 'Nilai'];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }
}

class MonthlyExpenseSheet implements FromCollection, WithHeadings, WithTitle, WithCharts
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function($item, $index) {
            return [
                'bulan' => $item['bulan'],
                'total' => $item['total'],
            ];
        });
    }

    public function headings(): array
    {
        return ['Bulan', 'Total Pengeluaran'];
    }

    public function title(): string
    {
        return 'Pengeluaran Bulanan';
    }

    public function charts()
    {
        $dataCount = count($this->data);
        
        // Data series labels
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Pengeluaran Bulanan!$B$1', null, 1),
        ];

        // X-Axis (Categories - Bulan)
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Pengeluaran Bulanan!$A$2:$A$' . ($dataCount + 1), null, $dataCount),
        ];

        // Y-Axis (Values - Total)
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Pengeluaran Bulanan!$B$2:$B$' . ($dataCount + 1), null, $dataCount),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        
        // Set the chart legend
        $legend = new Legend(Legend::POSITION_TOP, null, false);
        
        // Create the chart
        $chart = new Chart(
            'chart1',
            new ChartTitle('Grafik Pengeluaran per Bulan'),
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('D2');
        $chart->setBottomRightPosition('M20');

        return $chart;
    }
}

class CategorySaldoSheet implements FromCollection, WithHeadings, WithTitle, WithCharts
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function($item, $index) {
            return [
                'kategori' => $item['name'],
                'total' => $item['y'],
            ];
        });
    }

    public function headings(): array
    {
        return ['Kategori', 'Total Saldo'];
    }

    public function title(): string
    {
        return 'Saldo per Kategori';
    }

    public function charts()
    {
        $dataCount = count($this->data);
        
        if ($dataCount == 0) {
            return null;
        }

        // Data series labels
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Saldo per Kategori!$B$1', null, 1),
        ];

        // Categories (Kategori names)
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Saldo per Kategori!$A$2:$A$' . ($dataCount + 1), null, $dataCount),
        ];

        // Values (Total Saldo)
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Saldo per Kategori!$B$2:$B$' . ($dataCount + 1), null, $dataCount),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        
        // Set the chart legend
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        
        // Create the chart
        $chart = new Chart(
            'chart2',
            new ChartTitle('Grafik Saldo per Kategori'),
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('D2');
        $chart->setBottomRightPosition('M20');

        return $chart;
    }
}
