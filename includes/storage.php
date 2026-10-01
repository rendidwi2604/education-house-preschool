<?php
/**
 * Supabase Storage Helper
 *
 * Upload file ke Supabase Storage bucket "uploads".
 * Digunakan karena Vercel filesystem bersifat read-only.
 *
 * Setup Supabase Storage:
 *   1. Supabase Dashboard → Storage → New bucket → nama: "uploads" → Public: ON
 *   2. Buat folder: slider, galeri, guru, admin di dalam bucket
 *   3. Set env variable di Vercel:
 *      SUPABASE_URL  = https://xxxxxxxxxxxx.supabase.co
 *      SUPABASE_KEY  = [service_role key dari Project Settings → API]
 *
 * CATATAN: Gunakan service_role key (bukan anon key) agar bisa upload.
 * Service role key bersifat rahasia — HANYA taruh di env variable Vercel,
 * jangan pernah commit ke GitHub.
 */

/**
 * Upload file ke Supabase Storage.
 *
 * @param string $tmpPath   Path file sementara ($_FILES['x']['tmp_name'])
 * @param string $folder    Subfolder di bucket, contoh: 'slider', 'galeri', 'guru'
 * @param string $filename  Nama file tujuan, contoh: 'slide_123_456.webp'
 * @param string $mimeType  MIME type file
 * @return array{ok:bool, url:string, error:string}
 */
function supabase_upload(string $tmpPath, string $folder, string $filename, string $mimeType): array
{
    $supabaseUrl = getenv('SUPABASE_URL');
    $supabaseKey = getenv('SUPABASE_KEY');

    if (!$supabaseUrl || !$supabaseKey) {
        return ['ok' => false, 'url' => '', 'error' => 'SUPABASE_URL / SUPABASE_KEY belum diset di env variables.'];
    }

    $bucket      = 'uploads';
    $storagePath = $folder . '/' . $filename;
    $uploadUrl   = rtrim($supabaseUrl, '/') . '/storage/v1/object/' . $bucket . '/' . $storagePath;

    $fileData = file_get_contents($tmpPath);
    if ($fileData === false) {
        return ['ok' => false, 'url' => '', 'error' => 'Gagal membaca file upload.'];
    }

    $ctx = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => implode("\r\n", [
                'Authorization: Bearer ' . $supabaseKey,
                'Content-Type: ' . $mimeType,
                'x-upsert: true',          // overwrite jika sudah ada
                'Content-Length: ' . strlen($fileData),
            ]),
            'content'         => $fileData,
            'ignore_errors'   => true,
            'timeout'         => 30,
        ],
        'ssl' => ['verify_peer' => true],
    ]);

    $response   = @file_get_contents($uploadUrl, false, $ctx);
    $statusLine = $http_response_header[0] ?? 'HTTP/1.1 500';
    preg_match('/HTTP\/\d\.?\d?\s+(\d+)/', $statusLine, $m);
    $statusCode = (int) ($m[1] ?? 500);

    if ($statusCode === 200 || $statusCode === 201) {
        // URL publik gambar
        $publicUrl = rtrim($supabaseUrl, '/') . '/storage/v1/object/public/' . $bucket . '/' . $storagePath;
        return ['ok' => true, 'url' => $publicUrl, 'error' => ''];
    }

    $body = $response ?: 'No response';
    error_log("[SUPABASE UPLOAD] $statusCode: $body");
    return ['ok' => false, 'url' => '', 'error' => "Upload gagal (HTTP $statusCode). Cek SUPABASE_KEY dan bucket policy."];
}

/**
 * Hapus file dari Supabase Storage.
 *
 * @param string $folder   Subfolder di bucket
 * @param string $filename Nama file
 */
function supabase_delete(string $folder, string $filename): void
{
    $supabaseUrl = getenv('SUPABASE_URL');
    $supabaseKey = getenv('SUPABASE_KEY');
    if (!$supabaseUrl || !$supabaseKey || !$filename) return;

    $bucket      = 'uploads';
    $storagePath = $folder . '/' . $filename;
    $deleteUrl   = rtrim($supabaseUrl, '/') . '/storage/v1/object/' . $bucket . '/' . $storagePath;

    $ctx = stream_context_create([
        'http' => [
            'method'        => 'DELETE',
            'header'        => 'Authorization: Bearer ' . $supabaseKey,
            'ignore_errors' => true,
            'timeout'       => 10,
        ],
    ]);
    @file_get_contents($deleteUrl, false, $ctx);
}

/**
 * Deteksi MIME type dari ekstensi file.
 */
function mime_from_ext(string $ext): string
{
    return match (strtolower($ext)) {
        'jpg', 'jpeg' => 'image/jpeg',
        'png'         => 'image/png',
        'webp'        => 'image/webp',
        'gif'         => 'image/gif',
        default       => 'application/octet-stream',
    };
}
