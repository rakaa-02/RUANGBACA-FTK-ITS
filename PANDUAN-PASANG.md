# Ruang Baca FTK — Laravel 13 (versi 3, memakai database lama)

Paket ini berisi file aplikasi saja. Nama tabel dan kolom **sama dengan database SIM lama**
(`admin`, `akun`, `buku`, `peminjaman`), jadi file `.sql` lama bisa diimpor apa adanya.

## 1. Pasang Laravel 13 (Laragon)
Terminal Laragon, PHP harus 8.3 atau lebih baru (`php -v`):
```
cd C:\laragon\www
composer create-project laravel/laravel:^13.0 ruangbaca
```

## 2. Salin paket ini
Gabungkan folder `app`, `database`, `public`, `resources`, `routes` dari zip ke `C:\laragon\www\ruangbaca\`
(pilih Replace bila ditanya). Ada 3 file bawaan yang tertimpa: `routes/web.php`,
`app/Providers/AppServiceProvider.php`, `database/seeders/DatabaseSeeder.php`.

## 3. Impor database lama
1. Buka HeidiSQL dari Laragon → buat database baru bernama `rb_db` (utf8mb4_general_ci).
2. Pilih database `rb_db` → menu File → Run SQL file → pilih file `.sql` lama.

(atau lewat terminal: `mysql -u root rb_db < D:\lokasi\rb_db.sql`)

## 4. Atur `.env`
```
APP_URL=http://ruangbaca.test
APP_LOCALE=id
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rb_db
DB_USERNAME=root
DB_PASSWORD=

# supaya database hanya berisi 4 tabel SIM (tanpa tabel sesi/cache/antrean)
SESSION_DRIVER=cookie
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

## 5. Jalankan
```
cd C:\laragon\www\ruangbaca
php artisan config:clear
php artisan rb:amankan-password
```
- Anda **tidak perlu** `php artisan migrate`: file `.sql` lama sudah berisi keempat tabel
  (`admin`, `akun`, `buku`, `peminjaman`) dan sesi login disimpan di cookie, bukan di database.
- `rb:amankan-password` mengubah password teks biasa di database lama menjadi hash
  (login juga otomatis meng-hash password lama saat pemiliknya berhasil masuk).
- Bila Anda tetap ingin menjalankan `migrate`, hapus dulu 3 file migrasi bawaan Laravel di
  `database/migrations/` (`..._create_users_table.php`, `..._create_cache_table.php`, `..._create_jobs_table.php`),
  kalau tidak tabel `users`, `cache`, `jobs` akan dibuat lagi.

Buka http://ruangbaca.test (Reload Laragon dulu). Bila tidak bisa: `php artisan serve`.

## 6. Login
- **Admin / Petugas**: pilih "Admin / Petugas", pakai username dan password dari tabel `admin` lama.
- **Mahasiswa**: pilih "Visitor", pakai Nickname dan password dari tabel `akun` lama.

## Halaman
| Peran | URL | Isi |
|---|---|---|
| Admin | `/admin/dashboard` | Kartu ringkasan (termasuk "Buku Dipinjam"), 3 grafik dengan tooltip jumlah harian |
| Admin | `/admin/buku` | Daftar buku: cari, tambah, edit, hapus, Export Excel |
| Admin | `/admin/peminjaman` | Form peminjaman + daftar terbaru, denda, tombol Kembalikan |
| Admin | `/admin/dipinjam` | **Buku dipinjam** hari ini / kemarin / 7 / 30 hari / semua / satu tanggal pilihan; filter status |
| Admin | `/admin/peminjam` | **Daftar mahasiswa yang meminjam**: sedang meminjam, terlambat, pernah meminjam; klik "Lihat detail" untuk riwayat per mahasiswa |
| Admin | `/admin/anggota` | Kelola akun mahasiswa (dibuat admin) |
| Mahasiswa | `/mahasiswa/beranda` | Buku yang sedang dipinjam (judul, tanggal pinjam, batas, terlambat/denda) dan riwayat |

Mahasiswa tidak bisa membuka halaman admin, dan sebaliknya.

## Grafik dashboard
Grafik memakai data peminjaman, karena database lama tidak punya data pengunjung:
- Statistik Peminjaman (per hari, arahkan kursor untuk jumlahnya)
- Peminjaman by Fakultas
- Buku Baru per Bulan

## Export Excel
Butuh ekstensi PHP `zip` untuk menghasilkan `.xlsx`. Di Laragon: klik kanan ikon Laragon → PHP → Extensions → centang `zip`,
lalu Reload/Restart. Bila ekstensi belum aktif, aplikasi otomatis menghasilkan berkas `.xls` yang tetap bisa dibuka di Excel
(Excel mungkin menampilkan peringatan format; pilih Yes/Buka).

## Mengganti logo
Timpa `public/img/logo.svg` dengan logo Anda (nama sama). Bila logonya PNG, ubah `logo.svg` menjadi
`logo.png` di `resources/views/auth/login.blade.php`, `auth/lupa.blade.php`, dan
`layouts/admin.blade.php`, `layouts/visitor.blade.php`.

## Aturan yang dipakai (silakan ubah)
- Denda Rp 1.000/hari terlambat: `Peminjaman::DENDA_PER_HARI` di `app/Models/Peminjaman.php`.
- Status database lama: `ya` = sudah kembali, `tidak` = belum, `terlambat` = belum dan lewat batas.
  Aplikasi menghitung "terlambat" dari tanggal, jadi tidak perlu diperbarui manual.
- Satu eksemplar buku (satu baris di tabel `buku`) tidak bisa dipinjamkan lagi sebelum dikembalikan.
- Buku yang pernah dipinjam tidak bisa dihapus. Akun yang masih punya pinjaman aktif tidak bisa dihapus;
  menghapus akun lain juga menghapus riwayat peminjamannya (aturan FK di database lama).
- Default batas kembali di form peminjaman: 14 hari.

## Struktur database: hanya 4 tabel
| Tabel | Isi |
|---|---|
| `admin` | akun petugas |
| `akun` | akun mahasiswa (dibuat admin) |
| `buku` | data buku |
| `peminjaman` | catatan pinjam dan kembali |

Tabel lain (`users`, `cache`, `jobs`, `sessions`, `books`, `members`, `loans`, `visits`, `kunjungan`, dst.)
tidak dipakai. Untuk merapikan database lokal yang sudah terlanjur ramai: ekspor cadangan, lalu jalankan
isi `database/bersihkan-tabel.sql` di phpMyAdmin/HeidiSQL (tab SQL), kemudian refresh daftar tabel (F5).

Di Wasmer: impor `rb_db.sql` saja (otomatis 4 tabel), lalu isi `SESSION_DRIVER=cookie`,
`CACHE_STORE=array`, `QUEUE_CONNECTION=sync`. Tidak perlu menjalankan `migrate` sama sekali.

## Memperbarui dari versi 2
Salin ulang isi zip (Replace). File berikut tidak dipakai lagi dan boleh dihapus:
- `app/Models/Kunjungan.php`
- `app/Http/Controllers/Admin/KunjunganController.php`
- `database/seeders/KunjunganDemoSeeder.php`
- `database/migrations/2026_10_21_000002_create_kunjungan_table.php`
- folder `resources/views/admin/pengunjung`

Tabel `kunjungan` di database tidak dipakai lagi; hapus lewat phpMyAdmin bila mau: `DROP TABLE kunjungan;`
