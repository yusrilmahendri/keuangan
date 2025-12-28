<?php

namespace App\Exports;

use App\Models\Saldo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SaldoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Saldo::with('category')->orderBy('periode_saldo', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Kategori',
            'Jumlah Saldo',
            'Keterangan',
            'Periode Saldo',
            'Tanggal Input',
        ];
    }

    /**
     * @var Saldo $saldo
     */
    public function map($saldo): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $saldo->category ? $saldo->category->name : '-',
            'Rp ' . number_format($saldo->amount, 0, ',', '.'),
            $saldo->description,
            \Carbon\Carbon::parse($saldo->periode_saldo)->format('d M Y'),
            $saldo->created_at->format('d M Y H:i'),
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
