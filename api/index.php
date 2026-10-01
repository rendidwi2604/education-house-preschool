<?php
/**
 * Vercel Single Entry Point Router
 *
 * Satu-satunya Serverless Function — menangani SEMUA request.
 * Vercel Hobby plan limit: 12 functions, jadi semua route
 * dipusatkan ke sini.
 *
 * Cara kerja:
 *   1. Baca URL path dari request
 *   2. Petakan ke file PHP asli di root project
 *   3. chdir() ke root agar semua require/include relatif bekerja
 *   4. include file target
 */

// ── Root project (satu level di atas folder api/) ────────────
define('APP_ROOT', dirname(__DIR__));
chdir(APP_ROOT);

// ── Tampilkan error untuk debugging (HAPUS setelah masalah solved) ──
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// ── Baca path dari URL ────────────────────────────────────────
$uri  = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);
$path = rtrim($path ?? '/', '/') ?: '/';

// ── Routing table: URL path → file PHP di root ───────────────
$routes = [
    // Halaman publik
    '/'                             => 'index.php',
    '/index.php'                    => 'index.php',
    '/berita_detail.php'            => 'berita_detail.php',
    '/guru_detail.php'              => 'guru_detail.php',
    '/ppdb_submit.php'              => 'ppdb_submit.php',

    // Admin
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

// ── Resolve file target ───────────────────────────────────────
$targetFile = $routes[$path] ?? null;

// Fallback dinamis: coba cocokkan file .php yang ada di root
if ($targetFile === null) {
    $candidate = ltrim($path, '/');
    if (
        preg_match('/^[a-zA-Z0-9_\-\/]+\.php$/', $candidate) &&
        file_exists(APP_ROOT . '/' . $candidate)
    ) {
        $targetFile = $candidate;
    }
}

// Default: index.php
if ($targetFile === null) {
    $targetFile = 'index.php';
}

$fullPath = APP_ROOT . '/' . $targetFile;

// ── Keamanan: path traversal check ───────────────────────────
$realTarget = realpath($fullPath);
$realRoot   = realpath(APP_ROOT);

if (
    $realTarget === false ||
    !str_starts_with($realTarget, $realRoot . DIRECTORY_SEPARATOR) ||
    !str_ends_with($realTarget, '.php')
) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><body><h1>404 Not Found</h1></body></html>';
    exit;
}

// ── Set variabel server agar redirect di file target benar ───
$_SERVER['SCRIPT_FILENAME'] = $realTarget;
$_SERVER['SCRIPT_NAME']     = '/' . $targetFile;
$_SERVER['PHP_SELF']        = '/' . $targetFile;

// ── Jalankan file target ──────────────────────────────────────
include $realTarget;
