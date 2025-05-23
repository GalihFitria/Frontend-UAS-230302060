<?php

namespace App\Http\Controllers;

use App\Models\Matkul;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class MatkulController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $response = Http::get('http://localhost:8080/matkul');

        if ($response->successful()) {
            // mengurutkan data dosen berdasarkan NIDN
            $matkul = collect($response->json())->sortBy('kode_matkul')->values();
            return view('Matkul', compact('matkul'));
        } else {
            //jika gagal mengambil data, kembali ke halaman sebelumnya dengan pesan error
            return back()->with('error', 'Gagal mengambil data matkul');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('tambahmatkul');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $validate = $request->validate([
                'kode_matkul' => 'required',
                'nama_matkul' => 'required',
                'sks' => 'required'

            ]);

            Http::asForm()->post('http://localhost:8080/matkul', $validate);

            return redirect()->route('Matkul.index')->with('success', 'Matkul berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($kode_matkul)
    {
        //
        $response = Http::get("http://localhost:8080/matkul/$kode_matkul");
        if ($response->successful()) {
            $matkul = $response->json();
            return view('editmatkul', ['matkul' => $matkul]);
        } else {
            return redirect()->route('Matkul.index')->with('error', 'Data matkul tidak ditemukan.');
        }
    }


    public function update(Request $request, $matkul)
    {
        //
        try {
            $validate = $request->validate([
                'kode_matkul' => 'required',
                'nama_matkul' => 'required',
                'sks' => 'required'
            ]);

            Http::put("http://localhost:8080/matkul/$matkul", $validate);

            response()->json([
                'success' => true,
                'message' => 'Matkul berhasil diperbarui',
                'data' => $request
            ], 200);
            //redirect ke halaman index dosen
            return redirect()->route('Matkul.index');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf()
    {
        $response = Http::get('http://localhost:8080/matkul');
        if ($response->successful()) {
            $matkul = collect($response->json());
            $pdf = Pdf::loadView('pdf.cetak', compact('matkul')); 
            return $pdf->download('matkul.pdf');
        } else {
            return back()->with('error', 'Gagal mengambil data untuk PDF');
        }
    }

    public function destroy($matkul)
    {
        //
        Http::delete("http://localhost:8080/matkul/$matkul");
        return redirect()->route('Matkul.index');
    }
}
