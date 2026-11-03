# NETLEARN TKJ — Laravel

E-modul interaktif berbahasa Indonesia untuk kelas XI TKJ, dibuat sebagai aplikasi Laravel 13 dengan halaman Blade dan JavaScript/CSS lokal.

## Jalankan di Windows / XAMPP

1. Ekstrak folder proyek dan nyalakan **MySQL** dari XAMPP.
2. Buat database `netlearn_tkj` melalui phpMyAdmin dengan mengimpor `database/create_database.sql` (atau jalankan SQL di file tersebut).
3. Buka terminal di folder proyek. Pasang paket dan siapkan konfigurasi:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

4. Jika akun MySQL berbeda dari bawaan XAMPP, sesuaikan `DB_USERNAME` dan `DB_PASSWORD` di `.env` sebelum menjalankan migrasi.
5. Buka alamat yang ditampilkan Artisan, biasanya `http://127.0.0.1:8000`.

PHP 8.3 atau lebih baru dan Composer diperlukan. Proyek menggunakan Laravel 13 dan MySQL. Tidak perlu menjalankan `npm`.

## Isi

- Dashboard, petunjuk, capaian pembelajaran, peta konsep, rangkuman, glosarium, dan tentang pengembang.
- Dua unit dan 13 submateri dengan materi lengkap serta diagram SVG edukatif yang dibuat di browser.
- Aktivitas identifikasi topologi, mencocokkan konsep, konversi biner, dan latihan subnet.
- Pretest/posttest dengan skor yang dihitung server, penjelasan, riwayat hasil, dan tautan remediasi.
- Progres materi, aktivitas, posisi belajar terakhir, dan hasil evaluasi tersimpan di MySQL.
- Browser menyimpan cache lokal untuk tetap bisa dipakai saat database tidak tersambung; perubahan akan disinkronkan saat tersambung kembali.

Database mencatat ID acak per browser, progres, serta riwayat kuis. Prototipe ini belum meminta nama, email, atau akun peserta.

## Struktur Laravel

- `routes/web.php` — rute halaman.
- `app/Http/Controllers/LearningController.php` — controller modul.
- `app/Support/LearningContent.php` — materi, soal, pasangan istilah, dan glosarium.
- `app/Models/` dan `database/migrations/` — data peserta anonim, progres, dan riwayat evaluasi.
- `app/Http/Controllers/Api/` dan `routes/api.php` — endpoint sinkronisasi ke MySQL.
- `resources/views/` — halaman Blade.
- `public/css/app.css`, `public/js/app.js` — tampilan responsif dan interaksi.

Proyek ini adalah prototipe R&D dengan alur ADDIE. Halaman pengembang tidak menyatakan bahwa validasi ahli atau uji lapangan telah dilakukan.
