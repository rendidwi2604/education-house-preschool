<?php
session_start();
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../includes/functions.php';

// Kalau belum login, redirect ke halaman login dengan URL absolut
if (!isset($_SESSION['admin_id'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    header('Location: ' . $scheme . '://' . $host . '/admin/login.php');
    exit;
}
