<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Service\BudgetService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function index()
    {
        // Total Budget
        $totalBudget = $this->budgetService->getTotalBudget();

        // Total Pengeluaran (amount + price item)
        $totalAmount = Transaction::sum('amount');
        $totalPrice  = TransactionItem::sum('price');
        $totalPengeluaran = $totalAmount + $totalPrice;

        // Sisa Budget
        $sisaBudget = $totalBudget - $totalPengeluaran;

        // Transaksi Terakhir
        $lastTrans = Transaction::latest()->first();

        // Jumlah Transaksi
        $jumlahTransaksi = Transaction::count();

        // ---- Grafik 12 Bulan (Jan–Des) ----
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

        return view('dashboard', [
            'totalBudget'       => $totalBudget,
            'totalPengeluaran'  => $totalPengeluaran,
            'sisaBudget'        => $sisaBudget,
            'lastTrans'         => $lastTrans,
            'jumlahTransaksi'   => $jumlahTransaksi,
            'pengeluaranBulanan'=> $pengeluaranBulanan,
        ]);
    }
}