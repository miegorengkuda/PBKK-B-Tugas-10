<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PBKK — Web Routes
|--------------------------------------------------------------------------
|
| Tugas 2: Route Grouping, Parameter Validation, Named Routes.
| Tugas 4: Multi-View Profil Akademik, Blade Components, Vite.
| Tugas 5/6: Secure CRUD Academic Portal, Auth, Policies, Soft Delete, Transactions.
|
*/

// ──────────────────────────────────────────────
// Tugas 4 — Halaman utama via Controller
// ──────────────────────────────────────────────
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/profil-mahasiswa', [PageController::class, 'profile'])->name('profile');
Route::get('/ide-agent', [PageController::class, 'agent'])->name('agent');

// ──────────────────────────────────────────────
// Autentikasi Sederhana
// ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ──────────────────────────────────────────────
// Secure CRUD Academic Portal (Protected by Auth)
// PENTING: Rute statis (trash, bulk-destroy) diletakkan
// SEBELUM Route::resource agar tidak tertelan wildcard {project}.
// ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // 0. Dashboard Overview (AdminLTE)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Trash & Restore
    Route::get('/projects/trash', [ProjectController::class, 'trash'])->name('projects.trash');
    Route::patch('/projects/{id}/restore', [ProjectController::class, 'restore'])->name('projects.restore');

    // 2. Bulk Delete
    Route::delete('/projects/bulk-destroy', [ProjectController::class, 'bulkDestroy'])->name('projects.bulk-destroy');

    // 3. Resource Routes CRUD
    Route::resource('projects', ProjectController::class);
});

// ──────────────────────────────────────────────
// Tugas 2 — Route Grouping (Prefix "dashboard")
// Dipertahankan apa adanya dari tugas sebelumnya.
// ──────────────────────────────────────────────
Route::prefix('dashboard')->group(function () {

    // 1) Detail Profil Mahasiswa (NRP wajib, 10 digit angka)
    Route::get('/mahasiswa/{nrp}', function (string $nrp) {
        $mahasiswa = [
            'nama'       => 'Dafa Kumara Sahasika',
            'nrp'        => $nrp,
            'jurusan'    => 'Teknik Informatika',
            'fakultas'   => 'Fakultas Teknologi Elektro dan Informatika Cerdas',
            'kampus'     => 'Institut Teknologi Sepuluh Nopember (ITS)',
            'angkatan'   => 2024,
            'semester'   => 5,
            'email'      => '5025241112@student.its.ac.id',
            'matakuliah' => [
                'Pemrograman Berbasis Kerangka Kerja',
                'Rekayasa Kebutuhan',
                'Tata Kelola Teknologi Informasi',
                'Rekayasa Sistem Berbasis Pengetahuan',
                'Grafika Komputer',
                'Permodelan dan Simulasi',
                'Keamanan Informasi',
            ],
        ];

        return view('dashboard.mahasiswa', compact('mahasiswa'));
    })->where('nrp', '^[0-9]{10}$')->name('mahasiswa.detail');

    // 2) Ide Agentic AI (tema opsional)
    Route::get('/agent/{tema?}', function (?string $tema = null) {
        $tema = $tema ?? 'AI File Management Agent';

        $idea = [
            'deskripsi'  => 'Agent AI yang membantu pengguna mengelola file laptop dengan memindai metadata, mendeteksi file duplikat dan file lama, serta menganalisis isi dokumen dan gambar menggunakan Gemini Multimodal. Agent memilih tools yang relevan, melakukan reasoning untuk menentukan kondisi file, lalu memberikan rekomendasi Keep, Archive, atau Trash dengan persetujuan pengguna sebelum tindakan dilakukan.',
            'teknologi'  => ['Python', 'LangGraph', 'Gemini Multimodal', 'File System API', 'PyMuPDF / python-docx', 'Hashing', 'OpenCV / FFmpeg'],
            'use_case'   => 'Asisten pengelola file pribadi yang membantu mahasiswa membersihkan dan mengorganisasi penyimpanan laptop, seperti dokumen kuliah, file duplikat, screenshot, gambar, installer, dan file lama, tanpa harus memeriksa file satu per satu secara manual.',
            'arsitektur' => 'User memilih folder → Agent memindai file → mengambil metadata → memfilter kandidat → memilih tool yang relevan → menganalisis metadata, duplikat, teks, atau gambar → melakukan reasoning dengan Gemini → memberikan rekomendasi Keep / Archive / Trash → meminta persetujuan pengguna → memindahkan file ke Archive atau Recycle Bin.',
        ];

        return view('dashboard.agent', compact('tema', 'idea'));
    })->name('agent.idea');

    // 3) Kalkulator IPK (dua IP semester, menerima float 0.00 – 4.00)
    Route::get('/hitung-ipk/{ip1}/{ip2}', function (string $ip1, string $ip2) {
        $ip1 = (float) $ip1;
        $ip2 = (float) $ip2;

        // Validasi rentang 0 – 4
        if ($ip1 < 0 || $ip1 > 4 || $ip2 < 0 || $ip2 > 4) {
            abort(404, 'IP harus berada dalam rentang 0.00 – 4.00');
        }

        $total = $ip1 + $ip2;
        $rata  = $total / 2;

        // Tentukan predikat berdasarkan rata-rata
        $predikat = match (true) {
            $rata >= 3.50 => 'Cum Laude',
            $rata >= 3.00 => 'Sangat Memuaskan',
            $rata >= 2.50 => 'Memuaskan',
            $rata >= 2.00 => 'Cukup',
            default       => 'Kurang',
        };

        return view('dashboard.ipk', compact('ip1', 'ip2', 'total', 'rata', 'predikat'));
    })->where(['ip1' => '[0-3](\.[0-9]+)?|4(\.0+)?', 'ip2' => '[0-3](\.[0-9]+)?|4(\.0+)?'])
      ->name('ipk.kalkulator');

});

// ──────────────────────────────────────────────
// Fallback Route — Custom 404
// ──────────────────────────────────────────────
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
})->name('fallback');
