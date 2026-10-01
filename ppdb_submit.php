<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$nama_anak = trim($_POST['nama_anak'] ?? '');
$usia_anak = trim($_POST['usia_anak'] ?? '');
$nama_ortu = trim($_POST['nama_ortu'] ?? '');
$alamat    = trim($_POST['alamat'] ?? '');
$whatsapp  = trim($_POST['whatsapp'] ?? '');

// Validasi sederhana: semua field wajib diisi
if ($nama_anak === '' || $usia_anak === '' || $nama_ortu === '' || $alamat === '' || $whatsapp === '') {
    header('Location: /index.php?gagal=1#ppdb');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO pendaftar (nama_anak, nama_ortu, alamat, whatsapp, usia_anak, status)
     VALUES (?, ?, ?, ?, ?, 'Baru')"
);
$stmt->execute([$nama_anak, $nama_ortu, $alamat, $whatsapp, $usia_anak]);

header('Location: /index.php?sukses=1#ppdb');
exit;
