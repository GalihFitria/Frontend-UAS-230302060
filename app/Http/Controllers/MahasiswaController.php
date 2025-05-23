<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $response = Http::get('http://localhost:8080/mahasiswa');


        if ($response->successful()) {
            $mahasiswa = collect($response->json())->sortBy('npm')->values();

            return view('Mahasiswa', compact('mahasiswa'));
        } else {
            return back()->with('error', 'Gagal mengambil data mahasiswa');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view ('tambahmahasiswa');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $validate = $request->validate([
                'npm' => 'required',
                'nama_mahasiswa' => 'required',
                'email' => 'required',
                'id_user' => 'required',
                'nama_kelas' => 'required',
                'username' => 'required',
                'password' => 'required',
            ]);

            Http::asForm()->post('http://localhost:8080/mahasiswa', $validate);

            return redirect()->route('Mahasiswa.index')->with('success', 'Kelas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($npm)
    {
        //
        $response = Http::get("http://localhost:8080/mahasiswa/$npm");
        if ($response->successful()) {
            $mahasiswa = $response->json();
            return view('editmahasiswa', ['mahasiswa' => $mahasiswa]);
        } else {
            return redirect()->route('Mahasiswa.index')->with('error', 'Data kelas tidak ditemukan.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $mahasiswa)
    {
        //
        try {
            $validate = $request->validate([
                'npm' => 'required',
                'nama_mahasiswa' => 'required',
                'email' => 'required',
                'id_user' => 'required',
                'nama_kelas' => 'required',
                'username' => 'required',
                'password' => 'required',
            ]);

            Http::put("http://localhost:8080/mahasiswa/$mahasiswa", $validate);

            response()->json([
                'success' => true,
                'message' => 'Kelas berhasil diperbarui',
                'data' => $request
            ], 200);
            //redirect ke halaman index dosen
            return redirect()->route('Mahasiswa.index');
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
    public function destroy($mahasiswa)
    {
        //
        Http::delete("http://localhost:8080/mahasiswa/$mahasiswa");
        return redirect()->route('Mahasiswa.index');
    }
}
