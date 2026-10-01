<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/admin_auth.php';
require __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username        = trim($_POST['username'] ?? '');
    $institutionCode = $_POST['institution_code'] ?? '';
    $password        = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (!validAdminInstitutionCode($institutionCode)) {
        $error = 'Kode lembaga tidak valid.';
    } elseif ($username === '') {
        $error = 'Username wajib diisi.';
    } elseif (strlen($password) < 8) {
        $error = 'Password baru minimal 8 karakter.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM admin WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if (!$admin) {
            $error = 'Username tidak ditemukan.';
        } else {
            $pdo->prepare('UPDATE admin SET password = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
            $success = 'Password berhasil diubah. Silakan masuk dengan password baru Anda.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lupa Password — Education House Preschool</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="login-shell">
  <div class="login-box">

    <div class="brand">
      <img src="../assets/img/Logo_EduHouse.png" alt="Education House Logo"
           style="height:44px;width:auto;object-fit:contain;filter:drop-shadow(0 2px 8px rgba(0,0,0,.2));">
      <div>
        <span class="brand-name">Education House</span>
        <span class="brand-sub">Atur ulang password admin</span>
      </div>
    </div>

    <p class="sub">Lupa Password?</p>
    <p style="font-size:13px;color:#6B7280;font-weight:500;margin:-10px 0 18px;line-height:1.6;">
      Masukkan username, kode lembaga, dan password baru Anda di bawah ini.
    </p>

    <?php if ($error): ?>
    <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= h($success) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label><i class="fa-solid fa-user" style="color:#F97316;"></i> Username</label>
        <input type="text" name="username" value="<?= h($_POST['username'] ?? '') ?>" required
               autofocus placeholder="Masukkan username Anda">
      </div>
      <div class="form-group">
        <label><i class="fa-solid fa-building" style="color:#F97316;"></i> Kode Lembaga</label>
        <input type="password" name="institution_code" required placeholder="Kode keamanan lembaga">
      </div>
      <div style="background:#FFF7ED;border:1.5px solid #FED7AA;border-radius:12px;padding:12px 14px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start;">
        <i class="fa-solid fa-triangle-exclamation" style="color:#F97316;flex-shrink:0;margin-top:1px;"></i>
        <p style="font-size:12px;color:#78350F;margin:0;font-weight:600;line-height:1.5;">
          Jika tidak memiliki kode lembaga, hubungi pengelola sistem untuk mendapatkannya.
        </p>
      </div>
      <div class="form-group">
        <label><i class="fa-solid fa-lock" style="color:#7952D9;"></i> Password Baru</label>
        <input type="password" name="password" minlength="8" required placeholder="Minimal 8 karakter">
      </div>
      <div class="form-group">
        <label><i class="fa-solid fa-lock" style="color:#7952D9;"></i> Konfirmasi Password Baru</label>
        <input type="password" name="password_confirm" minlength="8" required placeholder="Ulangi password baru">
      </div>
      <button type="submit" class="btn btn-orange" style="width:100%;padding:13px;font-size:15px;border-radius:14px;">
        <i class="fa-solid fa-rotate-right"></i> Ubah Password
      </button>
    </form>

    <div class="auth-links">
      <a href="login.php"><i class="fa-solid fa-arrow-left" style="font-size:11px;"></i> Kembali ke Login</a>
    </div>
  </div>
</div>
</body>
</html>
