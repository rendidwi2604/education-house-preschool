<?php
/**
 * Vercel Entry Point Router
 *
 * Vercel menjalankan PHP dari folder api/, tapi semua file
 * sumber ada di root project. File ini bertugas:
 *   1. Menetapkan APP_ROOT = root project (satu level di atas api/)
 *   2. Membaca URL path dari request
 *   3. Memetakan ke file PHP yang sesuai di root atau admin/
 *   4. Meng-include file target sehingga semua require/include
 *      di file tersebut berjalan dengan __DIR__ yang benar
 *
 * PENTING: Vercel tidak bisa menyimpan file (read-only filesystem
 * kecuali /tmp). Upload gambar dari admin panel akan disimpan ke
 * /tmp dan HILANG setelah function di-restart. Gunakan Supabase
 * Storage atau Cloudinary untuk upload gambar production.
 */

// ── 1. Tetapkan root project ─────────────────────────────────
define('APP_ROOT', dirname(__DIR__));

// ── 2. Override path agar require relatif di file asli bekerja ─
//      Ubah working directory ke root project
chdir(APP_ROOT);

// ── 3. Parsing URL path ──────────────────────────────────────
$requestUri  = $_SERVER['REQUEST_URI'] ?? '/';
$parsedPath  = parse_url($requestUri, PHP_URL_PATH);
$path        = rtrim($parsedPath ?? '/', '/');

// Hapus prefix /api jika ada (Vercel kadang forward dengan prefix)
if (str_starts_with($path, '/api')) {
    $path = substr($path, 4);
}

// Normalisasi: hilangkan trailing slash
$path = rtrim($path, '/') ?: '/';

// ── 4. Routing table ─────────────────────────────────────────
$routes = [
    '/'                             => 'index.php',
    '/index.php'                    => 'index.php',
    '/berita_detail.php'            => 'berita_detail.php',
    '/guru_detail.php'              => 'guru_detail.php',
    '/ppdb_submit.php'              => 'ppdb_submit.php',

    '/admin'                        => 'admin/login.php',
    '/admin/login.php'              => 'admin/login.php',
    '/admin/logout.php'             => 'admin/logout.php',
    '/admin/dashboard.php'          => 'admin/dashboard.php',
    '/admin/berita.php'             => 'admin/berita.php',
    '/admin/berita_form.php'        => 'admin/berita_form.php',
    '/admin/galeri.php'             => 'admin/galeri.php',
    '/admin/guru.php'               => 'admin/guru.php',
    '/admin/guru_form.php'          => 'admin/guru_form.php',
    '/admin/slider.php'             => 'admin/slider.php',
    '/admin/slider_form.php'        => 'admin/slider_form.php',
    '/admin/instagram.php'          => 'admin/instagram.php',
    '/admin/pendaftar.php'          => 'admin/pendaftar.php',
    '/admin/pendaftar_export.php'   => 'admin/pendaftar_export.php',
    '/admin/profil.php'             => 'admin/profil.php',
    '/admin/register.php'           => 'admin/register.php',
    '/admin/forgot_password.php'    => 'admin/forgot_password.php',
];

// ── 5. Resolve file target ───────────────────────────────────
$targetFile = $routes[$path] ?? null;

// Fallback: coba cocokkan file langsung di root jika ada
if ($targetFile === null) {
    // Contoh: /admin/profil.php → admin/profil.php
    $candidate = ltrim($path, '/');
    if (
        preg_match('/^[a-zA-Z0-9_\-\/]+\.php$/', $candidate)
        && file_exists(APP_ROOT . '/' . $candidate)
    ) {
        $targetFile = $candidate;
    }
}

// Default ke index.php jika tidak ada yang cocok
if ($targetFile === null) {
    $targetFile = 'index.php';
}

$fullPath = APP_ROOT . '/' . $targetFile;

// ── 6. Keamanan: pastikan file ada dan masih di dalam APP_ROOT ─
$realTarget = realpath($fullPath);
$realRoot   = realpath(APP_ROOT);

if (
    $realTarget === false
    || !str_starts_with($realTarget, $realRoot . DIRECTORY_SEPARATOR)
    || !str_ends_with($realTarget, '.php')
) {
    http_response_code(404);
    echo '<h1>404 Not Found</h1>';
    echo '<p>Halaman tidak ditemukan.</p>';
    exit;
}

// ── 7. Simulasikan $_SERVER supaya file target
//       mendapat info path yang benar ──────────────────────────
$_SERVER['SCRIPT_FILENAME'] = $realTarget;
$_SERVER['SCRIPT_NAME']     = '/' . $targetFile;
$_SERVER['PHP_SELF']        = '/' . $targetFile;

// ── 8. Jalankan file target ───────────────────────────────────
// Gunakan include (bukan require) agar error tidak fatal
// dan file berjalan dalam scope global ini (penting untuk $pdo, dll)
include $realTarget;
