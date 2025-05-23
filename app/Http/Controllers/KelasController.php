<?php

namespace App\Http\Controllers;

use App\Models\kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = Http::get('http://localhost:8080/kelas');

        if ($response->successful()) {
            // mengurutkan data dosen berdasarkan NIDN
            $kelas = collect($response->json())->sortBy('kode_kelas')->values();
            return view('Kelas', compact('kelas'));
        } else {
            //jika gagal mengambil data, kembali ke halaman sebelumnya dengan pesan error
            return back()->with('error', 'Gagal mengambil data kelas');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('tambahkelas');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        try {
            $validate = $request->validate([
                'kode_kelas' => 'required',
                'nama_kelas' => 'required',
            ]);

            Http::asForm()->post('http://localhost:8080/kelas', $validate);

            return redirect()->route('Kelas.index')->with('success', 'Kelas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($kode_kelas)
    {
        //
        $response = Http::get("http://localhost:8080/kelas/$kode_kelas");
        if ($response->successful()) {
            $kelas = $response->json();
            return view('editkelas', ['kelas' => $kelas]);
        } else {
            return redirect()->route('Kelas.index')->with('error', 'Data kelas tidak ditemukan.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kelas)
    {
        //
        try {
            $validate = $request->validate([
                'kode_kelas' => 'required',
                'nama_kelas' => 'required'
            ]);

            Http::put("http://localhost:8080/kelas/$kelas", $validate);

            response()->json([
                'success' => true,
                'message' => 'Kelas berhasil diperbarui',
                'data' => $request
            ], 200);
            //redirect ke halaman index dosen
            return redirect()->route('Kelas.index');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kelas)
    {
        //
        Http::delete("http://localhost:8080/kelas/$kelas");
        return redirect()->route('Kelas.index');
    }
}
