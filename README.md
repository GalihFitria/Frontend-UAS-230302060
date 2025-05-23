# 🎓 Frontend - Sistem Monitoring Kehadiran Mahasiswa
```bash
Nama : Galih Fitria Fijar Rofiqoh
NPM : 230302060
Kelas : TI 2C
```

Proyek ini merupakan implementasi frontend Laravel dengan Tailwind CSS yang berkomunikasi dengan backend REST API (dari CodeIgniter).

🔗 [SI-KRS Backend (GitHub)](https://github.com/NalindraDT/Simon-kehadiran)

🔗 [SI-KRS Database (GitHub)](https://github.com/JiRizkyCahyusna/DBE_Simon)

## 🧱 Teknologi yang Digunakan
- Laravel 10
- Tailwind CSS
- Laravel HTTP Client (untuk konsumsi API)
- REST API Backend (misal: CodeIgniter 4)
- Vite (build frontend asset Laravel)

## DATABASE
<h3>import database</h3>

🔗 [SI-KRS Database (GitHub)](https://github.com/JiRizkyCahyusna/DBE_Simon)

## 📦 BACKEND
<h3>1. Clone Repository BE</h3>

```bash
git clone https://github.com/NalindraDT/Simon-kehadiran.git
cd Simon-kehadiran
```

<h3>2. Install Dependency CodeIgniter</h3>

```bash
composer install
```
<h3>3. Copy File Environment</h3>

```bash
cp .env.example .env
```

<h3>4. Menjalankan CodeIgniter</h3>

```bash
php spark serve
```

<h3>5. Cek EndPoint menggunakan Postman</h3>
<h3>Matkul :</h3>

- GET matkul : http://localhost:8080/matkul
- POST matkul : http://localhost:8080/matkul
- PUT matkul : http://localhost:8080/matkul/{kode_matkul}
- DELETE matkul : http://localhost:8080/matkul/{kode_matkul}

<h3>Mahasiswa</h3>

- GET mahasiswa : http://localhost:8080/mahasiswa
- POST mahasiswa : http://localhost:8080/mahasiswa
- PUT mahasiswa : http://localhost:8080/mahasiswa/{npm}
- DELETE mahasiswa : http://localhost:8080/mahasiswa/{npm}

## 🎨 FRONTEND
<h3>1. Clone Repository FE</h3>
Jika ingin langsung menggukan folder ini

```bash
git clone https://github.com/GalihFitria/Frontend-UAS-230302060.git
cd Frontend-UAS-230302060
```

<h3>2. Install Laravel </h3>
<h3>Melalui Terminal/CMD</h3>

```
composer create-priject laravel/laravel (nama-projek)
```

<h3>Laragon</h3>

- Buka Laragon
- Klik kanan Quick app
- Laravel

<h3>3. Install Dependency Laravel</h3>

```bash
composer install
```

<h3>4. Copy File Environment</h3>

```bash
cp .env.example .env
```
<h3>5. Set .env untuk Non-Database App</h3>

```bash
APP_NAME=Laravel
APP_URL=http://localhost:8000
SESSION_DRIVER=file
```

> Tidak perlu konfigurasi DB karena semua data berasal dari API CodeIgniter.

<h3>6. Menjalankan Laravel</h3>

```bash
php artisan serve
```

## 📁 Routing 
Di routes/web.php :

```bash
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProdiController;

Route::get('/', [DashboardController::class, 'index'])->name('Dashboard.index');
Route::resource('Mahasiswa', MahasiswaController::class);
Route::resource('Prodi', ProdiController::class);
Route::resource('Kelas', KelasController::class);
Route::get('/export-pdf', [ProdiController::class, 'exportPdf'])->name('export.pdf');
```

## 🧑‍💻 Controller
<h3>Generate Controller</h3>

```bash
php artisan make:controller MahasiswaController
php artisan make:controller KelasController
php artisan make:controller ProdiController
```
<h3>Contoh MahasiswaController.php</h3>

```bash
<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class MahasiswaController extends Controller
{
    
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

    
    public function create()
    {
        //
        $respon_kelas = Http::get('http://localhost:8080/kelas');
        $kelas = collect($respon_kelas->json())->sortBy('id_kelas')->values();

        $respon_prodi = Http::get('http://localhost:8080/prodi');
        $prodi = collect($respon_prodi->json())->sortBy('kode_prodi')->values();

        return view('tambahmahasiswa', [
            'kelas' => $kelas,
            'prodi' => $prodi
        ]);
    }

   
    public function store(Request $request)
    {
        //
        try {
            $validate = $request->validate([
                'npm' => 'required|unique:mahasiswa,npm',
                'nama_mahasiswa' => 'required',
                'id_kelas' => 'required',
                'kode_prodi' => 'required'
            ]);

            Http::asForm()->post('http://localhost:8080/mahasiswa', $validate);

            return redirect()->route('Mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($mahasiswa)
    {
        //
        $mahasiswaResponse = Http::get("http://localhost:8080/mahasiswa/$mahasiswa");
        $kelas = Http::get("http://localhost:8080/kelas")->json();
        $prodi = Http::get("http://localhost:8080/prodi")->json();

        if ($mahasiswaResponse->successful() && !empty($mahasiswaResponse[0])) {
            $mahasiswa = $mahasiswaResponse[0];

            // Tambahkan pencocokan manual ID berdasarkan nama
            foreach ($kelas as $k) {
                if ($k['nama_kelas'] === $mahasiswa['nama_kelas']) {
                    $mahasiswa['id_kelas'] = $k['id_kelas'];
                    break;
                }
            }

            foreach ($prodi as $p) {
                if ($p['nama_prodi'] === $mahasiswa['nama_prodi']) {
                    $mahasiswa['kode_prodi'] = $p['kode_prodi'];
                    break;
                }
            }

            return view('editmahasiswa', compact('mahasiswa', 'kelas', 'prodi'));
        } else {
            return back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }
    }


    public function update(Request $request, $mahasiswa)
    {
        //
        try {
            $validate = $request->validate([
                'npm' => 'required',
                'nama_mahasiswa' => 'required',
                'id_kelas' => 'required',
                'kode_prodi' => 'required'

            ]);

            Http::asForm()->put("http://localhost:8080/mahasiswa/$mahasiswa", $validate);

            return redirect()->route('Mahasiswa.index')->with('success', 'Mahasiswa berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }


    public function destroy($mahasiswa)
    {
        //
        Http::delete("http://localhost:8080/mahasiswa/$mahasiswa");
        return redirect()->route('Mahasiswa.index');
    }
}
```

<h3>Contoh KelasController.php</h3>

```bash
<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KelasController extends Controller
{
    
    public function index()
    {
        //
        $response = Http::get('http://localhost:8080/kelas');

        if ($response->successful()) {
            $kelas = collect($response->json())->sortBy('id_kelas')->values();
            return view('Kelas', compact('kelas'));
        } else {
            return back()->with('error', 'Gagal mengambil data kelas');
        }
    }

  
    public function create()
    {
        //
        return view('tambahkelas');
    }

    
    public function store(Request $request)
    {
        //
        try {
            $validate = $request->validate([
                'id_kelas' => 'required',
                'nama_kelas' => 'required',
            ]);

            Http::asForm()->post('http://localhost:8080/kelas', $validate);

            return redirect()->route('Kelas.index')->with('success', 'Kelas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id_kelas)
    {
        //

        $response = Http::get("http://localhost:8080/kelas/$id_kelas");

        if ($response->successful() && !empty($response[0])) {
            $kelas = $response[0]; // karena CodeIgniter mengembalikan array berisi 1 data
            return view('editkelas', compact('kelas'));
        } else {
            return back()->with('error', 'Gagal mengambil data kelas');
        }

    }

  
    public function update(Request $request, $kelas)
    {
        //
        try {
            $validate = $request->validate([
                'id_kelas' => 'required',
                'nama_kelas' => 'required'
            ]);

            Http::asForm()->put("http://localhost:8080/kelas/$kelas", $validate);

            return redirect()->route('Kelas.index')->with('success', 'Kelas berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

  
    public function destroy($kelas)
    {
        //
        Http::delete("http://localhost:8080/kelas/$kelas");
        return redirect()->route('Kelas.index');
    }
}
```

<h3>Contoh ProdiController.php</h3>

```bash
<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProdiController extends Controller
{
    
    public function index()
    {
        //
        $response = Http::get('http://localhost:8080/prodi');

        if ($response->successful()) {
            $prodi = collect($response->json())->sortBy('kode_prodi')->values();
            return view('Prodi', compact('prodi'));
        } else {
            return back()->with('error', 'Gagal mengambil data prodi');
        }
    }

    
    public function create()
    {
        //
        return view('tambahprodi');
    }

    
    public function store(Request $request)
    {
        //
        try {
            $validate = $request->validate([
                'kode_prodi' => 'required',
                'nama_prodi' => 'required'
            ]);

            Http::asForm()->post('http://localhost:8080/prodi', $validate);

            return redirect()->route('Prodi.index')->with('success', 'Prodi berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function edit($prodi)
    {
        //
        $response = Http::get("http://localhost:8080/prodi/$prodi");

        if ($response->successful() && !empty($response[0])) {
            $prodi = $response[0]; // karena CodeIgniter mengembalikan array berisi 1 data
            return view('editprodi', compact('prodi'));
        } else {
            return back()->with('error', 'Gagal mengambil data kelas');
        }

    }


    public function update(Request $request, $prodi)
    {
        //
        try {
            $validate = $request->validate([
                'kode_prodi' => 'required',
                'nama_prodi' => 'required'
            ]);

            Http::asForm()->put("http://localhost:8080/prodi/$prodi", $validate);

            return redirect()->route('Prodi.index')->with('success', 'Kelas berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }


    public function destroy($prodi)
    {
        
        Http::delete("http://localhost:8080/prodi/$prodi");
        return redirect()->route('Prodi.index');
    }
}
```

## 🧾 View (Blade)
<h3>Generate View</h3>

```bash
php artisan make:view Prodi
```

<h3>1. resources/views/Prodi.blade.php</h3>

```bash
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Prodi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100" data-page="dataprodi">
    <div class="flex">

        <aside class="w-64 bg-blue-700 min-h-screen text-white p-4">
            <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
            <h1 class="text-center text-4xl font-bold mb-6" style="font-family: 'Lobster', cursive;">KRS</h1>
            <nav>
                <ul>
                    <li class="mb-4">
                        <a href="{{ route('Dashboard.index') }}" class="flex items-center space-x-2 text-white font-semibold hover:bg-blue-800 p-2 rounded">
                            🏠 Dashboard
                        </a>
                    </li>
                    <li class="mb-4 relative">
                        <button id="dropdownButton" class="w-full flex items-center justify-between text-white font-semibold hover:bg-blue-800 p-2 rounded">
                            📊 Pengolahan Data
                            <span id="arrow">▼</span>
                        </button>
                        <ul id="dropdown" class="hidden bg-blue-600 mt-2 rounded-lg">
                            <li>
                                <a href="{{route('Mahasiswa.index')}}" class="block px-4 py-2 hover:bg-blue-700"> Data Mahasiswa</a>
                            </li>
                            <li>
                                <a href="{{route('Prodi.index')}}" class="block px-4 py-2 hover:bg-blue-700"> Data Prodi</a>
                            </li>
                            <li>
                                <a href="{{route('Kelas.index')}}" class="block px-4 py-2 hover:bg-blue-700"> Data Kelas</a>
                            </li>
                    </li>


                </ul>
            </nav>
        </aside>


        <main class="flex-1 p-6">
            <h2 class="text-center text-4xl font-bold">.::Data Prodi::.</h2>
            <div class="bg-white shadow-md p-4 rounded-lg mt-4">
                <div class="flex justify-between mb-4">
                    <a href="{{route('Prodi.create')}}" class="bg-blue-500 text-white px-4 py-2 rounded">+ Tambah Data</a>
                    <input type="text" id="searchInput" placeholder="Cari Program Studi..." class="border p-2 rounded w-1/3">
                </div>
                <table class="w-full mt-4 border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border p-2">No.</th>
                            <th class="border p-2">Kode Prodi</th>
                            <th class="border p-2">Nama Prodi</th>
                            <th class="border p-2">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="prodiTable">
                        @foreach($prodi as $index => $p)
                        <tr>
                            <td class="border p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border p-2">{{ $p['kode_prodi'] }}</td>
                            <td class="border p-2">{{ $p['nama_prodi'] }}</td>

                            <td class="border p-2 text-center flex gap-2 justify-center">
                                <a href="{{ route('Prodi.edit', $p['kode_prodi']) }}" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Edit</a>

                                <form action="{{ route('Prodi.destroy', $p['kode_prodi']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                                </form>
                            </td>


                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex justify-between items-center mt-4">
                    <button id="prevPage" class="bg-gray-400 text-white px-3 py-1 rounded hover:bg-gray-500">Previous</button>
                    <span id="pageInfo" class="text-gray-700">Page 1</span>
                    <button id="nextPage" class="bg-gray-400 text-white px-3 py-1 rounded hover:bg-gray-500">Next</button>
                </div>
            </div>
        </main>
    </div>

    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
            <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <div class="mt-4 flex justify-center space-x-4">
                <button onclick="deleteData()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Ya, Hapus</button>
                <button onclick="closeDeleteModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const button = document.getElementById("dropdownButton");
            const dropdown = document.getElementById("dropdown");
            const arrow = document.getElementById("arrow");

            button.addEventListener("click", function() {
                dropdown.classList.toggle("hidden");
                arrow.textContent = dropdown.classList.contains("hidden") ? "▼" : "▲";
            });
        });

        let currentPage = 1;
        const rowsPerPage = 10;
        const table = document.getElementById("prodiTable");
        const rows = table.getElementsByTagName("tr");
        const totalPages = Math.ceil(rows.length / rowsPerPage);

        function showPage(page) {
            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = "none";
            }
            let start = (page - 1) * rowsPerPage;
            let end = start + rowsPerPage;
            for (let i = start; i < end && i < rows.length; i++) {
                rows[i].style.display = "";
            }
            document.getElementById("pageInfo").textContent = `Page ${page} of ${totalPages}`;
        }

        document.getElementById("prevPage").addEventListener("click", function() {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        document.getElementById("nextPage").addEventListener("click", function() {
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });

        showPage(currentPage);

        document.addEventListener("DOMContentLoaded", function() {
            let currentPage = document.body.getAttribute("data-page");
            let dropdownMenu = document.getElementById("dropdown-menu");
            let dropdownBtn = document.getElementById("dropdown-btn");
            let arrow = document.getElementById("arrow");
            let activeLink = document.querySelector(`a[href='${currentPage}']`);

            let pages = ["penilaian", "datadosen", "datamahasiswa", "matakuliah", "dataprodi", "datakelas"];

            if (pages.includes(currentPage)) {
                dropdownMenu.classList.remove("hidden");
                arrow.innerHTML = "▲";
            }

            if (activeLink) {
                activeLink.classList.add("bg-blue-800", "text-white");
            }

            dropdownBtn.addEventListener("click", function() {
                if (dropdownMenu.classList.contains("hidden")) {
                    dropdownMenu.classList.remove("hidden");
                    arrow.innerHTML = "▲";
                } else {
                    dropdownMenu.classList.add("hidden");
                    arrow.innerHTML = "▼";
                }
            });
        });



        function openDeleteModal(event, element) {
            event.preventDefault();
            deleteElement = element.closest("tr");
            document.getElementById("deleteModal").classList.remove("hidden");
        }

        function closeDeleteModal() {
            document.getElementById("deleteModal").classList.add("hidden");
            deleteElement = null;
        }

        function deleteData() {
            if (deleteElement) {
                deleteElement.remove();
                deleteElement = null;
            }
            closeDeleteModal();
        }

        //seacrh
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#prodiTable tr");

            rows.forEach(row => {
                let namaDosen = row.cells[2].textContent.toLowerCase();
                if (namaDosen.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    </script>
</body>

</html>
```

<h3>2. resources/views/tambahprodi.blade.php</h3>

```bash
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Prodi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center h-screen bg-blue-100">


    <div id="formModal" class="flex items-center justify-center bg-gray-800 bg-opacity-50 fixed inset-0">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4 text-center">Tambah Data Prodi</h2>
            <form action="{{route('Prodi.store')}}" method="post">
                @csrf

                <label class="block">Kode Prodi</label>
                <input type="text" id="kode_prodiinput" class="border w-full p-2 mb-2 rounded" name="kode_prodi">

                <label class="block">Nama Prodi</label>
                <input type="text" id="nama_prodiInput" class="border w-full p-2 mb-2 rounded" name="nama_prodi">

                <div class="flex justify-center space-x-4 mt-4">
                    <a href="{{route('Prodi.index')}}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition duration-200">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200 active:scale-95">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
            <div class="flex justify-center items-center mb-4">
                <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-blue-500"></div>
            </div>
            <h2 class="text-lg font-bold mb-4">Data berhasil ditambahkan!</h2>
            <button onclick="redirectToDataDosen()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                OK
            </button>
        </div>
    </div>

    <script>
        function closeModal() {
            document.getElementById("formModal").classList.add("hidden");
        }

        function showSuccessModal() {
            document.getElementById("successModal").classList.remove("hidden");
            setTimeout(() => {
                document.querySelector(".animate-spin").classList.add("hidden");
            }, 1000);
        }

        function redirectToDataDosen() {
            window.location.href = "Prodi";
        }
    </script>

</body>

</html>
```

<h3>3. resources/views/editprodi.blade.php</h3>

```bash
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Prodi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="flex items-center justify-center h-screen bg-blue-100">

        <div id="formModal" class="flex items-center justify-center bg-gray-800 bg-opacity-50 fixed inset-0">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-xl font-bold mb-4 text-center">Edit Prodi</h2>
                <form action="{{ route('Prodi.update', $prodi['kode_prodi']) }}" method="post">
                    @csrf
                    @method('PUT')
                    <label class="block">Kode Prodi</label>
                    <input type="text" name="kode_prodi" value="{{$prodi['kode_prodi']}}" id="editProdi" class="border w-full p-2 mb-2 rounded">

                    <label class="block">Nama Prodi</label>
                    <input type="text" name="nama_prodi" value="{{$prodi['nama_prodi']}}" id="editNamaProdi" class="border w-full p-2 mb-2 rounded">


                    <div class="flex justify-center space-x-6 mt-4">
                        <a href="{{route('Prodi.index')}}" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition duration-200 shadow-md">
                            Batal
                        </a>
                        <button onclick="openConfirmModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 shadow-md active:scale-95">
                            Ubah Data
                        </button>
                </form>
            </div>

        </div>
    </div>
    </div>


    <div id="confirmModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
            <h2 class="text-lg font-bold mb-4">Konfirmasi Perubahan</h2>
            <p>Apakah Anda yakin ingin mengubah data ini?</p>
            <div class="mt-4 flex justify-center space-x-4">
                <button onclick="showSuccessModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ya, Ubah</button>
                <button onclick="closeConfirmModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</button>
            </div>
        </div>
    </div>


    <div id="successModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
            <div class="flex justify-center items-center mb-4">
                <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-blue-500"></div>
            </div>
            <h2 class="text-lg font-bold mb-4">Data berhasil diubah!</h2>
            <a href="dataprodi" onclick="closeSuccessModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">OK<a>
        </div>
    </div>

    <script>
        function openConfirmModal() {
            document.getElementById("confirmModal").classList.remove("hidden");
        }

        function closeConfirmModal() {
            document.getElementById("confirmModal").classList.add("hidden");
        }

        function showSuccessModal() {
            closeConfirmModal();
            document.getElementById("successModal").classList.remove("hidden");
            setTimeout(() => {
                document.querySelector(".animate-spin").classList.add("hidden");
            }, 1000);
        }

        function closeSuccessModal() {
            document.getElementById("successModal").classList.add("hidden");
        }
    </script>
</body>

</html>
```

## Export PDF
- `composer require barryvdh/laravel-dompdf `
- buat view cetak
- penambahan function di ProdiController

```bash
public function exportPdf()
    {
        $response = Http::get('http://localhost:8080/prodi');
        if ($response->successful()) {
            $prodi = collect($response->json());
            $pdf = Pdf::loadView('pdf.cetak', compact('prodi')); 
            return $pdf->download('prodi.pdf');
        } else {
            return back()->with('error', 'Gagal mengambil data untuk PDF');
        }
    }
```
> karena tombol submit berada pada Prodi.blade.php maka function exportPDFnya ditaruh Prodi Controller



