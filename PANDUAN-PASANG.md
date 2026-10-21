# Ruang Baca FTK — Laravel 13 (versi 2, memakai database lama)

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
```

## 5. Jalankan
```
cd C:\laragon\www\ruangbaca
php artisan migrate
php artisan rb:amankan-password
```
- `migrate` membuat tabel baru `kunjungan` (untuk statistik pengunjung) dan tabel sesi Laravel.
  Tabel lama yang sudah Anda impor **tidak disentuh**.
- `rb:amankan-password` mengubah password teks biasa di database lama menjadi hash.
  Password admin di database lama tersimpan sebagai teks biasa, jadi sebaiknya dijalankan sekali.
  (Login juga otomatis meng-hash password lama saat pemiliknya berhasil masuk.)

Buka http://ruangbaca.test (Reload Laragon dulu). Bila tidak bisa: `php artisan serve`.

## 6. Login
- **Admin / Petugas**: pilih "Admin / Petugas", pakai username dan password dari tabel `admin` lama.
- **Mahasiswa**: pilih "Visitor", pakai Nickname dan password dari tabel `akun` lama.
  Login mahasiswa otomatis tercatat sebagai kunjungan hari itu (sekali per hari).

## Halaman
| Peran | URL | Isi |
|---|---|---|
| Admin | `/admin/dashboard` | Kartu ringkasan, kartu sirkulasi, 3 grafik (tooltip jumlah harian) |
| Admin | `/admin/buku` | Daftar buku: cari, tambah, edit, hapus, Export Excel |
| Admin | `/admin/peminjaman` | Form peminjaman + daftar terbaru, denda, tombol Kembalikan |
| Admin | `/admin/dipinjam` | **Buku dipinjam** hari ini / kemarin / 7 / 30 hari / semua / rentang tanggal; filter status |
| Admin | `/admin/anggota` | Kelola akun mahasiswa (dibuat admin) |
| Admin | `/admin/pengunjung` | Tabel kunjungan, filter tanggal |
| Mahasiswa | `/mahasiswa/beranda` | Buku yang sedang dipinjam (judul, tanggal pinjam, batas, terlambat/denda) dan riwayat |

Mahasiswa tidak bisa membuka halaman admin, dan sebaliknya.

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

## Data contoh grafik (opsional)
Database lama tidak punya data kunjungan, jadi grafik kosong sampai mahasiswa login.
Untuk mencoba tampilan grafik: `php artisan db:seed --class=KunjunganDemoSeeder`
Hapus lagi dengan `DELETE FROM kunjungan;` di HeidiSQL.
