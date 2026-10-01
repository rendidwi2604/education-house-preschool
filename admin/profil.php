<?php
require __DIR__ . '/includes/auth.php';

$stmt = $pdo->prepare('SELECT id, username, nama, foto, created_at FROM admin WHERE id = ?');
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

if (!$admin) { session_destroy(); redirect('/admin/login.php'); exit; }

$page_title = 'Profil Saya';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $foto = $admin['foto'];

    if ($nama === '') {
        $error = 'Nama wajib diisi.';
    } elseif (!empty($_FILES['foto']['name'])) {
        $izin = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $izin, true)) {
            $error = 'Format foto harus JPG, PNG, atau WEBP.';
        } elseif ($_FILES['foto']['size'] > 3 * 1024 * 1024) {
            $error = 'Ukuran foto maksimal 3MB.';
        } elseif (!is_uploaded_file($_FILES['foto']['tmp_name'])) {
            $error = 'Foto gagal diunggah.';
        } else {
            $folder = __DIR__ . '/../assets/uploads/admin/';
            if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
                $error = 'Folder foto profil tidak dapat dibuat.';
            } else {
                $fotoBaru = 'admin_' . $admin['id'] . '_' . time() . '.' . $ext;
                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $fotoBaru)) {
                    $error = 'Foto gagal disimpan.';
                } else {
                    $foto = $fotoBaru;
                    if ($admin['foto'] && is_file($folder . $admin['foto'])) unlink($folder . $admin['foto']);
                }
            }
        }
    }

    if ($error === '') {
        $pdo->prepare('UPDATE admin SET nama = ?, foto = ? WHERE id = ?')->execute([$nama, $foto, $admin['id']]);
        $_SESSION['admin_nama'] = $nama;
        $_SESSION['admin_foto'] = $foto;
        $admin['nama'] = $nama;
        $admin['foto'] = $foto;
        $success = 'Profil berhasil diperbarui.';
    }
}

require __DIR__ . '/includes/admin_header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= h($success) ?></div>
<?php endif; ?>

<div class="profile-layout">

  <!-- Left: Profile card -->
  <div>
    <div class="panel profile-card">
      <?php if ($admin['foto']): ?>
      <img class="profile-photo" src="../assets/uploads/admin/<?= h($admin['foto']) ?>" alt="<?= h($admin['nama']) ?>">
      <?php else: ?>
      <div class="profile-placeholder">
        <i class="fa-solid fa-user" style="color:#7952D9;font-size:36px;"></i>
      </div>
      <?php endif; ?>
      <h2 style="font-family:'Quicksand',sans-serif;font-size:18px;font-weight:900;color:#1E1B4B;margin:0 0 4px;"><?= h($admin['nama']) ?></h2>
      <p style="font-size:13px;color:#94A3B8;font-weight:600;margin:0 0 14px;">@<?= h($admin['username']) ?></p>
      <span style="display:inline-flex;align-items:center;gap:5px;background:#EDE9FE;color:#7952D9;font-size:12px;font-weight:700;padding:4px 12px;border-radius:999px;">
        <i class="fa-solid fa-shield-halved text-xs"></i> Administrator
      </span>
      <div style="margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9;font-size:12px;color:#94A3B8;font-weight:600;">
        <i class="fa-solid fa-calendar-days" style="margin-right:4px;"></i>
        Bergabung: <?= tgl($admin['created_at']) ?>
      </div>
    </div>

    <div class="panel" style="background:linear-gradient(135deg,#EDE9FE,#DBEAFE);border-color:#C4B5FD;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
        <div style="width:36px;height:36px;border-radius:10px;background:#7952D9;display:flex;align-items:center;justify-content:center;">
          <i class="fa-solid fa-shield-halved" style="color:#fff;font-size:16px;"></i>
        </div>
        <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:14px;color:#1E1B4B;">Keamanan Akun</div>
      </div>
      <p style="font-size:12.5px;color:#475569;font-weight:500;line-height:1.6;margin:0 0 12px;">Untuk mengubah password, gunakan halaman lupa sandi dengan kode lembaga.</p>
      <a href="forgot_password.php" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;">
        <i class="fa-solid fa-key"></i> Ubah Password
      </a>
    </div>
  </div>

  <!-- Right: Edit form -->
  <div class="panel">
    <div class="panel-head">
      <h3><i class="fa-solid fa-pen-to-square"></i> Edit Profil</h3>
    </div>
    <form method="post" enctype="multipart/form-data">

      <!-- Photo upload area -->
      <div style="display:flex;align-items:center;gap:18px;margin-bottom:22px;padding:16px;background:#F8FAFC;border-radius:14px;border:1.5px solid #E8ECF4;">
        <div style="position:relative;flex-shrink:0;">
          <?php if ($admin['foto']): ?>
          <img src="../assets/uploads/admin/<?= h($admin['foto']) ?>" id="profilePreview"
               style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #7952D9;">
          <?php else: ?>
          <div id="profilePlaceholder" style="width:72px;height:72px;border-radius:50%;background:#EDE9FE;border:3px solid #7952D9;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-user" style="color:#7952D9;font-size:28px;"></i>
          </div>
          <img id="profilePreview" src="" style="display:none;width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #7952D9;">
          <?php endif; ?>
        </div>
        <div style="flex:1;">
          <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:13.5px;color:#1E1B4B;margin-bottom:6px;">Foto Profil</div>
          <label for="fotoInput"
                 style="display:inline-flex;align-items:center;gap:6px;background:#7952D9;color:#fff;font-size:12.5px;font-weight:700;padding:7px 14px;border-radius:10px;cursor:pointer;transition:filter .15s;"
                 onmouseenter="this.style.filter='brightness(1.1)'" onmouseleave="this.style.filter='brightness(1)'">
            <i class="fa-solid fa-camera"></i> Ganti Foto
          </label>
          <input type="file" id="fotoInput" name="foto" accept=".jpg,.jpeg,.png,.webp"
                 style="display:none;" onchange="previewProfile(this)">
          <span class="form-help" style="display:block;margin-top:4px;">JPG, PNG, WEBP. Maks 3MB.</span>
        </div>
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-user" style="color:#7952D9;"></i> Nama Lengkap</label>
        <input type="text" name="nama" value="<?= h($admin['nama']) ?>" required>
      </div>
      <div class="form-group">
        <label><i class="fa-solid fa-at" style="color:#94A3B8;"></i> Username</label>
        <input type="text" value="<?= h($admin['username']) ?>" readonly
               style="background:#F8FAFC;color:#94A3B8;cursor:not-allowed;">
        <span class="form-help">Username tidak dapat diubah.</span>
      </div>
      <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
      </button>
    </form>
  </div>

</div>

<script>
function previewProfile(input){
  var preview=document.getElementById('profilePreview');
  var placeholder=document.getElementById('profilePlaceholder');
  if(input.files&&input.files[0]){
    var reader=new FileReader();
    reader.onload=function(e){
      preview.src=e.target.result;
      preview.style.display='block';
      if(placeholder) placeholder.style.display='none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
