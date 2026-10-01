<?php
require 'config/db.php';
require 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ? AND status = 'terbit'");
$stmt->execute([$id]);
$berita = $stmt->fetch();

if (!$berita) {
    http_response_code(404);
    $page_title = 'Berita Tidak Ditemukan';
} else {
    $page_title = $berita['judul'];
}

require 'includes/header.php';
?>

<main class="section">
  <div class="wrap">
    <?php if (!$berita): ?>
      <div class="panel news-detail">
        <h1>Berita tidak ditemukan</h1>
        <p>Berita yang Anda cari tidak tersedia atau sudah tidak diterbitkan.</p>
        <a class="btn btn-primary" href="index.php#berita">Kembali ke Berita</a>
      </div>
    <?php else: ?>
      <article class="panel news-detail">
        <span class="badge"><?= h($berita['kategori']) ?></span>
        <h1><?= h($berita['judul']) ?></h1>
        <small class="news-date"><?= tgl($berita['created_at']) ?></small>
        <?php if ($berita['gambar']): ?>
          <img class="news-detail-image" src="assets/uploads/galeri/<?= h($berita['gambar']) ?>" alt="<?= h($berita['judul']) ?>">
        <?php endif; ?>
        <div class="news-content"><?= nl2br(h($berita['isi'])) ?></div>
        <a class="btn btn-ghost" href="index.php#berita">Kembali ke Berita</a>
      </article>
    <?php endif; ?>
  </div>
</main>

<?php require 'includes/footer.php'; ?>
