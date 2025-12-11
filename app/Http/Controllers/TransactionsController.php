<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use App\Service\BudgetService;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class TransactionsController extends Controller
{   
    protected $budgetService;
    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function data()
    {
        $transactions = Transaction::with(['category', 'items'])
            ->orderBy('transaction_date', 'desc');

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

            // FORMAT TANGGAL KE Y-m-d
            ->editColumn('transaction_date', function ($row) {
                return \Carbon\Carbon::parse($row->transaction_date)->format('Y-m-d');
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
        // Total budget dari service
        $totalBudget = $this->budgetService->getTotalBudget();

        // Total amount dari tabel transactions
        $totalAmount = Transaction::sum('amount');

        // Total price dari tabel transaction_items
        // TANPA dikali quantity, sesuai permintaan Anda
        $totalPrice = TransactionItem::sum('price');

        // Total keseluruhan amount + price
        $totalSemua = $totalAmount + $totalPrice;

        // Sisa budget
        $sisaBudget = $totalBudget - $totalSemua;
        $updated_saldo = Budget::orderBy('periode', 'desc')->first();
        $dateTransaksi = Transaction::latest()->first();
  
        return view('transactions.index', [
            'transaksi' => Transaction::all(),
            'dateTransaksi' => $dateTransaksi,
            'title' => 'Transaction List',
            'totalAmount' => $totalAmount,
            'totalPrice' => $totalPrice,
            'totalSemua' => $totalSemua,
            'sisaBudget' => $sisaBudget,
            'updated_saldo' => $updated_saldo,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('transactions.create', [
            'categories' => Category::all(),
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
            'category_id' => $request->category_id,
            'total' => $total, // sudah bersih
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
            'categories' => Category::all(),
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
        'category_id' => 'required|exists:categories,id',
        'amount' => 'required|string',
        'description' => 'nullable|string',
        'date' => 'required|date',

        'items' => 'required|array',
        'items.*.name' => 'required|string',
        'items.*.quantity' => 'required|numeric|min:1',
        'items.*.price' => 'required|string',
        'items.*.note' => 'nullable|string',
    ]);

    DB::beginTransaction();
    try {

        // ==========================
        // FORMAT TOTAL → ANGKA
        // ==========================
        $amount = preg_replace('/[^0-9]/', '', $validated['total']);

        // ==========================
        // UPDATE TRANSAKSI
        // ==========================
        $transaction->update([
            'category_id' => $validated['category_id'],
            'amount' => $amount,
            'description' => $validated['description'],
            'transaction_date' => $validated['date'],
        ]);

        // ==========================
        // HANDLING NOTA UPLOAD
        // ==========================
        if ($request->hasFile('nota')) {

            if ($transaction->nota) {
                Storage::delete($transaction->nota);
            }

            $notaPath = $request->file('nota')->store('nota');
            $transaction->update(['nota' => $notaPath]);
        }

        // ==========================
        // PROSES ITEMS
        // ==========================

        $existingIds = $transaction->items->pluck('id')->toArray();
        $requestIds = collect($validated['items'])->pluck('id')->filter()->toArray();

        // --- HAPUS ITEM YANG DIHAPUS DI FORM
        $idsToDelete = array_diff($existingIds, $requestIds);
        TransactionItem::whereIn('id', $idsToDelete)->delete();

        // --- UPDATE ATAU INSERT ITEM BARU
        foreach ($validated['items'] as $item) {

            // Format harga → angka
            $price = preg_replace('/[^0-9]/', '', $item['price']);

            if (isset($item['id'])) {

                // UPDATE ITEM LAMA
                TransactionItem::where('id', $item['id'])->update([
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'note' => $item['note'] ?? null
                ]);

            } else {

                // INSERT ITEM BARU
                $transaction->items()->create([
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
