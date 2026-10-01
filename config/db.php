<?php
/**
 * Konfigurasi Koneksi Database
 * ─────────────────────────────────────────────────────────────
 * Mendukung dua mode:
 *
 *   LOKAL (XAMPP/MySQL):
 *     Set DB_DRIVER=mysql di environment, atau biarkan default
 *     dan edit nilai DB_HOST, DB_NAME, DB_USER, DB_PASS di bawah.
 *
 *   PRODUCTION (Vercel + Supabase/PostgreSQL):
 *     Jangan hardcode kredensial di sini.
 *     Set semua nilai sebagai Environment Variables di:
 *       Vercel Dashboard → Project → Settings → Environment Variables
 *
 *     Variabel yang diperlukan:
 *       DB_DRIVER  = pgsql
 *       DB_HOST    = aws-0-ap-south-1.pooler.supabase.com
 *       DB_PORT    = 5432
 *       DB_NAME    = postgres
 *       DB_USER    = postgres.lgkvnxdeurhgfqoofoti
 *       DB_PASS    = [password supabase Anda]
 *       DB_SCHEMA  = public
 *
 * KEAMANAN: File ini ada di .gitignore dan .vercelignore.
 *   Untuk production, HANYA gunakan env variables — jangan
 *   simpan password di file ini.
 * ─────────────────────────────────────────────────────────────
 */

// ── Baca dari environment variable, fallback ke nilai default ──
$_driver = getenv('DB_DRIVER') ?: 'pgsql';
$_host   = getenv('DB_HOST')   ?: 'aws-0-ap-south-1.pooler.supabase.com';
$_port   = getenv('DB_PORT')   ?: '5432';
$_name   = getenv('DB_NAME')   ?: 'postgres';
$_user   = getenv('DB_USER')   ?: 'postgres.lgkvnxdeurhgfqoofoti';
$_pass   = getenv('DB_PASS')   ?: 'education-house12!';   // JANGAN hardcode password di sini
$_schema = getenv('DB_SCHEMA') ?: 'public';

// ── Bangun DSN ────────────────────────────────────────────────
if ($_driver === 'pgsql') {
    $_dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
        $_host, $_port, $_name
    );
} else {
    $_dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $_host, $_port, $_name
    );
}

// ── Buat koneksi PDO ──────────────────────────────────────────
try {
    $pdo = new PDO($_dsn, $_user, $_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // Set search_path untuk PostgreSQL (Supabase)
    if ($_driver === 'pgsql') {
        $pdo->exec("SET search_path TO " . $_schema);
    }

} catch (PDOException $e) {
    // Deteksi apakah lokal atau production
    $isLocal = in_array($_host, ['localhost', '127.0.0.1'], true);

    if ($isLocal) {
        // Lokal: tampilkan detail error untuk debugging
        http_response_code(500);
        die('<pre style="color:red;padding:20px;"><strong>Koneksi database gagal (lokal)</strong><br>' . htmlspecialchars($e->getMessage()) . '</pre>');
    } else {
        // Production: log saja, jangan tampilkan detail ke browser
        error_log('[DB ERROR] ' . $e->getMessage());
        http_response_code(500);
        die('<h1 style="font-family:sans-serif;color:#c00;padding:40px;">Terjadi kesalahan pada server.</h1><p style="font-family:sans-serif;padding:0 40px;">Mohon coba beberapa saat lagi.</p>');
    }
}

// Bersihkan variabel sensitif dari scope global
unset($_driver, $_host, $_port, $_name, $_user, $_pass, $_schema, $_dsn);
