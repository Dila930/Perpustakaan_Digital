# Perpustakaan_Digital\
Website Perpustakaan Digital berbasis PHP dan MySQL yang dirancang untuk memudahkan pengelolaan dan akses buku secara digital. Sistem ini mendukung peran admin dan pengguna dengan fitur manajemen data buku serta pencatatan riwayat transaksi.

## Deskripsi Sistem
Perpustakaan Digital merupakan sistem informasi berbasis web yang memungkinkan pengguna untuk:
1. Melakukan registrasi dan login
2. Melihat daftar buku yang tersedia
3. Membaca detail dan isi buku
4. Melihat riwayat transaksi/peminjaman
5. Admin memiliki akses tambahan untuk mengelola data buku dan pengguna.

##Fitur Utama
1. Autentikasi (Login dan Register)
2. Manajemen data buku
3. Detail dan isi buku digital
4. Riwayat transaksi/peminjaman
5. Dashboard admin
6. Database dengan data awal (sample data)

##Teknologi yang Digunakan
PHP (Backend)
MySQL (Database)
HTML & CSS (Frontend)
XAMPP(Local Development Server)

Struktur Direktori
Perpustakaan_Digital/
│
├── admin/                # Modul admin
├── assets/               # File CSS dan aset pendukung
├── config/               # Konfigurasi koneksi database
├── user/                 # Modul pengguna
├── index.php             # Halaman utama
├── login.php             # Halaman login
├── logout.php            # Proses logout
├── register.php          # Halaman registrasi
└── perpus_sekolah.sql    # File database

##Instalasi dan Konfigurasi
1. Clone repository:
   git clone https://github.com/Dila930/Perpustakaan_Digital.git
3. Pindahkan folder ke direktori htdocs (XAMPP) atau www (Laragon).
4. Buat database baru di phpMyAdmin, misalnya:
   perpus_sekolah
5. Import file:
   perpus_sekolah.sql
6. Sesuaikan konfigurasi database pada file di folder config/ apabila diperlukan.
7. Jalankan melalui browser:
8. http://localhost/Perpustakaan_Digital/
