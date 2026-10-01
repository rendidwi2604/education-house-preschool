# Website Profil Education House Preschool

Website profil sekolah TK dengan panel admin, dibuat dengan **PHP native + MySQL**
(tanpa framework, supaya mudah dijalankan di hosting sederhana / XAMPP).

## Struktur Folder

```
tk-pelangi-ceria/
├── database.sql              # Skema database + data contoh, import ini duluan
├── config/db.php             # Pengaturan koneksi database
├── includes/                 # Header, footer, fungsi bantu halaman publik
├── index.php                 # Halaman utama (publik)
├── ppdb_submit.php           # Menyimpan data form pendaftaran PPDB
├── assets/
│   ├── css/style.css
│   └── uploads/               # Tempat foto galeri & foto guru tersimpan
└── admin/
    ├── login.php / logout.php
    ├── dashboard.php
    ├── berita.php + berita_form.php       # CRUD berita & pengumuman
    ├── galeri.php                          # Upload & hapus foto kegiatan
   ├── pendaftar.php + pendaftar_export.php # Data pendaftar PPDB + export Excel
    ├── guru.php + guru_form.php            # CRUD data guru & staf
    └── includes/                           # Sidebar, header/footer admin, auth
```

## Cara Menjalankan (Local, pakai XAMPP/Laragon)

1. Salin folder `tk-pelangi-ceria` ke folder `htdocs` (XAMPP) atau `www` (Laragon).
2. Buka **phpMyAdmin**, buat koneksi baru, lalu import file `database.sql`
   (ini otomatis membuat database `tk_pelangi_ceria` beserta isinya).
3. Cek pengaturan koneksi di `config/db.php` — defaultnya cocok untuk XAMPP:
   ```php
   $DB_HOST = 'localhost';
   $DB_NAME = 'tk_pelangi_ceria';
   $DB_USER = 'root';
   $DB_PASS = '';
   ```
4. Buka `http://localhost/tk-pelangi-ceria/` di browser untuk halaman publik.
5. Buka `http://localhost/tk-pelangi-ceria/admin/login.php` untuk panel admin.

Jika database sudah terpasang sebelum fitur Instagram ditambahkan, jalankan
`config/migrate_instagram.sql` melalui phpMyAdmin pada database
`tk_pelangi_ceria`. Migration ini mempertahankan postingan Instagram lama.
Setelah itu, tambahkan link postingan publik melalui menu **Instagram Website**
di panel admin. Setiap link baru menambah item; postingan privat atau yang
menonaktifkan penyematan tidak dapat ditampilkan.

Untuk mengaktifkan kegiatan Islami dan testimoni pada database yang sudah ada,
jalankan `config/migrate_kegiatan_testimoni.sql` melalui phpMyAdmin pada database
`tk_pelangi_ceria`. Database baru mendapat struktur ini otomatis dari
`database.sql`. Admin dapat memilih satu sumber untuk tiap konten: unggah file
manual, masukkan link postingan/Reel Instagram publik, atau masukkan link video
TikTok. Gambar sampul video TikTok ditampilkan pada kartu dan player dibuka saat
diklik. Testimoni tidak
menyimpan nama orang tua maupun anak. Video manual dibatasi 50MB; sesuaikan
`upload_max_filesize` dan `post_max_size` PHP pada Laragon/hosting agar unggahan
sebesar itu dapat diterima.

Ekspor data pendaftar mengunduh workbook `.xlsx` berformat jika ekstensi PHP
`zip` (`ext-zip`) tersedia. Server tanpa ekstensi tersebut tetap mendapat
fallback CSV.

## Login Admin Default

```
Username : admin
Password : admin123
```

**Segera ganti password ini setelah pertama kali login**, dengan menambahkan
fitur ganti password atau update langsung lewat phpMyAdmin (gunakan
fungsi `password_hash()` PHP, jangan simpan password polos).

## Deploy ke Hosting (cPanel dsb.)

1. Upload seluruh folder lewat File Manager atau FTP.
2. Buat database MySQL baru lewat cPanel, lalu import `database.sql` lewat phpMyAdmin.
3. Sesuaikan `config/db.php` dengan nama database, username, dan password yang
   diberikan oleh hosting (biasanya bukan `root` dan bukan password kosong).
4. Pastikan folder `assets/uploads/galeri`, `assets/uploads/guru`,
   `assets/uploads/islami`, dan `assets/uploads/testimoni` bisa ditulis
   (writable / permission 755 atau 775).

## Catatan Keamanan

- Password admin disimpan dalam bentuk **hash bcrypt**, bukan teks biasa.
- Upload foto dibatasi tipe file (jpg/jpeg/png/webp) dan ukuran maksimal 3MB.
- Semua output ke HTML sudah melalui `htmlspecialchars()` (fungsi `h()`) untuk
  mencegah serangan XSS.
- Query database memakai **prepared statement** (PDO) untuk mencegah SQL Injection.
- Untuk produksi, sebaiknya aktifkan HTTPS dan pertimbangkan menambah proteksi
  brute-force pada halaman login (misalnya batasi percobaan login).

## Mengembangkan Lebih Lanjut

Beberapa hal yang bisa ditambahkan sendiri:
- Ganti password admin dari dalam panel (bukan lewat phpMyAdmin)
- Multi-admin dengan level akses berbeda
- Kalender kegiatan sekolah
- Notifikasi WhatsApp otomatis saat ada pendaftar baru
