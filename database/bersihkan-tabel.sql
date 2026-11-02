-- Merapikan database ruangbaca: sisakan HANYA admin, akun, buku, peminjaman.
-- Jalankan di database lokal "ruangbaca" SETELAH ekspor cadangan.
-- Pastikan 4 tabel inti berisi data Anda; skrip ini tidak menyentuh keempatnya.

SET FOREIGN_KEY_CHECKS = 0;

-- sisa versi pertama & kedua aplikasi
DROP TABLE IF EXISTS loans, visits, members, books, kunjungan;

-- tabel bawaan Laravel yang tidak dipakai SIM ini
DROP TABLE IF EXISTS users, password_reset_tokens, cache, cache_locks, jobs, job_batches, failed_jobs;

-- sesi disimpan di cookie (SESSION_DRIVER=cookie), catatan migrasi tidak diperlukan
DROP TABLE IF EXISTS sessions, migrations;

SET FOREIGN_KEY_CHECKS = 1;

-- hasil akhir: admin, akun, buku, peminjaman
SHOW TABLES;
