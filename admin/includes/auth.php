<?php
session_start();
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../includes/functions.php';

// Kalau belum login, tendang ke halaman login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
