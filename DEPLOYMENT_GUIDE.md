# Panduan Deployment: Vercel + Supabase

**Project:** Education House Preschool  
**Stack:** PHP Native · PostgreSQL (Supabase) · Hosting Serverless (Vercel)

---

## Daftar Isi

1. [Gambaran Arsitektur](#1-gambaran-arsitektur)
2. [Persiapan Awal](#2-persiapan-awal)
3. [Setup Database di Supabase](#3-setup-database-di-supabase)
4. [Jalankan Migrasi SQL](#4-jalankan-migrasi-sql)
5. [Upload Project ke GitHub](#5-upload-project-ke-github)
6. [Deploy ke Vercel](#6-deploy-ke-vercel)
7. [Set Environment Variables di Vercel](#7-set-environment-variables-di-vercel)
8. [Verifikasi & Testing](#8-verifikasi--testing)
9. [Catatan Penting: File Upload di Vercel](#9-catatan-penting-file-upload-di-vercel)
10. [Troubleshooting](#10-troubleshooting)
11. [Checklist Ringkas](#11-checklist-ringkas)

---

## 1. Gambaran Arsitektur

```
Browser
   │
   ▼
Vercel (PHP Serverless via vercel-php@0.7.4)
   │  ├── api/index.php          ← router utama (index, berita_detail, dll)
   │  ├── api/admin/*.php        ← semua halaman admin
   │  └── assets/               ← CSS, gambar statis (CDN Vercel)
   │
   ▼
Supabase (PostgreSQL 15)
   └── Database: postgres (schema: public)
       ├── admin, berita, galeri, guru
       ├── slider, instagram_posts, instagram_setting
       ├── kegiatan_islami, testimoni_orangtua, pendaftar
```

**Cara kerja routing:**
Semua request PHP diarahkan ke `api/` via `vercel.json`. Setiap file di `api/` memanggil file asli di root project menggunakan `APP_ROOT` + `chdir()` agar semua `require`/`include` relatif tetap berjalan normal.

---

## 2. Persiapan Awal

### Akun yang diperlukan (semua gratis)

| Layanan | URL | Kegunaan |
|---------|-----|----------|
| GitHub | github.com | Menyimpan kode project |
| Supabase | supabase.com | Database PostgreSQL |
| Vercel | vercel.com | Hosting PHP serverless |

### Tools di komputer

| Tool | Link | Kegunaan |
|------|------|----------|
| Git | git-scm.com | Upload kode ke GitHub |
| Node.js (opsional) | nodejs.org | Untuk Vercel CLI |
| VS Code | sudah ada | Edit konfigurasi |

---

## 3. Setup Database di Supabase

### 3.1 Buat Project Baru

1. Buka [app.supabase.com](https://app.supabase.com) → login atau daftar
2. Klik **"New Project"**
3. Isi form:
   - **Name:** `education-house`
   - **Database Password:** buat password kuat — **catat baik-baik**, tidak bisa dilihat lagi
   - **Region:** `Southeast Asia (Singapore)` — pilih ini untuk latensi rendah dari Indonesia
4. Klik **"Create new project"** → tunggu sekitar 2 menit

### 3.2 Catat Kredensial Database

1. Di Supabase Dashboard, klik tombol **"Connect"** (kanan atas, dekat nama project)
2. Pilih tab **"Direct connection"**
3. Catat semua nilai berikut — akan dipakai di langkah 7:

```
Host     : db.<project-ref>.supabase.co   contoh: db.lgkvnxdeurhgfqoofoti.supabase.co
Port     : 5432
Database : postgres
User     : postgres
Password : [password yang dibuat di step 3.1]
```

> **Catatan:** Password tidak ditampilkan di sini — gunakan password
> yang Anda buat saat pertama kali membuat project Supabase.
> Jika lupa, bisa reset di **Project Settings → Database → Reset database password**

---

## 4. Jalankan Migrasi SQL

### 4.1 Buka SQL Editor

1. Di sidebar kiri Supabase, klik ikon **SQL Editor** (ikon `< >`)
2. Klik **"New query"**

### 4.2 Jalankan 3 File Migrasi Secara Berurutan

> ⚠️ **Urutan wajib: 01 → 02 → 03. Jangan dibalik.**

---

**STEP A — Jalankan `migration/01_schema.sql`**

1. Buka file `migration/01_schema.sql` di VS Code
2. **Ctrl+A** → **Ctrl+C** (copy semua)
3. Paste ke SQL Editor Supabase
4. Klik **"Run"** (tombol hijau, atau **Ctrl+Enter**)
5. Tunggu hingga muncul: `Success. No rows returned`

---

**STEP B — Jalankan `migration/02_seed_data.sql`**

1. Buka **"New query"** baru di SQL Editor
2. Buka file `migration/02_seed_data.sql`, copy semua, paste
3. Klik **"Run"**
4. Hasil: beberapa baris `setval` — itu normal

---

**STEP C — Jalankan `migration/03_rls_policy.sql`**

1. Buka **"New query"** baru
2. Copy isi `migration/03_rls_policy.sql`, paste, klik **"Run"**

### 4.3 Verifikasi

1. Klik **"Table Editor"** di sidebar kiri
2. Pastikan 10 tabel ini muncul:

```
✅ admin              ✅ berita
✅ galeri             ✅ guru
✅ instagram_posts    ✅ instagram_setting
✅ kegiatan_islami    ✅ pendaftar
✅ slider             ✅ testimoni_orangtua
```

---

## 5. Upload Project ke GitHub

### 5.1 Buat Repository di GitHub

1. Buka [github.com](https://github.com) → login
2. Klik **"+"** (kanan atas) → **"New repository"**
3. Isi:
   - **Repository name:** `education-house-preschool`
   - **Visibility:** `Private` ← pilih ini agar kode tidak publik
4. Klik **"Create repository"**

### 5.2 Siapkan File Sebelum Upload

Pastikan `config/db.php` **tidak** berisi password production. Cek
isinya — semua nilai harus `getenv(...)` bukan string hardcoded.

File yang **sudah ada** di `.gitignore` (tidak akan ikut terupload):
```
config/db.php          ← AMAN, tidak terupload
*.sql                  ← file backup SQL tidak terupload
migration/             ← folder migrasi tidak terupload
```

> ⚠️ **PENTING:** Folder `migration/` ada di `.vercelignore` tapi
> **tidak** ada di `.gitignore`. Artinya file migrasi ikut ke GitHub
> tapi tidak di-deploy ke Vercel. Ini sudah benar.

### 5.3 Push ke GitHub

Buka terminal di VS Code (`Ctrl + ~`), jalankan perintah ini satu per satu:

```bash
# Masuk ke folder project
cd C:\php\htdocs\education_hp

# Inisialisasi git (hanya sekali)
git init

# Tambahkan semua file
git add .

# Cek apa yang akan di-commit (pastikan config/db.php TIDAK ada di list)
git status

# Buat commit pertama
git commit -m "Initial commit: Education House Preschool"

# Hubungkan ke GitHub (ganti URL dengan repo Anda)
git remote add origin https://github.com/username-anda/education-house-preschool.git

# Push ke GitHub
git push -u origin main
```

> Jika `git push` minta login, masukkan username dan
> **Personal Access Token** GitHub (bukan password).
> Buat token di: GitHub → Settings → Developer settings →
> Personal access tokens → Tokens (classic) → Generate new token
> Centang scope: `repo`

---

## 6. Deploy ke Vercel

### 6.1 Hubungkan GitHub ke Vercel

1. Buka [vercel.com](https://vercel.com) → login atau daftar
2. Klik **"Add New..."** → **"Project"**
3. Pilih **"Import Git Repository"**
4. Klik **"Add GitHub Account"** → authorize Vercel
5. Cari repository `education-house-preschool` → klik **"Import"**

### 6.2 Konfigurasi Project

Di halaman "Configure Project":

| Setting | Nilai |
|---------|-------|
| **Framework Preset** | `Other` |
| **Root Directory** | `.` (titik, root project) |
| **Build Command** | *(kosongkan)* |
| **Output Directory** | *(kosongkan)* |
| **Install Command** | *(kosongkan)* |

> **Jangan klik "Deploy" dulu** — lanjut ke langkah 7 untuk set
> environment variables terlebih dahulu.

---

## 7. Set Environment Variables di Vercel

Ini adalah langkah **terpenting**. Tanpa ini, website tidak bisa
terhubung ke database Supabase.

### 7.1 Cara Menambahkan

Di halaman "Configure Project" (sebelum deploy pertama):
1. Scroll ke bawah ke bagian **"Environment Variables"**
2. Tambahkan variabel satu per satu:

| Name | Value |
|------|-------|
| `DB_DRIVER` | `pgsql` |
| `DB_HOST` | `aws-0-ap-south-1.pooler.supabase.com` |
| `DB_PORT` | `5432` |
| `DB_NAME` | `postgres` |
| `DB_USER` | `postgres.lgkvnxdeurhgfqoofoti` |
| `DB_PASS` | `[password Supabase Anda]` |
| `DB_SCHEMA` | `public` |

3. Setiap variabel: ketik **Name**, ketik **Value**, klik **"Add"**
4. Pastikan semua 7 variabel sudah masuk
5. Klik **"Deploy"**

### 7.2 Edit Environment Variables Setelah Deploy

Jika perlu ubah nilai setelah deploy:
1. Vercel Dashboard → pilih project Anda
2. Tab **"Settings"** → **"Environment Variables"**
3. Klik ikon edit (pensil) di sebelah variabel yang ingin diubah
4. Setelah simpan → klik **"Redeploy"** agar perubahan aktif

---

## 8. Verifikasi & Testing

Setelah deploy selesai, Vercel akan memberi URL seperti:
`https://education-house-preschool.vercel.app`

### 8.1 Cek Halaman Utama

1. Buka URL tersebut di browser
2. Yang harus muncul:
   - ✅ Navbar dengan logo
   - ✅ Hero slider dengan foto
   - ✅ Section program (Toodler, Playgroup, dll)
   - ✅ Data guru dengan foto
   - ✅ Berita/prestasi siswa
   - ✅ Form pendaftaran

### 8.2 Cek Admin Panel

1. Akses `https://[url-anda]/admin/login.php`
2. Login dengan:
   - Username: `admin`
   - Password: `admin123` *(sesuai data di seed)*
3. Yang harus bisa dilakukan:
   - ✅ Melihat dashboard dengan statistik
   - ✅ Membuka menu Berita, Guru, Pendaftar, dll

### 8.3 Cek Form Pendaftaran

1. Di halaman utama scroll ke **"Formulir Pendaftaran"**
2. Isi semua field, klik **"Kirim Pendaftaran Sekarang"**
3. Harus redirect ke halaman sukses dengan pesan konfirmasi
4. Cek di Supabase → **Table Editor** → tabel `pendaftar`
5. Data baru harus muncul

### 8.4 Cek Koneksi Database

Cara cepat verifikasi koneksi DB berhasil:
Jika halaman utama tampil dengan data (slider, guru, berita) → koneksi OK.
Jika halaman putih atau error 500 → ada masalah env variables (cek langkah 7).

---

## 9. Catatan Penting: File Upload di Vercel

> ⚠️ **KETERBATASAN KRITIS:** Vercel adalah serverless platform dengan
> filesystem **read-only**. File yang diupload melalui admin panel
> (gambar berita, foto guru, slider) akan disimpan ke `/tmp` dan
> **HILANG** setelah function di-restart (biasanya dalam beberapa menit).

### Solusi untuk Upload Gambar Production

Gunakan **Supabase Storage** (gratis, 1GB):

**Langkah singkat setup Supabase Storage:**
1. Supabase Dashboard → **"Storage"** di sidebar
2. Klik **"New bucket"**
3. Buat bucket: `uploads` → centang **"Public bucket"** → Create
4. Di dalam bucket, buat folder: `galeri`, `guru`, `slider`, `admin`, `testimoni`

**Update kode upload (untuk developer):**
Ganti fungsi `move_uploaded_file()` di file-file admin dengan
Supabase Storage API menggunakan `file_get_contents` + stream context,
atau gunakan library PHP Supabase.

**Sementara (untuk demo/testing):**
Upload gambar langsung ke bucket via Supabase Dashboard → Storage → Upload file.
Lalu update nama file di database via Table Editor.

---

## 10. Troubleshooting

### ❌ Halaman putih / error 500

**Penyebab:** Environment variables belum di-set atau salah.

**Solusi:**
1. Vercel Dashboard → Project → Settings → Environment Variables
2. Pastikan semua 7 variabel ada dan nilainya benar
3. Klik **"Redeploy"** dari tab Deployments

---

### ❌ Error "SQLSTATE: connection refused" atau timeout

**Penyebab:** Kredensial Supabase salah atau port tidak sesuai.

**Solusi:**
1. Pastikan `DB_PORT` = `6543` (Transaction pooler, bukan 5432)
2. Pastikan `DB_HOST` sesuai region yang dipilih saat buat project Supabase
3. Pastikan `DB_USER` format: `postgres.xxxxxxxxxxxxx` (ada titik dan project ref)
4. Cek password tidak ada spasi di awal/akhir

---

### ❌ Error "relation does not exist"

**Penyebab:** Tabel belum dibuat di Supabase.

**Solusi:** Ulangi langkah 4 — jalankan `01_schema.sql` di SQL Editor Supabase.

---

### ❌ Gambar tidak muncul di website

**Penyebab A:** File gambar tidak ikut ter-deploy ke Vercel.

**Solusi:**
- Pastikan folder `assets/uploads/` ter-commit ke GitHub (cek `.gitignore`)
- File gambar tidak ada di `.gitignore` (folder uploads yang ada di sana
  hanya `*.gitkeep`, isinya boleh)
- Push ulang: `git add assets/ && git commit -m "add assets" && git push`

**Penyebab B:** Path gambar di database tidak sesuai nama file.

**Solusi:** Cek nama file di Supabase Table Editor → kolom `gambar` di
tabel `berita`/`guru`/`slider` harus sama persis dengan nama file di
`assets/uploads/`.

---

### ❌ Admin login tidak bisa / session hilang

**Penyebab:** Vercel serverless tidak persistent — tapi session PHP
berbasis cookie dan tetap berfungsi.

**Solusi:**
1. Pastikan `session_start()` ada di awal file admin yang membutuhkan session
2. Pastikan `php.ini` di `api/` sudah ada (sudah dibuat di langkah sebelumnya)
3. Coba clear browser cache dan cookies, lalu login ulang

---

### ❌ Deploy gagal di Vercel dengan error "Runtime not found"

**Penyebab:** `vercel.json` tidak terbaca atau format salah.

**Solusi:**
1. Pastikan `vercel.json` ada di **root project** (bukan di subfolder)
2. Validasi JSON: buka vercel.json di VS Code, pastikan tidak ada syntax error
3. Pastikan runtime `vercel-php@0.7.4` ditulis persis seperti itu

---

### ❌ URL `/admin/dashboard.php` redirect ke login terus

**Penyebab:** Normal jika belum login. Jika sudah login tapi terus redirect:

**Solusi:**
1. Cek `api/admin/dashboard.php` — pastikan isinya hanya:
   ```php
   <?php
   define('APP_ROOT', dirname(__DIR__, 2));
   chdir(APP_ROOT);
   include APP_ROOT . '/admin/dashboard.php';
   ```
2. Cek `admin/includes/auth.php` — `session_start()` sudah ada
3. Coba login ulang dari `/admin/login.php`

---

## 11. Checklist Ringkas

```
SUPABASE
[ ] Daftar/login di supabase.com
[ ] Buat project baru (region: Singapore)
[ ] Catat: Host, Port (6543), Username, Password
[ ] SQL Editor → jalankan 01_schema.sql
[ ] SQL Editor → jalankan 02_seed_data.sql
[ ] SQL Editor → jalankan 03_rls_policy.sql
[ ] Table Editor → verifikasi 10 tabel ada

GITHUB
[ ] Buat repository private di GitHub
[ ] git init di folder project
[ ] git add . → git status (pastikan config/db.php tidak ada)
[ ] git commit -m "Initial commit"
[ ] git remote add origin [url repo]
[ ] git push -u origin main

VERCEL
[ ] Daftar/login di vercel.com
[ ] Add New Project → Import dari GitHub
[ ] Framework: Other, Root: . , Build/Output: kosong
[ ] Set 7 environment variables (DB_DRIVER, DB_HOST, DB_PORT,
    DB_NAME, DB_USER, DB_PASS, DB_SCHEMA)
[ ] Klik Deploy
[ ] Buka URL hasil deploy → cek halaman utama tampil
[ ] Cek /admin/login.php → login berhasil
[ ] Test form pendaftaran → data masuk ke Supabase
[ ] Cek Supabase Table Editor → tabel pendaftar ada data baru
```

---

## Struktur File yang Di-deploy ke Vercel

```
education_hp/
├── api/                        ← PHP Serverless Functions
│   ├── index.php               ← router utama
│   ├── berita_detail.php
│   ├── guru_detail.php
│   ├── ppdb_submit.php
│   ├── php.ini                 ← konfigurasi PHP
│   └── admin/
│       ├── login.php
│       ├── dashboard.php
│       └── [16 file admin lainnya]
├── assets/                     ← static files (CDN Vercel)
│   ├── css/
│   ├── img/
│   └── uploads/
├── config/
│   └── db.php                  ← baca dari env variables
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── admin/                      ← source PHP (di-include oleh api/admin/)
│   ├── includes/
│   └── [semua file admin]
├── index.php                   ← source (di-include oleh api/index.php)
├── berita_detail.php
├── guru_detail.php
├── ppdb_submit.php
└── vercel.json                 ← konfigurasi routing Vercel
```

---

*Panduan ini untuk project Education House Preschool — PHP Native + Vercel + Supabase PostgreSQL*
