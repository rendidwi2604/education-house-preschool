<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($page_title ?? 'Admin') ?> — Admin Education House</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css?v=20260930-1">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/sidebar.php'; ?>

  <!-- Mobile backdrop -->
  <div id="mob-backdrop" onclick="closeSidebar()"
       style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:35;"></div>

  <div class="main">
    <!-- TOPBAR -->
    <div class="topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <!-- Mobile hamburger -->
        <button id="mob-menu-btn" onclick="openSidebar()"
                style="display:none;width:36px;height:36px;border-radius:10px;border:1.5px solid #E8ECF4;background:#fff;cursor:pointer;align-items:center;justify-content:center;color:#6B7280;font-size:16px;">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">
          <?php
          $icons = [
            'Dashboard'           => ['fa-gauge-high',    '#58A834'],
            'Berita & Pengumuman' => ['fa-newspaper',     '#D98B55'],
            'Tambah Berita'       => ['fa-circle-plus',   '#D98B55'],
            'Edit Berita'         => ['fa-pen-to-square', '#D98B55'],
            'Galeri Kegiatan'     => ['fa-images',        '#69BD45'],
            'Slider Hero'         => ['fa-film',          '#2B8FE8'],
            'Tambah Slide'        => ['fa-circle-plus',   '#2B8FE8'],
            'Edit Slide'          => ['fa-pen-to-square', '#2B8FE8'],
            'Data Pendaftar'      => ['fa-clipboard-list','#2B8FE8'],
            'Guru & Staf'         => ['fa-chalkboard-user','#58A834'],
            'Tambah Guru'         => ['fa-circle-plus',   '#58A834'],
            'Edit Guru'           => ['fa-pen-to-square', '#58A834'],
            'Profil Saya'         => ['fa-circle-user',   '#328C39'],
          ];
          $cur = $page_title ?? 'Dashboard';
          $ico = $icons[$cur] ?? ['fa-circle', '#328C39'];
          ?>
          <span class="title-icon" style="background:<?= $ico[1] ?>22;color:<?= $ico[1] ?>;">
            <i class="fa-solid <?= $ico[0] ?>"></i>
          </span>
          <?= h($cur) ?>
        </div>
      </div>

      <div class="topbar-actions">
        <!-- Quick link to site -->
        <a href="../index.php" target="_blank"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 13px;border-radius:10px;border:1.5px solid #E8ECF4;font-size:12.5px;font-weight:700;color:#6B7280;text-decoration:none;transition:border-color .15s;"
           onmouseenter="this.style.borderColor='#328C39';this.style.color='#328C39'"
           onmouseleave="this.style.borderColor='#E8ECF4';this.style.color='#6B7280'">
          <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
          Lihat Website
        </a>

        <!-- Admin user -->
        <a class="admin-user" href="profil.php">
          <?php if (!empty($_SESSION['admin_foto'])): ?>
            <img src="../assets/uploads/admin/<?= h($_SESSION['admin_foto']) ?>" alt="Foto profil">
          <?php else: ?>
            <span class="admin-avatar" style="background:#EAF5E8;display:flex;align-items:center;justify-content:center;">
              <i class="fa-solid fa-user" style="color:#328C39;font-size:14px;"></i>
            </span>
          <?php endif; ?>
          <span><?= h($_SESSION['admin_nama'] ?? 'Admin') ?></span>
          <i class="fa-solid fa-chevron-down" style="font-size:10px;color:#94A3B8;"></i>
        </a>
      </div>
    </div>

    <!-- BREADCRUMB -->
    <div style="padding:10px 24px 0;display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#94A3B8;">
      <a href="dashboard.php" style="color:#94A3B8;text-decoration:none;" onmouseenter="this.style.color='#328C39'" onmouseleave="this.style.color='#94A3B8'">
        <i class="fa-solid fa-house text-xs"></i>
      </a>
      <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
      <span style="color:#374151;"><?= h($page_title ?? 'Dashboard') ?></span>
    </div>

    <div class="content">
