<?php
<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';

// Helper URL absolut
function abs_url(string $path): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . $path;
}

if (isset($_SESSION['admin_id'])) {
    header('Location: ' . abs_url('/admin/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    $passwordValid = $admin && (
        password_verify($password, $admin['password'])
        || hash_equals($admin['password'], $password)
    );

    if ($passwordValid) {
        if (!password_get_info($admin['password'])['algo']) {
            $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?");
            $update->execute([$newPasswordHash, $admin['id']]);
        }
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama'];
        $_SESSION['admin_foto'] = $admin['foto'] ?? null;
        header('Location: ' . abs_url('/admin/dashboard.php'));
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin — Education House Preschool</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css?v=20260930-1">
<style>
  /* extra decorative elements */
  .login-deco-star {
    position:absolute;
    pointer-events:none;
    animation: floatUpDown 3s ease-in-out infinite;
  }
  @keyframes floatUpDown {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-10px); }
  }
  .login-deco-star:nth-child(2) { animation-delay:.8s; }
  .login-deco-star:nth-child(3) { animation-delay:1.6s; }
  .sun-shine { animation: rotateSlow 28s linear infinite; }
  @keyframes rotateSlow { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

  .input-wrap { position:relative; }
  .input-wrap .input-icon {
    position:absolute; left:14px; top:50%; transform:translateY(-50%);
    color:#69A85A; font-size:14px; pointer-events:none;
  }
  .input-wrap input {
    padding-left:40px !important;
  }
  .toggle-pw {
    position:absolute; right:14px; top:50%; transform:translateY(-50%);
    background:none; border:none; cursor:pointer; color:#94A3B8;
    font-size:14px; padding:0;
  }
  .toggle-pw:hover { color:#328C39; }
</style>
</head>
<body style="background:linear-gradient(135deg,#E4F5DC 0%,#EFF8E9 72%,#FFF0DE 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;position:relative;overflow:hidden;">

<!-- Decorative SVG floaters -->
<svg class="login-deco-star" style="top:8%;left:8%;width:56px;height:56px;opacity:.35;" viewBox="0 0 100 100">
  <defs><linearGradient id="ls1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#FCD34D"/><stop offset="100%" style="stop-color:#F59E0B"/></linearGradient></defs>
  <path d="M50 5L61 38L95 38L68 58L79 91L50 71L21 91L32 58L5 38L39 38Z" fill="url(#ls1)" stroke="#D97706" stroke-width="2"/>
  <circle cx="38" cy="25" r="5" fill="#fff" opacity=".6"/>
</svg>

<svg class="login-deco-star" style="top:12%;right:10%;width:44px;height:44px;opacity:.35;" viewBox="0 0 100 100">
  <defs><linearGradient id="ls2" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#FCA5A5"/><stop offset="100%" style="stop-color:#F87171"/></linearGradient></defs>
  <path d="M50 85C50 85,20 60,20 40C20 25,30 20,40 25C45 27,50 32,50 32C50 32,55 27,60 25C70 20,80 25,80 40C80 60,50 85,50 85Z" fill="url(#ls2)" stroke="#DC2626" stroke-width="2"/>
  <circle cx="35" cy="34" r="5" fill="#fff" opacity=".6"/>
</svg>

<svg class="login-deco-star" style="bottom:12%;left:12%;width:40px;height:40px;opacity:.3;" viewBox="0 0 80 80">
  <defs><linearGradient id="ls3" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#86EFAC"/><stop offset="100%" style="stop-color:#22C55E"/></linearGradient></defs>
  <ellipse cx="40" cy="30" rx="24" ry="30" fill="url(#ls3)" stroke="#16A34A" stroke-width="2"/>
  <circle cx="30" cy="20" r="6" fill="#fff" opacity=".5"/>
  <path d="M40 60 Q36 72 40 80" stroke="#16A34A" stroke-width="2" fill="none"/>
</svg>

<svg class="login-deco-star" style="bottom:14%;right:8%;width:52px;height:52px;opacity:.3;animation-delay:1.2s;" viewBox="0 0 100 100">
  <defs><linearGradient id="ls4" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#A5F3FC"/><stop offset="100%" style="stop-color:#06B6D4"/></linearGradient></defs>
  <ellipse cx="50" cy="36" rx="28" ry="35" fill="url(#ls4)" stroke="#0891B2" stroke-width="2"/>
  <circle cx="38" cy="24" r="7" fill="#fff" opacity=".5"/>
  <path d="M50 71 Q46 84 50 95" stroke="#0891B2" stroke-width="2" fill="none"/>
</svg>

<!-- Login card -->
<div class="login-box" style="position:relative;z-index:2;">

  <!-- Brand header -->
    <div class="brand" style="margin-bottom:20px;">
      <img src="../assets/img/Logo_EduHouse.png" alt="Education House Logo"
           style="height:44px;width:auto;object-fit:contain;filter:drop-shadow(0 2px 8px rgba(0,0,0,.2));">
      <div>
        <span class="brand-name">Education House</span>
        <span class="brand-sub">Preschool Admin Panel</span>
      </div>
    <!-- Spinning sun -->
    <svg class="sun-shine" style="width:32px;height:32px;margin-left:auto;flex-shrink:0;" viewBox="0 0 100 100">
      <g transform="translate(50 50)">
        <line x1="0" y1="-44" x2="0" y2="-34" stroke="#F59E0B" stroke-width="5" stroke-linecap="round"/>
        <line x1="31" y1="-31" x2="24" y2="-24" stroke="#F59E0B" stroke-width="5" stroke-linecap="round"/>
        <line x1="44" y1="0" x2="34" y2="0" stroke="#F59E0B" stroke-width="5" stroke-linecap="round"/>
        <line x1="31" y1="31" x2="24" y2="24" stroke="#F59E0B" stroke-width="5" stroke-linecap="round"/>
        <line x1="0" y1="44" x2="0" y2="34" stroke="#F59E0B" stroke-width="5" stroke-linecap="round"/>
        <line x1="-31" y1="31" x2="-24" y2="24" stroke="#F59E0B" stroke-width="5" stroke-linecap="round"/>
        <line x1="-44" y1="0" x2="-34" y2="0" stroke="#F59E0B" stroke-width="5" stroke-linecap="round"/>
        <line x1="-31" y1="-31" x2="-24" y2="-24" stroke="#F59E0B" stroke-width="5" stroke-linecap="round"/>
      </g>
      <circle cx="50" cy="50" r="20" fill="#FCD34D" stroke="#D97706" stroke-width="2"/>
      <circle cx="43" cy="46" r="2.5" fill="#92400E"/>
      <circle cx="57" cy="46" r="2.5" fill="#92400E"/>
      <path d="M43 55 Q50 61 57 55" stroke="#92400E" stroke-width="2.5" fill="none" stroke-linecap="round"/>
    </svg>
  </div>

  <p class="sub">Masuk ke Panel Admin</p>

  <?php if ($error): ?>
  <div class="alert alert-error">
    <i class="fa-solid fa-circle-exclamation"></i>
    <?= h($error) ?>
  </div>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label><i class="fa-solid fa-user" style="color:#328C39;"></i> Username</label>
      <div class="input-wrap">
        <i class="fa-solid fa-user input-icon"></i>
        <input type="text" name="username" required autofocus
               placeholder="Masukkan username Anda"
               value="<?= h($_POST['username'] ?? '') ?>">
      </div>
    </div>
    <div class="form-group">
      <label><i class="fa-solid fa-lock" style="color:#328C39;"></i> Password</label>
      <div class="input-wrap">
        <i class="fa-solid fa-lock input-icon"></i>
        <input type="password" name="password" id="pwInput" required placeholder="Masukkan password">
        <button type="button" class="toggle-pw" onclick="togglePw()" id="pwToggle">
          <i class="fa-solid fa-eye" id="pwEyeIcon"></i>
        </button>
      </div>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;padding:13px;font-size:15px;border-radius:14px;margin-top:4px;">
      <i class="fa-solid fa-right-to-bracket"></i>
      Masuk ke Dashboard
    </button>
  </form>

  <div class="auth-links">
    <a href="forgot_password.php"><i class="fa-solid fa-key" style="font-size:11px;"></i> Lupa Password?</a>
    <a href="register.php"><i class="fa-solid fa-user-plus" style="font-size:11px;"></i> Daftar Admin Baru</a>
  </div>

  <div style="text-align:center;margin-top:18px;padding-top:16px;border-top:1px solid #F1F5F9;">
    <a href="../index.php" style="font-size:12px;color:#94A3B8;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;">
      <i class="fa-solid fa-arrow-left text-xs"></i>
      Kembali ke halaman utama
    </a>
  </div>
</div>

<script>
function togglePw(){
  var i=document.getElementById('pwInput');
  var ic=document.getElementById('pwEyeIcon');
  if(i.type==='password'){i.type='text';ic.className='fa-solid fa-eye-slash';}
  else{i.type='password';ic.className='fa-solid fa-eye';}
}
</script>
</body>
</html>
