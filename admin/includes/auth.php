<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../includes/functions.php';
require __DIR__ . '/../../includes/session_handler.php';

// Mulai session via DB handler (persistent di Vercel)
db_session_start($pdo);

// Kalau belum login, redirect ke halaman login
if (!isset($_SESSION['admin_id'])) {
    redirect('/admin/login.php');
}
