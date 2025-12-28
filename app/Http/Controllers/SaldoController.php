<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Saldo;

class SaldoController extends Controller
{   

    public function data()
    {
        $saldo = Saldo::orderBy('periode_saldo', 'desc');

        return DataTables::of($saldo)
            ->addColumn('amount', function ($model) {
                return 'Rp ' . number_format($model->amount, 0, ',', '.');
            })
            ->addColumn('periode_saldo', function ($model) {
                return date('Y-m-d', strtotime($model->periode_saldo));
            })
            ->addColumn('action', 'saldo.action')
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $updated_saldo = Saldo::latest()->first(); // ambil transaksi saldo terbaru

        return view('saldo.index', [
            'Transaksi'     => Saldo::all(),
            'total_saldo'   => Saldo::sum('amount'),
            'updated_saldo' => $updated_saldo,
            'title'         => 'Saldo List',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('saldo.create', [
            'title' => 'Tambah Saldo',
            'categories' => \App\Models\Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required',
            'description' => 'required|max:255',
            'periode_saldo' => 'required|date',
            'category_id' => 'required',
        ]);

        Saldo::create([
            'amount' => $this->cleanRupiah($validatedData['amount']),
            'description' => $validatedData['description'],
            'periode_saldo' => $validatedData['periode_saldo'],
            'category_id' => $validatedData['category_id'],
        ]);

        return redirect()->route('saldos.index')->with('success', 'Saldo berhasil ditambahkan!');
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
        return view('saldo.show', [
            'title' => 'Detail Saldo',
            'Saldo' => Saldo::findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('saldo.edit', [
            'title' => 'Edit Saldo',
            'Saldo' => Saldo::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'amount' => 'required',
            'description' => 'required|max:255',
            'periode_saldo' => 'required|date',
        ]);
            
        $saldo = Saldo::findOrFail($id);
        $saldo->update([
            'amount' => $this->cleanRupiah($validatedData['amount']),
            'description' => $validatedData['description'],
            'periode_saldo' => $validatedData['periode_saldo'],
        ]);

        return redirect()->route('saldos.index')->with('info', 'Saldo berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
