<?php
require 'config/db.php';
require 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM guru WHERE id = ?');
$stmt->execute([$id]);
$guru = $stmt->fetch();

$page_title = $guru ? $guru['nama'] : 'Pengajar Tidak Ditemukan';
require 'includes/header.php';
?>

<main class="section">
  <div class="wrap">
    <?php if (!$guru): ?>
      <div class="panel teacher-detail">
        <h1>Pengajar tidak ditemukan</h1>
        <p>Profil pengajar yang Anda cari tidak tersedia.</p>
        <a class="btn btn-primary" href="index.php#pengajar">Kembali ke Pengajar</a>
      </div>
    <?php else: ?>
      <article class="panel teacher-detail">
        <?php if ($guru['foto']): ?>
          <img class="teacher-detail-photo" src="assets/uploads/guru/<?= h($guru['foto']) ?>" alt="Foto <?= h($guru['nama']) ?>">
        <?php else: ?>
          <div class="teacher-detail-placeholder">👩‍🏫</div>
        <?php endif; ?>
        <div class="teacher-detail-content">
          <h1><?= h($guru['nama']) ?></h1>
          <span class="teacher-detail-role"><?= h($guru['jabatan']) ?></span>
          <div class="teacher-facts">
            <?php if (!empty($guru['bidang'])): ?><div><strong>Bidang:</strong> <?= h($guru['bidang']) ?></div><?php endif; ?>
            <?php if (!empty($guru['pendidikan'])): ?><div><strong>Pendidikan:</strong> <?= h($guru['pendidikan']) ?></div><?php endif; ?>
            <?php if (!empty($guru['pengalaman'])): ?><div><strong>Pengalaman:</strong> <?= h($guru['pengalaman']) ?></div><?php endif; ?>
          </div>
          <?php if (!empty($guru['bio'])): ?>
            <p><?= nl2br(h($guru['bio'])) ?></p>
          <?php else: ?>
            <p>Pengajar di Education House Preschool.</p>
          <?php endif; ?>
          <a class="btn btn-ghost" href="index.php#pengajar">Kembali ke Pengajar</a>
        </div>
      </article>
    <?php endif; ?>
  </div>
</main>

<?php require 'includes/footer.php'; ?>
