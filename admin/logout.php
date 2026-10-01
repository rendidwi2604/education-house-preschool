<?php
session_start();
session_destroy();
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
header('Location: ' . $scheme . '://' . $host . '/admin/login.php');
exit;
