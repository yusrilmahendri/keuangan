<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Saldo;
use App\Models\Category;
use App\Service\SaldoService;
use Carbon\Carbon;
use App\Exports\DashboardExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    protected $saldoService;

    public function __construct(SaldoService $saldoService)
    {
        $this->saldoService = $saldoService;
    }

    public function index()
    {
        // Total Saldo
        $totalSaldo = Saldo::sum('amount');

        // Total Pengeluaran (amount + price item)
        $totalAmount = Transaction::sum('amount');
        $totalPengeluaran = $totalAmount;

        // Sisa Saldo
        $sisaSaldo = $totalSaldo - $totalPengeluaran;

        // Transaksi Terakhir
        $lastTrans = Transaction::latest()->first();

        // Jumlah Transaksi
        $jumlahTransaksi = Transaction::count();

        // Categories for filter
        $categories = Category::all();

        // ---- Grafik 12 Bulan (Jan–Des) ----
        $pengeluaranBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulanNama = Carbon::create()->month($i)->translatedFormat('M');

            $total = Transaction::whereMonth('created_at', $i)->sum('amount');

            $pengeluaranBulanan[] = [
                'bulan' => $bulanNama,
                'total' => $total,
            ];
        }

        // ---- Saldo per Kategori ----
        $saldoPerKategori = [];
        foreach ($categories as $category) {
            $totalSaldoCategory = Saldo::where('category_id', $category->id)->sum('amount');

            if ($totalSaldoCategory > 0) {
                $saldoPerKategori[] = [
                    'name' => $category->name,
                    'y' => $totalSaldoCategory,
                ];
            }
        }

        return view('dashboard', [
            'totalSaldo'        => $totalSaldo,
            'totalPengeluaran'  => $totalPengeluaran,
            'sisaSaldo'         => $sisaSaldo,
            'lastTrans'         => $lastTrans,
            'jumlahTransaksi'   => $jumlahTransaksi,
            'pengeluaranBulanan'=> $pengeluaranBulanan,
            'categories'        => $categories,
            'saldoPerKategori'  => $saldoPerKategori,
        ]);
    }

    /**
     * Export dashboard to Excel
     */
    public function exportExcel()
    {
        $data = $this->getDashboardData();
        return Excel::download(new DashboardExport($data), 'laporan-dashboard-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export dashboard to PDF
     */
    public function exportPdf()
    {
        $data = $this->getDashboardData();

        $pdf = Pdf::loadView('dashboard-pdf', $data);
        return $pdf->download('laporan-dashboard-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Get dashboard data for export
     */
    private function getDashboardData()
    {
        // Total Saldo
        $totalSaldo = Saldo::sum('amount');

        // Total Pengeluaran
        $totalAmount = Transaction::sum('amount');
        $totalPengeluaran = $totalAmount;

        // Sisa Saldo
        $sisaSaldo = $totalSaldo - $totalPengeluaran;

        // Jumlah Transaksi
        $jumlahTransaksi = Transaction::count();

        // Categories
        $categories = Category::all();

        // Pengeluaran Bulanan
        $pengeluaranBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulanNama = Carbon::create()->month($i)->translatedFormat('M');
            $total = Transaction::whereMonth('created_at', $i)->sum('amount')
                   + TransactionItem::whereMonth('created_at', $i)->sum('price');

            $pengeluaranBulanan[] = [
                'bulan' => $bulanNama,
                'total' => $total,
            ];
        }

        // Saldo per Kategori
        $saldoPerKategori = [];
        foreach ($categories as $category) {
            $totalSaldoCategory = Saldo::where('category_id', $category->id)->sum('amount');

            if ($totalSaldoCategory > 0) {
                $saldoPerKategori[] = [
                    'name' => $category->name,
                    'y' => $totalSaldoCategory,
                ];
            }
        }

        return [
            'totalSaldo'        => $totalSaldo,
            'totalPengeluaran'  => $totalPengeluaran,
            'sisaSaldo'         => $sisaSaldo,
            'jumlahTransaksi'   => $jumlahTransaksi,
            'pengeluaranBulanan'=> $pengeluaranBulanan,
            'saldoPerKategori'  => $saldoPerKategori,
        ];
    }
}
