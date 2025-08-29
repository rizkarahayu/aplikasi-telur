<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualans = Penjualan::with('produk')->latest()->get();
        $produks = Produk::all(); // untuk dropdown produk
        return view('penjualan.index', compact('penjualans', 'produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'mode' => 'required|in:kg,peti',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'nullable|integer|min:1', // boleh kosong (pakai harga default)
        ]);

        $produk = \App\Models\Produk::findOrFail($request->produk_id);

        // Tentukan harga default sesuai mode
        $hargaDefault = $request->mode === 'kg'
            ? $produk->harga_per_kg
            : $produk->harga_per_peti;

        // Jika pegawai isi manual, pakai harga input. Kalau kosong, pakai harga default
        $hargaSatuan = $request->harga_satuan ?: $hargaDefault;

        $total = $request->jumlah * $hargaSatuan;
        // dd($request->produk_id);
        \App\Models\Penjualan::create([
            // 'produk_id' => $produk->id,
            'produk_id' => $request->produk_id,
            'mode' => $request->mode,
            'jumlah' => $request->jumlah,
            'harga_satuan' => $hargaSatuan,
            'total' => $total,
        ]);

        return redirect()->back()->with('success', 'Penjualan berhasil disimpan!');
    }
}

