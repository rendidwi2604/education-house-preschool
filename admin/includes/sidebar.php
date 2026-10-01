<?php $current = basename($_SERVER['SCRIPT_NAME']); ?>
<?php $contentSection = $_GET['bagian'] ?? ''; ?>

<aside class="side" id="sidebar">
  <!-- Brand -->
  <a href="dashboard.php" class="brand">
    <img src="../assets/img/Logo_EduHouse.png" alt="Education House Logo"
         style="height:34px;width:auto;object-fit:contain;flex-shrink:0;filter:drop-shadow(0 1px 4px rgba(0,0,0,.3));">
    <div>
      <span class="brand-text">Education House</span>
      <span class="brand-sub">Admin Panel</span>
    </div>
  </a>

  <!-- Admin mini profile -->
  <div style="padding:12px 14px;margin:8px;background:rgba(255,255,255,.07);border-radius:13px;display:flex;align-items:center;gap:10px;">
    <?php if (!empty($_SESSION['admin_foto'])): ?>
      <img src="../assets/uploads/admin/<?= h($_SESSION['admin_foto']) ?>"
           style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.25);flex-shrink:0;">
    <?php else: ?>
      <div style="width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fa-solid fa-user" style="color:rgba(255,255,255,.6);font-size:14px;"></i>
      </div>
    <?php endif; ?>
    <div style="min-width:0;">
      <div style="font-size:12.5px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
        <?= h($_SESSION['admin_nama'] ?? 'Admin') ?>
      </div>
      <div style="font-size:10.5px;color:rgba(255,255,255,.4);font-weight:600;">Administrator</div>
    </div>
  </div>

  <!-- Nav -->
  <span class="side-section-label">Menu Utama</span>

  <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'on' : '' ?>">
    <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span>
    <span>Dashboard</span>
  </a>

  <a href="pendaftar.php" class="<?= $current === 'pendaftar.php' ? 'on' : '' ?>">
    <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
    <span>Data Pendaftar</span>
    <?php
    // Show count badge for new registrants
    if (isset($pdo)) {
      $n = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status='Baru'")->fetchColumn();
      if ($n > 0): ?>
      <span style="margin-left:auto;background:#EC4899;color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:999px;line-height:1.4;"><?= $n ?></span>
    <?php endif; } ?>
  </a>

  <div class="side-divider"></div>
  <span class="side-section-label">Konten</span>

  <a href="berita.php" class="<?= in_array($current, ['berita.php','berita_form.php']) ? 'on' : '' ?>">
    <span class="nav-icon"><i class="fa-solid fa-newspaper"></i></span>
    <span>Berita & Pengumuman</span>
  </a>

  <a href="slider.php" class="<?= in_array($current, ['slider.php','slider_form.php']) ? 'on' : '' ?>">
    <span class="nav-icon"><i class="fa-solid fa-film"></i></span>
    <span>Slider Hero</span>
  </a>

  <a href="galeri.php" class="<?= in_array($current, ['galeri.php','instagram.php']) ? 'on' : '' ?>">
    <span class="nav-icon"><i class="fa-solid fa-photo-film"></i></span>
    <span>Galeri & Instagram</span>
  </a>

  <a href="galeri.php?bagian=islami#kegiatanIslamiAdmin" class="<?= $current === 'galeri.php' && $contentSection === 'islami' ? 'on' : '' ?>">
    <span class="nav-icon"><i class="fa-solid fa-moon"></i></span>
    <span>Kegiatan Islami</span>
  </a>

  <a href="galeri.php?bagian=testimoni#testimoniAdmin" class="<?= $current === 'galeri.php' && $contentSection === 'testimoni' ? 'on' : '' ?>">
    <span class="nav-icon"><i class="fa-solid fa-video"></i></span>
    <span>Testimoni Orang Tua</span>
  </a>

  <a href="guru.php" class="<?= in_array($current, ['guru.php','guru_form.php']) ? 'on' : '' ?>">
    <span class="nav-icon"><i class="fa-solid fa-chalkboard-user"></i></span>
    <span>Guru & Staf</span>
  </a>

  <div class="side-divider"></div>
  <span class="side-section-label">Akun</span>

  <a href="profil.php" class="<?= $current === 'profil.php' ? 'on' : '' ?>">
    <span class="nav-icon"><i class="fa-solid fa-circle-user"></i></span>
    <span>Profil Saya</span>
  </a>

  <a href="../index.php" target="_blank">
    <span class="nav-icon"><i class="fa-solid fa-globe"></i></span>
    <span>Lihat Website</span>
    <i class="fa-solid fa-arrow-up-right-from-square" style="margin-left:auto;font-size:9px;opacity:.5;"></i>
  </a>

  <div class="spacer"></div>

  <div class="side-divider"></div>
  <a href="logout.php" onclick="return confirm('Yakin mau keluar dari panel admin?')"
     style="margin:4px 8px 14px !important;background:rgba(239,68,68,.12) !important;color:#FCA5A5 !important;">
    <span class="nav-icon" style="background:rgba(239,68,68,.2) !important;">
      <i class="fa-solid fa-right-from-bracket" style="color:#FCA5A5;"></i>
    </span>
    <span>Keluar</span>
  </a>
</aside>

<script>
function openSidebar(){
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('mob-backdrop').style.display='block';
  document.body.style.overflow='hidden';
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('mob-backdrop').style.display='none';
  document.body.style.overflow='';
}
// Show mobile menu btn on small screens
(function(){
  var btn = document.getElementById('mob-menu-btn');
  if(btn && window.innerWidth <= 768) btn.style.display='flex';
  window.addEventListener('resize', function(){
    if(!btn) return;
    btn.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
  });
})();
</script>
