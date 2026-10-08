<p align="center">
  <img src="htdocs/logo.png" alt="Logo PestGuard" width="120">
</p>

<h1 align="center">PestGuard</h1>

<p align="center">
  Sistem informasi berbasis web untuk memantau persebaran hama dan kondisi area pertanian.
</p>

## Tentang PestGuard

PestGuard membantu petugas memetakan area pertanian, mencatat jenis serta jumlah hama, menentukan tingkat kondisi area, dan menyajikan data melalui dashboard, grafik, peta interaktif, serta laporan.

Aplikasi ini dibuat menggunakan PHP native dan MySQL/MariaDB sehingga dapat dijalankan dengan mudah melalui XAMPP tanpa Composer atau proses build tambahan.

## Fitur

- Autentikasi pengguna dengan role admin dan petugas.
- Akses tamu untuk melihat dashboard dan laporan.
- Registrasi akun petugas baru.
- Dashboard ringkasan jumlah area berdasarkan status:
  - Aman
  - Waspada
  - Bahaya
- Grafik distribusi status dan data hama.
- Peta persebaran area pertanian.
- Pencarian lokasi dan penggambaran area berbentuk polygon.
- Pencatatan beberapa jenis hama dalam satu area.
- Manajemen dan penghapusan data area.
- Filter laporan berdasarkan status.
- Ekspor laporan ke format CSV yang dapat dibuka melalui Microsoft Excel.
- Halaman profil dan informasi status pengguna.

## Hak Akses

| Fitur | Admin/Petugas | Tamu |
|---|:---:|:---:|
| Melihat dashboard | ✅ | ✅ |
| Melihat peta dan grafik | ✅ | ✅ |
| Melihat laporan | ✅ | ✅ |
| Menginput data area | ✅ | ❌ |
| Mengelola dan menghapus area | ✅ | ❌ |
| Melihat profil | ✅ | ✅ |

> Akun yang dibuat melalui halaman registrasi otomatis mendapatkan role `petugas`.

## Teknologi

- PHP 8
- MySQL/MariaDB
- HTML, CSS, dan JavaScript
- [Leaflet](https://leafletjs.com/) untuk peta interaktif
- [Leaflet Draw](https://leaflet.github.io/Leaflet.draw/) untuk menggambar area
- [Leaflet Control Geocoder](https://github.com/perliedman/leaflet-control-geocoder) untuk pencarian lokasi
- [Chart.js](https://www.chartjs.org/) untuk visualisasi grafik
- XAMPP sebagai lingkungan pengembangan lokal

## Persyaratan

Sebelum menjalankan aplikasi, pastikan perangkat telah memiliki:

- XAMPP atau web server lain yang mendukung PHP dan MySQL/MariaDB.
- PHP 8.0 atau versi yang lebih baru.
- MySQL/MariaDB.
- Browser modern.
- Koneksi internet untuk memuat pustaka CDN, geocoder, dan tile peta.

## Instalasi

### 1. Clone repository

Simpan proyek di dalam direktori `htdocs` milik XAMPP:

```bash
git clone <URL_REPOSITORY> C:\xampp\htdocs\pestguard
```

Atau unduh repository lalu ekstrak ke:

```text
C:\xampp\htdocs\pestguard
```

### 2. Jalankan server

Buka XAMPP Control Panel, kemudian jalankan:

- Apache
- MySQL

### 3. Buat database

1. Buka [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Buat database baru bernama `pestguard`.
3. Pilih database tersebut.
4. Buka menu **Import**.
5. Impor file:

```text
htdocs/pestguard.sql
```

File SQL sudah berisi struktur tabel, data contoh area, dan akun demo.

### 4. Konfigurasi koneksi database

Pengaturan database berada di `htdocs/db.php`.

Konfigurasi bawaan:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pestguard";
```

Sesuaikan nilai tersebut apabila konfigurasi MySQL Anda berbeda.

### 5. Buka aplikasi

Akses aplikasi melalui:

```text
http://localhost/pestguard/htdocs/
```

## Akun Demo

| Role | Username | Password |
|---|---|---|
| Admin | `admin` | `123` |
| Petugas | `petugas` | `123` |

Anda juga dapat membuat akun petugas baru melalui halaman registrasi.

## Cara Penggunaan

1. Masuk menggunakan akun admin atau petugas.
2. Pilih menu **Input Data**.
3. Cari lokasi yang ingin dipetakan.
4. Gunakan alat gambar pada peta untuk membuat batas area.
5. Isi nama area, jenis hama, jumlah hama, dan status kondisi.
6. Simpan data ke database.
7. Pantau ringkasan melalui dashboard dan peta persebaran.
8. Buka menu **Laporan** untuk memfilter atau mengekspor data.
9. Gunakan menu **Manajemen Area** untuk melihat dan menghapus data.

## Struktur Proyek

```text
pestguard/
└── htdocs/
    ├── img/
    │   └── bg_sawah.png
    ├── area.php          # Manajemen data area
    ├── dashboard.php     # Ringkasan, grafik, dan peta
    ├── db.php            # Konfigurasi database
    ├── index.php         # Login dan akses tamu
    ├── input-data.php    # Input data dan pemetaan area
    ├── laporan.php       # Filter dan ekspor laporan
    ├── logout.php        # Proses logout
    ├── profil.php        # Profil pengguna
    ├── register.php      # Registrasi petugas
    ├── pestguard.sql     # Struktur dan data awal database
    └── logo.png          # Logo aplikasi
```

## Struktur Database

### Tabel `areas`

Menyimpan data area pertanian:

- Nama area
- Jenis hama
- Jumlah hama
- Status area
- Warna status
- Geometri area dalam format GeoJSON
- Tanggal pencatatan

### Tabel `users`

Menyimpan data pengguna:

- Username
- Password
- Role pengguna (`admin` atau `petugas`)

## Catatan Keamanan

Versi saat ini cocok untuk pengembangan lokal atau demonstrasi. Sebelum digunakan di lingkungan produksi, disarankan untuk:

- Menggunakan `password_hash()` dan `password_verify()` untuk menyimpan password.
- Mengganti akun serta password demo.
- Menggunakan prepared statement pada seluruh query yang menerima input pengguna.
- Menambahkan perlindungan CSRF pada proses penghapusan dan formulir.
- Melakukan validasi dan escaping output untuk mencegah XSS.
- Menggunakan akun database khusus dengan password yang kuat.
- Menonaktifkan tampilan error PHP pada server produksi.

## Pengembangan Berikutnya

Beberapa fitur yang dapat ditambahkan:

- Edit data area.
- Pengelolaan pengguna oleh admin.
- Upload foto kondisi tanaman.
- Penentuan status otomatis berdasarkan jumlah hama.
- Filter laporan berdasarkan rentang tanggal.
- Ekspor laporan ke PDF.
- Notifikasi ketika area masuk kategori bahaya.
- API untuk integrasi dengan aplikasi mobile atau sensor lapangan.

## Lisensi

Repository ini belum menyertakan lisensi. Tambahkan file `LICENSE` apabila proyek akan didistribusikan atau digunakan secara terbuka.
