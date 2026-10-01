<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/admin_auth.php';
require __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama            = trim($_POST['nama'] ?? '');
    $username        = trim($_POST['username'] ?? '');
    $password        = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $institutionCode = $_POST['institution_code'] ?? '';

    if (!validAdminInstitutionCode($institutionCode)) {
        $error = 'Kode lembaga tidak valid.';
    } elseif ($nama === '' || $username === '') {
        $error = 'Nama dan username wajib diisi.';
    } elseif (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
        $error = 'Username harus 3-50 karakter, hanya huruf, angka, titik, underscore, atau strip.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM admin WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username sudah digunakan, coba username lain.';
        } else {
            $pdo->prepare('INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)')->execute([$username, password_hash($password, PASSWORD_DEFAULT), $nama]);
            $success = 'Akun admin berhasil dibuat! Silakan masuk.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar Admin — Education House Preschool</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="login-shell">
  <div class="login-box" style="max-width:460px;">

    <div class="brand">
      <img src="../assets/img/Logo_EduHouse.png" alt="Education House Logo"
           style="height:44px;width:auto;object-fit:contain;filter:drop-shadow(0 2px 8px rgba(0,0,0,.2));">
      <div>
        <span class="brand-name">Education House</span>
        <span class="brand-sub">Daftarkan akun admin baru</span>
      </div>
    </div>

    <p class="sub">Buat Akun Admin</p>

    <?php if ($error): ?>
    <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= h($success) ?></div>
    <?php endif; ?>

    <form method="post">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label><i class="fa-solid fa-user" style="color:#7952D9;"></i> Nama Lengkap</label>
          <input type="text" name="nama" value="<?= h($_POST['nama'] ?? '') ?>" required placeholder="Nama Anda">
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-at" style="color:#7952D9;"></i> Username</label>
          <input type="text" name="username" value="<?= h($_POST['username'] ?? '') ?>" required placeholder="username_anda">
        </div>
      </div>
      <div class="form-group">
        <label><i class="fa-solid fa-building" style="color:#F97316;"></i> Kode Lembaga</label>
        <input type="password" name="institution_code" required placeholder="Masukkan kode lembaga">
        <span class="form-help">Hubungi pengelola sistem untuk mendapatkan kode ini.</span>
      </div>
      <div class="form-group">
        <label><i class="fa-solid fa-lock" style="color:#7952D9;"></i> Password</label>
        <input type="password" name="password" minlength="8" required placeholder="Minimal 8 karakter">
      </div>
      <div class="form-group">
        <label><i class="fa-solid fa-lock" style="color:#7952D9;"></i> Konfirmasi Password</label>
        <input type="password" name="password_confirm" minlength="8" required placeholder="Ulangi password">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;padding:13px;font-size:15px;border-radius:14px;">
        <i class="fa-solid fa-user-plus"></i> Daftar Sekarang
      </button>
    </form>

    <div class="auth-links">
      <a href="login.php"><i class="fa-solid fa-right-to-bracket" style="font-size:11px;"></i> Masuk ke akun yang ada</a>
    </div>
  </div>
</div>
</body>
</html>
