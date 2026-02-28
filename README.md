# Neraca

Neraca adalah aplikasi web manajemen keuangan pribadi komprehensif yang dirancang untuk membantu pengguna melacak arus kas, merencanakan anggaran, menetapkan target finansial, serta menganalisis dan memprediksi kondisi keuangan di masa depan. Dibangun menggunakan framework Laravel, Neraca memberikan solusi lengkap mulai dari pencatatan transaksi dasar hingga pelaporan analitik yang mendalam.

## Demo

![Demo](/documentation/neraca-landing-page.png)

## Fitur Utama

Aplikasi ini dilengkapi dengan serangkaian fitur untuk mempermudah pengelolaan finansial Anda:

- Keamanan Akun Tingkat Lanjut: Mendukung sistem registrasi, login, pengaturan ulang kata sandi, serta Autentikasi Dua Faktor untuk memberikan lapisan perlindungan tambahan pada akun pengguna.
- Dashboard Dinamis: Menampilkan ringkasan informasi keuangan dengan tata letak yang dapat disesuaikan dan disimpan menurut preferensi setiap pengguna.
- Manajemen Multi-Akun: Memungkinkan pengguna mengelola berbagai dompet seperti uang tunai, rekening bank, atau layanan dompet digital dalam satu sistem.
- Kategorisasi Transaksi: Mengelompokkan riwayat pemasukan dan pengeluaran agar lebih rapi, terorganisir, dan mudah dianalisis.
- Pencatatan Transaksi Lengkap: Mencatat seluruh aktivitas pemasukan dan pengeluaran, serta mendukung fitur transfer dana antar akun milik pengguna.
- Perencanaan Anggaran: Memberikan kemampuan untuk membuat batasan pengeluaran bulanan per kategori guna mencegah pengeluaran yang berlebihan.
- Target Keuangan: Mendukung penyusunan rencana tabungan untuk pencapaian finansial tertentu beserta pelacakan progres setoran dananya.
- Kalender dan Transaksi Berulang: Menampilkan transaksi dalam format kalender dan memungkinkan otomatisasi riwayat pengeluaran atau pemasukan yang sifatnya rutin.
- Template Transaksi: Menyediakan format cepat untuk beragam jenis transaksi yang sering terjadi, meminimalkan repetisi dalam pengisian formulir.
- Prediksi Keuangan: Menganalisis pola arus kas pengguna untuk membantu memproyeksikan kondisi keuangan di periode berikutnya.
- Pelaporan dan Ekspor Data: Menampilkan rangkuman statistik keuangan berkala. Mendukung fitur impor data serta ekspor hasil laporan ke dalam format PDF, Excel, dan CSV.
- Log Aktivitas: Mencatat seluruh jejak perubahan data yang dilakukan oleh pengguna untuk keperluan pemantauan sistem.
- Pencadangan dan Pemulihan Sistem: Memungkinkan ekspor dan impor kembali seluruh basis data pengguna untuk menjaga keamanan informasi jangka panjang.

## Teknologi yang Digunakan (Tech Stack)

Aplikasi Neraca dibangun dengan teknologi modern untuk memberikan performa dan keamanan terbaik:

- **Framework**: Laravel 12
- **Bahasa Pemrograman**: PHP 8.2
- **Database**: MySQL 
- **Frontend / Aset**: Vite & Tailwind CSS 
- **Pustaka Utama**:
  - `barryvdh/laravel-dompdf`: Ekspor laporan ke PDF.
  - `maatwebsite/excel`: Impor/ekspor transaksi berbasis spreadsheet.
  - `pragmarx/google2fa-laravel` & `bacon/bacon-qr-code`: Autentikasi Dua Faktor (2FA).

## Akses Aplikasi
**[neraca.web.id](https://neraca.web.id)**

## Lisensi

Licence di bawah [MIT](LICENSE)
