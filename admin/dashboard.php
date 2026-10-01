<?php
require __DIR__ . '/includes/auth.php';
$page_title = 'Dashboard';

$jml_pendaftar = $pdo->query("SELECT COUNT(*) FROM pendaftar")->fetchColumn();
$jml_baru      = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'Baru'")->fetchColumn();
$jml_berita    = $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
$jml_guru      = $pdo->query("SELECT COUNT(*) FROM guru")->fetchColumn();

$pendaftar_terbaru = $pdo->query("SELECT * FROM pendaftar ORDER BY created_at DESC LIMIT 5")->fetchAll();
$berita_terbaru    = $pdo->query("SELECT * FROM berita ORDER BY created_at DESC LIMIT 5")->fetchAll();

require __DIR__ . '/includes/admin_header.php';
?>

<!-- Dashboard Hero -->
<div class="dashboard-hero">
  <!-- decorative circles -->
  <div style="position:absolute;top:-30px;right:120px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,.06);pointer-events:none;"></div>
  <div style="position:absolute;bottom:-40px;right:40px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>

  <div style="position:relative;">
    <span class="eyebrow">
      <i class="fa-solid fa-sparkles" style="margin-right:5px;"></i>
      Panel Administrasi · <?= date('l, d F Y') ?>
    </span>
    <h2>Selamat datang, <?= h($_SESSION['admin_nama'] ?? 'Admin') ?>! 👋</h2>
    <p>Kelola semua konten Education House Preschool dari satu tempat.</p>

    <!-- quick stat pills -->
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:16px;">
      <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.18);color:#fff;font-size:12px;font-weight:700;padding:5px 12px;border-radius:999px;">
        <i class="fa-solid fa-user-plus text-xs"></i>
        <?= $jml_baru ?> pendaftar baru
      </span>
      <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.18);color:#fff;font-size:12px;font-weight:700;padding:5px 12px;border-radius:999px;">
        <i class="fa-solid fa-newspaper text-xs"></i>
        <?= $jml_berita ?> berita
      </span>
    </div>
  </div>

  <div style="display:flex;gap:10px;flex-shrink:0;position:relative;">
    <a class="btn btn-orange" href="berita_form.php" style="white-space:nowrap;">
      <i class="fa-solid fa-circle-plus"></i> Tambah Berita
    </a>
    <a class="btn" href="pendaftar.php"
       style="background:rgba(255,255,255,.2);color:#fff;border:1.5px solid rgba(255,255,255,.35);white-space:nowrap;">
      <i class="fa-solid fa-clipboard-list"></i> Pendaftar
    </a>
  </div>
</div>

<!-- Stat Cards -->
<div class="stat-row">
  <div class="stat">
    <div class="stat-icon" style="background:linear-gradient(135deg,#2F7D32,#58A834);">
      <i class="fa-solid fa-users"></i>
    </div>
    <div>
      <div class="num"><?= $jml_pendaftar ?></div>
      <div class="lbl">Total Pendaftar</div>
    </div>
  </div>
  <div class="stat">
    <div class="stat-icon" style="background:linear-gradient(135deg,#F97316,#FB923C);">
      <i class="fa-solid fa-user-plus"></i>
    </div>
    <div>
      <div class="num" style="color:#EA580C;"><?= $jml_baru ?></div>
      <div class="lbl">Pendaftar Baru</div>
    </div>
  </div>
  <div class="stat">
    <div class="stat-icon" style="background:linear-gradient(135deg,#58A834,#F97316);">
      <i class="fa-solid fa-newspaper"></i>
    </div>
    <div>
      <div class="num"><?= $jml_berita ?></div>
      <div class="lbl">Total Berita</div>
    </div>
  </div>
  <div class="stat">
    <div class="stat-icon" style="background:linear-gradient(135deg,#468926,#58A834);">
      <i class="fa-solid fa-chalkboard-user"></i>
    </div>
    <div>
      <div class="num"><?= $jml_guru ?></div>
      <div class="lbl">Guru & Staf</div>
    </div>
  </div>
</div>

<!-- Quick Action Grid -->
<div class="dashboard-quick-actions">
  <?php
  $actions = [
    ['berita_form.php','fa-circle-plus','#D98B55','rgba(217,139,85,.12)','rgba(217,139,85,.35)','Tambah Berita','Buat artikel baru'],
    ['galeri.php','fa-photo-film','#328C39','rgba(50,140,57,.1)','rgba(50,140,57,.3)','Galeri & Instagram','Foto dan video kegiatan'],
    ['guru_form.php','fa-user-plus','#2B8FE8','rgba(43,143,232,.1)','rgba(43,143,232,.3)','Tambah Guru','Profil pengajar baru'],
    ['pendaftar.php','fa-clipboard-list','#69BD45','rgba(105,189,69,.12)','rgba(105,189,69,.35)','Data Pendaftar','Kelola PPDB'],
  ];
  foreach ($actions as [$href,$icon,$color,$tint,$hover,$title,$sub]):
  ?>
  <a href="<?= $href ?>"
     style="display:flex;align-items:center;gap:12px;background:#fff;border:1.5px solid #E8ECF4;border-radius:16px;padding:14px 16px;text-decoration:none;transition:box-shadow .18s,transform .18s,border-color .18s;"
    onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)';this.style.transform='translateY(-2px)';this.style.borderColor='<?= $hover ?>';"
     onmouseleave="this.style.boxShadow='none';this.style.transform='none';this.style.borderColor='#E8ECF4';">
    <div style="width:40px;height:40px;border-radius:12px;background:<?= $tint ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <i class="fa-solid <?= $icon ?>" style="color:<?= $color ?>;font-size:16px;"></i>
    </div>
    <div>
      <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:13px;color:#1E1B4B;"><?= $title ?></div>
      <div style="font-size:11.5px;color:#94A3B8;font-weight:500;"><?= $sub ?></div>
    </div>
  </a>
  <?php endforeach; ?>
</div>

<!-- Tables -->
<div class="dashboard-columns">

  <!-- Pendaftar terbaru -->
  <div class="panel">
    <div class="panel-head">
      <h3><i class="fa-solid fa-clipboard-list"></i> Pendaftar Terbaru</h3>
      <a class="btn btn-ghost btn-sm" href="pendaftar.php">Lihat semua <i class="fa-solid fa-arrow-right text-xs"></i></a>
    </div>
    <?php if (count($pendaftar_terbaru) === 0): ?>
    <div class="empty-note">
      <i class="fa-solid fa-clipboard-list"></i>
      Belum ada pendaftar.
    </div>
    <?php else: ?>
    <table>
      <tr><th>Nama Anak</th><th>Orang Tua</th><th>Tanggal</th><th>Status</th></tr>
      <?php foreach ($pendaftar_terbaru as $p): ?>
      <tr>
        <td style="font-weight:600;"><?= h($p['nama_anak']) ?></td>
        <td style="color:#6B7280;"><?= h($p['nama_ortu']) ?></td>
        <td style="color:#6B7280;font-size:12.5px;"><?= tgl($p['created_at']) ?></td>
        <td><span class="tag status-<?= h($p['status']) ?>"><?= h($p['status']) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </div>

  <!-- Berita terbaru -->
  <div class="panel">
    <div class="panel-head">
      <h3><i class="fa-solid fa-newspaper"></i> Berita Terbaru</h3>
      <a class="btn btn-ghost btn-sm" href="berita.php">Lihat semua <i class="fa-solid fa-arrow-right text-xs"></i></a>
    </div>
    <?php if (count($berita_terbaru) === 0): ?>
    <div class="empty-note">
      <i class="fa-solid fa-newspaper"></i>
      Belum ada berita.
    </div>
    <?php else: ?>
    <table>
      <tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th></tr>
      <?php foreach ($berita_terbaru as $b): ?>
      <tr>
        <td style="font-weight:600;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= h($b['judul']) ?></td>
        <td><span style="font-size:11.5px;background:#EEF2FF;color:#4F46E5;font-weight:700;padding:2px 8px;border-radius:6px;"><?= h($b['kategori']) ?></span></td>
        <td style="color:#6B7280;font-size:12.5px;"><?= tgl($b['created_at']) ?></td>
        <td><span class="tag <?= $b['status'] === 'terbit' ? 'ok' : 'draft' ?>"><?= h(ucfirst($b['status'])) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </div>

</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
