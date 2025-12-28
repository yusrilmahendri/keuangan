<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Saldo;
use Illuminate\Support\Facades\DB;
use App\Service\BudgetService;
use Illuminate\Support\Facades\Storage;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionsController extends Controller
{   
    public function data()
    {
        $transactions = Transaction::orderBy('transaction_date', 'desc');

        return DataTables::of($transactions)
            ->addColumn('name', function (Transaction $model) {
                return $model->category->name ?? '-';
            })

            ->addColumn('name_items', function (Transaction $model) {

                if ($model->items->isEmpty()) {
                    return '-';
                }

                $html = '<ul style="padding-left:16px; margin:0;">';

                foreach ($model->items as $item) {
                    $html .= '<li>';
                    $html .= '<strong>' . ($item->name ?? '-') . '</strong>';
                    $html .= '<br>Jumlah: ' . ($item->quantity ?? 0);
                    $html .= '<br>Harga: Rp ' . number_format($item->price, 0, ',', '.');

                    if (!empty($item->note)) {
                        $html .= '<br>Keterangan: ' . $item->note;
                    }

                    $html .= '</li><br>';
                }

                $html .= '</ul>';

                return $html;
            })

            // FORMAT RUPIAH
            ->editColumn('amount', function ($row) {
                return 'Rp ' . number_format($row->amount, 0, ',', '.');
            })

            // FORMAT DESCRIPTION
            ->editColumn('description', function ($row) {
                return $row->description ?: '-';
            })

            // FORMAT TANGGAL KE d M Y
            ->editColumn('transaction_date', function ($row) {
                return \Carbon\Carbon::parse($row->transaction_date)->format('d M Y');
            })


            ->addColumn('action', 'transactions.action')
            ->addIndexColumn()

            ->rawColumns(['action', 'name_items']) // 🔥 WAJIB AGAR RENDER HTML
            ->toJson();
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Total saldo dari tabel saldos
        $totalSaldo = Saldo::sum('amount');

        // Total amount dari tabel transactions
        $totalAmount = Transaction::sum('amount');

        // Total price dari tabel transaction_items
        // TANPA dikali quantity, sesuai permintaan Anda
        $totalPrice = TransactionItem::sum('price');

        // Total keseluruhan amount + price
        $totalSemua = $totalAmount + $totalPrice;

        // Sisa saldo (saldo - total transaksi)
        $sisaSaldo = $totalSaldo - $totalSemua;
        $dateTransaksi = Transaction::latest()->first();
  
        return view('transactions.index', [
            'transaksi' => Transaction::all(),
            'dateTransaksi' => $dateTransaksi,
            'title' => 'Transaction List',
            'totalAmount' => $totalAmount,
            'totalPrice' => $totalPrice,
            'totalSemua' => $totalSemua,
            'totalSaldo' => $totalSaldo,
            'sisaSaldo' => $sisaSaldo,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('transactions.create', [
            'title' => 'Tambah Transaksi',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Simpan file nota
        $notaFile = null;
        if ($request->hasFile('nota')) {
            $notaFile = $request->file('nota')->store('nota', 'public');
        }

        // Hapus format Rupiah dari total
        $total = preg_replace('/[Rp\s\.]/', '', $request->total);

        // Simpan transaksi utama
        $trx = Transaction::create([
            'amount' => $total, // sudah bersih
            'transaction_date'  => $request->date,
            'description' => $request->description,
            'nota'  => $notaFile
        ]);

        // Simpan detail barang (jika ada)
        if ($request->items) {
            foreach ($request->items as $item) {
                if (!empty($item['price'])) {
                    // Hapus format Rupiah dari harga item
                    $price = preg_replace('/[Rp\s\.]/', '', $item['price']);

                    TransactionItem::create([
                        'transaction_id' => $trx->id,
                        'name'     => $item['name'] ?? null,
                        'quantity' => $item['quantity'] ?? 1,
                        'price'    => $price,
                        'note'     => $item['note'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('transactions.edit', [
            'transaction' => Transaction::with('items')->findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::with('items')->findOrFail($id);

        // ==========================
        // VALIDASI
        // ==========================
        $validated = $request->validate([
            'amount' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',

            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer',
            'items.*.name' => 'nullable|string',
            'items.*.quantity' => 'nullable',
            'items.*.price' => 'nullable|string',
            'items.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // FORMAT TOTAL
            $amount = preg_replace('/[^0-9]/', '', $validated['amount']);

            // UPDATE TRANSAKSI
            $transaction->update([
                'amount' => $amount,
                'description' => $validated['description'] ?? null,
                'transaction_date' => $validated['date'],
            ]);

            // HANDLING NOTA
            if ($request->hasFile('nota')) {
                if ($transaction->nota) {
                    Storage::disk('public')->delete($transaction->nota);
                }

                $notaPath = $request->file('nota')->store('nota', 'public');
                $transaction->update(['nota' => $notaPath]);
            }

            // ======================================
            // FIX UTAMA: HANDLE JIKA ITEMS KOSONG
            // ======================================
            if (!isset($validated['items']) || count($validated['items']) === 0) {

                // Hapus semua item lama
                $transaction->items()->delete();

                DB::commit();
                return redirect()
                    ->route('transactions.index')
                    ->with('success', 'Transaction updated successfully (without items)!');
            }

            // ======================================
            // PROSES ITEMS JIKA ADA
            // ======================================

            $existingIds = $transaction->items->pluck('id')->toArray();

            $requestIds = collect($validated['items'])
                ->pluck('id')
                ->filter()
                ->toArray();

            // HAPUS ITEM LAMA YANG DI REMOVE
            $idsToDelete = array_diff($existingIds, $requestIds);
            TransactionItem::whereIn('id', $idsToDelete)->delete();

            // UPDATE / INSERT item baru
            foreach ($validated['items'] as $item) {

                // Convert harga
                $price = isset($item['price'])
                    ? preg_replace('/[^0-9]/', '', $item['price'])
                    : 0;

                // Update item lama
                if (isset($item['id'])) {
                    TransactionItem::where('id', $item['id'])->update([
                        'name' => $item['name'] ?? null,
                        'quantity' => $item['quantity'] ?? null,
                        'price' => $price,
                        'note' => $item['note'] ?? null,
                    ]);
                }

                // Insert item baru
                else {
                    $transaction->items()->create([
                        'name' => $item['name'] ?? null,
                        'quantity' => $item['quantity'] ?? null,
                        'price' => $price,
                        'note' => $item['note'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaction updated successfully!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Export transactions to Excel
     */
    public function exportExcel()
    {
        return Excel::download(new TransactionExport, 'data-transaksi-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export transactions to PDF
     */
    public function exportPdf()
    {
        $transactions = Transaction::with(['category', 'items'])->orderBy('transaction_date', 'desc')->get();
        $totalTransaksi = Transaction::sum('amount');

        $pdf = Pdf::loadView('transactions.pdf', [
            'transactions' => $transactions,
            'totalTransaksi' => $totalTransaksi,
        ]);

        return $pdf->download('data-transaksi-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        // Hapus file nota jika ada
        if ($transaction->nota) {
            Storage::disk('public')->delete($transaction->nota);
        }
        
        // Hapus transaction items (cascade delete)
        $transaction->items()->delete();
        
        // Hapus transaction
        $transaction->delete();
        
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus!');
    }
}