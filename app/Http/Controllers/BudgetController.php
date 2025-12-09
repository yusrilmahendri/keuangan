<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget; 
use App\Service\SaldoService;
use Yajra\DataTables\Facades\DataTables;

class BudgetController extends Controller
{   

    protected $saldoService;
    public function __construct(SaldoService $saldoService)
    {
        $this->saldoService = $saldoService;
    }
    public function data()
    {
        $budget = Budget::orderBy('periode', 'desc');

        return DataTables::of($budget)
            ->addColumn('amount', function ($model) {
                return 'Rp ' . number_format($model->amount, 0, ',', '.');
            })
            ->addColumn('action', 'budgets.action')
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        // Hitung sisa saldo
        $sisaSaldo = $this->saldoService->getTotalSaldo() - Budget::sum('amount');

        // ID budget yang ingin disimpan/update
        $budgetId = 1;

        // Simpan atau update berdasarkan ID
        $budget = Budget::updateOrCreate(
            ['id' => $budgetId],       // kondisi pencarian
            ['amount_saldo' => $sisaSaldo] // data yang diperbarui
        );

        return view('budgets.index', [
            'updated_saldo' => $budget,
            'budgets'       => Budget::all(),
            'total_saldo'   => $sisaSaldo,
            'title'         => 'Budget List',
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('budgets.create', [
            'title' => 'Tambah Anggaran',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required',
            'periode' => 'required|date',
            'description' => 'nullable|max:255',
        ]);

        Budget::create([
            'amount' => $this->cleanRupiah($validatedData['amount']),
            'periode' => $validatedData['periode'],
            'description' => $validatedData['description'] ?? null,
        ]); 

        return redirect()->route('budgets.index')
            ->with('success', 'Budget berhasil ditambahkan!');  
    }

    private function cleanRupiah($rupiah)
    {
        // Hapus "Rp", spasi, dan titik
        $clean = str_replace(['Rp', ' ', '.'], '', $rupiah);

        // Ubah koma menjadi titik jika ada
        $clean = str_replace(',', '.', $clean);

        return (float) $clean;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('budgets.show', [
            'budget' => Budget::findOrFail($id),
            'title' => 'Detail Budget',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('budgets.edit', [
            'budget' => Budget::findOrFail($id),
            'title' => 'Edit Budget',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'amount' => 'required',
            'periode_saldo' => 'required|date',
            'description' => 'nullable|max:255',
        ]);

        $budget = Budget::findOrFail($id);
        $budget->update([
            'amount' => $this->cleanRupiah($validatedData['amount']),
            'periode' => $validatedData['periode_saldo'],
            'description' => $validatedData['description'] ?? null,
        ]);

        return redirect()->route('budgets.index')
            ->with('info', 'Budget berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
