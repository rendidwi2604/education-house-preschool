<?php
// Mencegah XSS saat menampilkan data ke halaman
function h($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// Format tanggal Indonesia sederhana, contoh: 23 Sep 2026
function tgl($datetime) {
    $bulan = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
              '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
    $d = date('d', strtotime($datetime));
    $m = $bulan[date('m', strtotime($datetime))];
    $y = date('Y', strtotime($datetime));
    return "$d $m $y";
}

function instagram_post_path($url) {
    if (!is_string($url)) return null;

    $parts = parse_url(trim($url));
    if (!$parts || strtolower($parts['scheme'] ?? '') !== 'https') return null;
    if (!in_array(strtolower($parts['host'] ?? ''), ['instagram.com', 'www.instagram.com'], true)) return null;
    if (!preg_match('~^/(p|reel|tv)/([A-Za-z0-9_-]+)/?$~D', $parts['path'] ?? '', $matches)) return null;

    return '/' . $matches[1] . '/' . $matches[2];
}

function tiktok_video_url($url) {
    if (!is_string($url)) return null;

    $parts = parse_url(trim($url));
    if (!$parts || strtolower($parts['scheme'] ?? '') !== 'https') return null;
    if (!empty($parts['user']) || !empty($parts['pass']) || isset($parts['port'])) return null;
    if (!in_array(strtolower($parts['host'] ?? ''), ['tiktok.com', 'www.tiktok.com', 'm.tiktok.com', 'vm.tiktok.com', 'vt.tiktok.com'], true)) return null;
    if (($parts['path'] ?? '/') === '/') return null;

    return trim($url);
}

function tiktok_video_id($url) {
    $validUrl = tiktok_video_url($url);
    if ($validUrl === null) return null;

    $path = parse_url($validUrl, PHP_URL_PATH);
    if (!is_string($path) || !preg_match('~^/@[^/]+/video/([0-9]+)(?:/)?$~D', $path, $matches)) return null;

    return $matches[1];
}

function tiktok_canonical_video_url($url) {
    $validUrl = tiktok_video_url($url);
    if ($validUrl === null) return null;
    if (tiktok_video_id($validUrl) !== null) return $validUrl;
    if (!function_exists('curl_init')) return null;

    $curl = curl_init($validUrl);
    curl_setopt_array($curl, [
        CURLOPT_NOBODY => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
    ]);
    $result = curl_exec($curl);
    $resolvedUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    curl_close($curl);

    if ($result === false || tiktok_video_id($resolvedUrl) === null) return null;

    return $resolvedUrl;
}
function tiktok_thumbnail_file($url) {
    $videoId = tiktok_video_id($url);
    if ($videoId === null) return null;

    $folder = __DIR__ . '/../assets/uploads/testimoni/';
    foreach (glob($folder . 'tiktok-' . $videoId . '.*') ?: [] as $cachedFile) {
        if (is_file($cachedFile)) return basename($cachedFile);
    }
    if (!function_exists('curl_init')) return null;

    $oembedUrl = 'https://www.tiktok.com/oembed?url=' . rawurlencode($url);
    $curl = curl_init($oembedUrl);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
    ]);
    $metadataResponse = curl_exec($curl);
    $metadataStatus = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);
    if ($metadataResponse === false || $metadataStatus !== 200) return null;

    $metadata = json_decode($metadataResponse, true);
    $thumbnailUrl = $metadata['thumbnail_url'] ?? null;
    $thumbnailParts = is_string($thumbnailUrl) ? parse_url($thumbnailUrl) : false;
    $thumbnailHost = strtolower($thumbnailParts['host'] ?? '');
    if (!$thumbnailParts || ($thumbnailParts['scheme'] ?? '') !== 'https' || !preg_match('/(?:^|\.)tiktokcdn(?:-[a-z0-9-]+)?\.com$/D', $thumbnailHost)) return null;

    $curl = curl_init($thumbnailUrl);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
    ]);
    $image = curl_exec($curl);
    $imageHost = strtolower(parse_url(curl_getinfo($curl, CURLINFO_EFFECTIVE_URL), PHP_URL_HOST) ?? '');
    curl_close($curl);
    if (!is_string($image) || strlen($image) > 5 * 1024 * 1024 || !preg_match('/(?:^|\.)tiktokcdn(?:-[a-z0-9-]+)?\.com$/D', $imageHost)) return null;

    $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($image);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime]) || @getimagesizefromstring($image) === false) return null;

    if (!is_dir($folder) && !mkdir($folder, 0755, true)) return null;
    $filename = 'tiktok-' . $videoId . '.' . $extensions[$mime];
    if (file_put_contents($folder . $filename, $image) === false) return null;

    return $filename;
}
