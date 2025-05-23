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
use App\Http\Controllers\MatkulController;

Route::get('/', [DashboardController::class, 'index'])->name('Dashboard.index');
Route::resource('Kelas', KelasController::class);
Route::resource('Matkul', MatkulController::class);
Route::resource('Mahasiswa', MahasiswaController::class);
Route::get('/export-pdf', [MatkulController::class, 'exportPdf'])->name('export.pdf');

```

## 🧑‍💻 Controller
<h3>Generate Controller</h3>

```bash
php artisan make:controller MahasiswaController / php artisan make:model Mahasiswa -mcr
php artisan make:controller MatkulController
```
file berada di `app/Http/Controllers/MahasiswaController.php`
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
        return view ('tambahmahasiswa');
    }

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


    public function destroy($mahasiswa)
    {
        //
        Http::delete("http://localhost:8080/mahasiswa/$mahasiswa");
        return redirect()->route('Mahasiswa.index');
    }
}

```

## 🧾 View (Blade)
<h3>Generate View</h3>

```bash
php artisan make:view nama_file
```
file berada di `resources/views/Mahasiswa.blade.php`

<h3>1. Contoh Mahasiswa.blade.php</h3>

```bash
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100" data-page="mahasiswa">
    <div class="flex">

        <aside class="w-64 bg-blue-700 min-h-screen text-white p-4">
            <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
            <h1 class="text-center text-4xl font-bold mb-6" style="font-family: 'Lobster', cursive;">SiMon</h1>
            <nav>
                <ul>
                    <li class="mb-4">
                        <a href="{{ route('Dashboard.index') }}" class="flex items-center space-x-2 text-white font-semibold hover:bg-blue-800 p-2 rounded">
                            🏠 Dashboard
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="{{ route('Kelas.index') }}" class="flex items-center space-x-2 text-white font-semibold hover:bg-blue-800 p-2 rounded">
                            Data Kelas
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="{{ route('Matkul.index') }}" class="flex items-center space-x-2 text-white font-semibold hover:bg-blue-800 p-2 rounded">
                            Data MataKuliah
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="{{ route('Mahasiswa.index') }}" class="flex items-center space-x-2 text-white font-semibold hover:bg-blue-800 p-2 rounded">
                            Data Mahasiswa
                        </a>
                    </li>


                </ul>
            </nav>
        </aside>


        <main class="flex-1 p-6">
            <h2 class="text-center text-4xl font-bold">.::Data Mata Mahasiswa::.</h2>
            <div class="bg-white shadow-md p-4 rounded-lg mt-4">
                <div class="flex justify-between mb-4">
                    <a href="{{route('Mahasiswa.create')}}" class="bg-blue-500 text-white px-4 py-2 rounded">+ Tambah Data</a>
                    <input type="text" id="searchInput" placeholder="Cari ..." class="border p-2 rounded w-1/3">
                </div>
                <table class="w-full mt-4 border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border p-2">No</th>
                            <th class="border p-2">Npm</th>
                            <th class="border p-2">Nama Mahasiswa</th>
                            <th class="border p-2">Email</th>
                            <th class="border p-2">Id User</th>
                            <th class="border p-2">Nama Kelas</th>
                            <th class="border p-2">Username</th>
                            <th class="border p-2">Password</th>
                            <th class="border p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="matkulTable">
                        <!--menampilkan data dosen dari BE-->
                        @foreach($mahasiswa as $index => $m)
                        <tr>
                            <td class="border p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border p-2">{{ $m['npm'] }}</td>
                            <td class="border p-2">{{ $m['nama_mahasiswa'] }}</td>
                            <td class="border p-2">{{ $m['email'] }}</td>
                            <td class="border p-2">{{ $m['id_user'] }}</td>
                            <td class="border p-2">{{ $m['nama_kelas'] }}</td>
                            <td class="border p-2">{{ $m['username'] }}</td>
                            <td class="border p-2">{{ $m['password'] }}</td>


                            <td class="border p-2 text-center flex gap-2 justify-center">

                                <a href="{{ route('Mahasiswa.edit', $m['npm']) }}" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Edit</a>

                                <form action="{{ route('Mahasiswa.destroy', $m['npm']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                                </form>
                            </td>


                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Navigasi halaman (pagination) -->
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
        // Fungsi pagination
        let currentPage = 1;
        const rowsPerPage = 10;
        const table = document.getElementById("dosenTable");
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

        // Navigasi halaman sebelumnya dan selanjutnya
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

        // Fungsi dropdown menu otomatis terbuka jika halaman cocok
        document.addEventListener("DOMContentLoaded", function() {
            let currentPage = document.body.getAttribute("data-page");
            let dropdownMenu = document.getElementById("dropdown-menu");
            let dropdownBtn = document.getElementById("dropdown-btn");
            let arrow = document.getElementById("arrow");
            let activeLink = document.querySelector(`a[href='${currentPage}']`);

            let pages = ["penilaian", "dosen", "mahasiswa", "matakuliah", "prodi", "kelas"];

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

        let deleteElement = null;

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

        // Fungsi pencarian dosen
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#dosenTable tr");

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

## Export PDF
- `composer require barryvdh/laravel-dompdf `
-  buat view cetak pada `resources/views/pdf/cetak.blade.php`

```bash
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Hasil Studi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #000;
        }

        .header img {
            max-width: 100px;
            margin-bottom: 10px;
        }

        .header h3 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #333;
        }

        .content {
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <!-- <img src="https://pnc.ac.id/wp-content/uploads/2023/01/logo-pnc.png" alt="Logo PNC" onerror="this.src='https://via.placeholder.com/100x100?text=PNC+Logo';"> -->
        <h3>KEMENTERIAN PENDIDIKAN, TINGGI, SAINS, DAN TEKNOLOGI</h3>
        <h3>POLITEKNIK NEGERI CILACAP</h3>
        <p>Jalan Dr. Soetomo No. 1, Sidakaya - Cilacap 53212 Jawa Tengah</p>
        <p>Telepon: (0282) 533329, Fax: (0282) 537992</p>
        <p>www.pnc.ac.id, Email: sekretariat@pnc.ac.id</p>
    </div>

    <!-- Konten Utama -->
    <div class="content">
        <h2 style="text-align: center;">Data Matkul</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Matkul</th>
                    <th>Nama Matkul</th>
                    <th>SKS</th>

                </tr>
            </thead>
            <tbody>
                @foreach($matkul as $index => $m)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $m['kode_matkul'] }}</td>
                    <td>{{ $m['nama_matkul'] }}</td>
                    <td>{{ $m['sks'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <!-- Footer (opsional) -->

</body>

</html>
```

- penambahan function di MatkulController

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
> karena tombol submit berada pada Matkul.blade.php maka function exportPDFnya ditaruh MatkulController



