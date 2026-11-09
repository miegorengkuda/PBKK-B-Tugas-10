# PBKK — Secure CRUD Academic Portal

Aplikasi web portal akademik berbasis Laravel 13 dan Tailwind CSS v4. Proyek ini merupakan pengembangan berkelanjutan dari tugas sebelumnya (Tugas 2 dan Tugas 4), kini diperluas dengan fitur **Secure CRUD Academic Portal** yang aman, modular, dan teruji.

---

## Fitur Utama: Secure CRUD Academic Portal

### 1. Autentikasi Pengguna & Relasi Database
- Menggunakan tabel standar `users` yang berelasi dengan tabel `projects` melalui Foreign Key (`user_id` &rarr; `users.id`, `onDelete('cascade')`).
- Relasi Eloquent:
  - `User hasMany Project`
  - `Project belongsTo User`
- Dilengkapi sistem autentikasi sederhana (Login, Register, Logout) dengan password hashing (`Hash::make`), validasi ketat, dan proteksi middleware `auth`.
- **Akun Demo Bawaan**:
  - **Email**: `demo@example.com`
  - **Password**: `password123`

### 2. Resource Controller & Rute Terstruktur
- Menggunakan `Route::resource('projects', ProjectController::class)`.
- Rute statis seperti `/projects/trash` dan `/projects/bulk-destroy` didefinisikan **sebelum** resource route untuk mencegah konflik dengan parameter wildcard `{project}`.
- Total 10 action controller: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `trash`, `restore`, dan `bulkDestroy`.

### 3. Validasi Form Request Terisolasi
Seluruh validasi input dipisahkan ke dalam kelas Form Request mandiri (tidak ada validasi manual di controller):
- `StoreProjectRequest`: Memvalidasi `title`, `status` (`draft`, `active`, `archived`), `category`, `tech_stack`, `started_at`, dan `description`.
- `UpdateProjectRequest`: Memvalidasi input pembaruan serta mengecek otorisasi policy kepemilikan proyek.
- `BulkDestroyProjectRequest`: Memvalidasi array `ids` dan keberadaan ID proyek di database.

### 4. Otorisasi Kepemilikan & Anti-Spoofing (Security)
- `ProjectPolicy` diterapkan secara eksplisit di seluruh endpoint aksi (`view`, `update`, `delete`, `restore`).
- Akses atau modifikasi proyek milik user lain secara otomatis menghasilkan respon **HTTP 403 Forbidden**.
- **Anti-Spoofing `user_id`**: Input `user_id` dari form diabaikan; server selalu mengasosiasikan proyek baru dengan user yang sedang terautentikasi (`$request->user()->projects()->create(...)`).

### 5. Soft Deletes & Pemulihan (Restore)
- Menggunakan trait `SoftDeletes` pada model `Project`.
- Proyek yang dihapus dipindahkan ke Kotak Sampah (`/projects/trash`) tanpa menghapus data secara permanen (`deleted_at` terisi).
- Fitur **Restore per proyek** (`PATCH /projects/{id}/restore`) dibatasi ketat melalui query `$request->user()->projects()->onlyTrashed()->findOrFail($id)` sehingga user tidak dapat memulihkan proyek milik akun lain.

### 6. Aksi Massal (Bulk Delete via Checkbox)
- Checkbox pada setiap baris tabel proyek dan checkbox **Select All**.
- Tombol **Hapus Terpilih** dengan konfirmasi interaktif.
- Menghapus banyak data sekaligus dalam satu request yang aman.

### 7. Database Transactions (Atomicity)
- Operasi penulisan kritis dibungkus menggunakan `DB::transaction()`:
  - `store()`: Pembuatan proyek baru.
  - `update()`: Pembaruan data proyek.
  - `bulkDestroy()`: Penghapusan massal proyek untuk menjamin semua record terhapus bersamaan atau dibatalkan jika terjadi error.

### 8. User Interface, AdminLTE Dashboard & Flash Notifications
- **Dashboard AdminLTE**: Menggunakan package `jeroennoten/laravel-adminlte` dengan template layout `@extends('adminlte::page')` yang responsif, terintegrasi sidebar, navbar, usermenu dropdown, darkmode widget, dan content wrapper.
- **Tipografi Konsisten (Inter Font)**: Menggunakan font **Inter** sebagai font utama yang diterapkan secara konsisten pada seluruh elemen AdminLTE (sidebar, navbar, dashboard widgets, tabel, form, button, dan modal) dengan bobot font proporsional (400 regular, 500 medium, 600 semi-bold) tanpa merusak struktur CSS asli AdminLTE.
- **Statistik Dinamis Dashboard (`/dashboard`)**: Menampilkan 4 widget statistik aktual dari database (Total Users, Total Projects, Active Projects, Draft/Archived Projects), serta tabel proyek terbaru dan ringkasan profil.
- **Integrasi Penuh CRUD Project**: Halaman index, create, edit, show, dan trash Proyek Akademik diintegrasikan langsung ke layout AdminLTE dengan komponen tabel, badge, alert, dan modal Bootstrap 5.
- Tampilan public tetap memiliki dukungan tema **Light & Dark Mode** berbasis class Tailwind CSS v4 dengan flash alert.

---

## Fitur Tugas Sebelumnya yang Tetap Dipertahankan

Seluruh fitur dari Tugas 2 dan Tugas 4 tetap berfungsi penuh:
- `/` &rarr; Beranda Akademik (`PageController@home`)
- `/profil-mahasiswa` &rarr; Profil Mahasiswa & Interaksi Status Alert (`PageController@profile`)
- `/ide-agent` &rarr; Proposal Ide Agentic AI: AI Gmail Inbox Manager (`PageController@agent`)
- `/dashboard/mahasiswa/{nrp}` &rarr; Detail Mahasiswa dengan validasi regex NRP 10 digit
- `/dashboard/agent/{tema?}` &rarr; Ide Agentic AI versi route grouping
- `/dashboard/hitung-ipk/{ip1}/{ip2}` &rarr; Kalkulator IPK dua semester
- Fallback Route &rarr; Custom 404 Error Page

---

## Panduan Instalasi & Menjalankan Proyek

1. **Clone repositori dan install dependency PHP**:
   ```bash
   composer install
   ```

2. **Setup file lingkungan `.env`**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Jalankan Migration & Seeder (Idempotent)**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
   *Catatan: Seeder aman dijalankan berulang kali tanpa menggandakan data akun demo atau sampel proyek.*

4. **Install dependency NPM & Build Asset**:
   ```bash
   npm install
   npm run build
   # Atau untuk live reload development:
   npm run dev
   ```

5. **Jalankan Web Server**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui `http://localhost:8000`.

---

## Pengujian Otomatis (Automated Testing)

Proyek ini dilengkapi dengan **Feature Test** menyeluruh menggunakan `RefreshDatabase` pada database SQLite in-memory:

```bash
php artisan test
```

### Cakupan Pengujian (`tests/Feature/DashboardTest.php` & `tests/Feature/ProjectCrudTest.php`):
1. `test_guest_cannot_access_dashboard_and_is_redirected_to_login`
2. `test_authenticated_user_can_access_dashboard_and_see_statistics`
3. `test_dashboard_displays_correct_database_aggregates`
4. `test_dashboard_displays_user_recent_projects`
5. `test_guest_is_redirected_to_login_when_accessing_projects`
6. `test_user_can_view_only_their_own_projects_on_index`
7. `test_user_can_create_project_with_valid_data`
8. `test_create_project_validation_via_store_project_request`
9. `test_user_can_view_own_project_detail`
10. `test_user_cannot_view_other_users_project_returns_403`
11. `test_user_can_update_own_project`
12. `test_user_cannot_update_other_users_project_returns_403`
13. `test_user_can_soft_delete_own_project`
14. `test_user_cannot_delete_other_users_project_returns_403`
15. `test_user_can_restore_own_trashed_project`
16. `test_user_cannot_restore_other_users_trashed_project`
17. `test_user_can_bulk_delete_multiple_projects_via_bulk_request`
18. `test_bulk_delete_validates_ids_array_via_form_request`
19. `test_bulk_delete_cannot_delete_other_users_projects`

Semua 21 test (68 assertions) lulus 100%.
