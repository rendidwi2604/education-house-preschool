<?php
require __DIR__ . '/includes/auth.php';
$page_title = 'Berita & Pengumuman';

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    // Hapus gambar dari berita_gambar (cascade via FK)
    $pdo->prepare("DELETE FROM berita WHERE id = ?")->execute([$id]);
    redirect('/admin/berita.php?hapus_sukses=1');
}

// Ambil berita + jumlah gambar per berita
$daftar = $pdo->query("
    SELECT b.*,
           COUNT(bg.id) AS jumlah_gambar,
           MIN(bg.url)  AS gambar_pertama
    FROM berita b
    LEFT JOIN berita_gambar bg ON bg.berita_id = b.id
    GROUP BY b.id
    ORDER BY b.created_at DESC
")->fetchAll();

require __DIR__ . '/includes/admin_header.php';
?>

<?php if (isset($_GET['tersimpan'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Berita berhasil disimpan.</div>
<?php endif; ?>
<?php if (isset($_GET['hapus_sukses'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Berita berhasil dihapus.</div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <h3><i class="fa-solid fa-newspaper"></i> Semua Berita & Pengumuman</h3>
    <a class="btn btn-primary btn-sm" href="berita_form.php">
      <i class="fa-solid fa-circle-plus"></i> Tambah Berita
    </a>
  </div>

  <?php if (count($daftar) === 0): ?>
  <div class="empty-note">
    <i class="fa-regular fa-newspaper"></i>
    Belum ada berita. Klik "Tambah Berita" untuk membuat yang pertama.
  </div>
  <?php else: ?>
  <table>
    <tr>
      <th style="width:72px;">Gambar</th>
      <th>Judul</th>
      <th>Kategori</th>
      <th>Tanggal</th>
      <th>Status</th>
      <th style="text-align:right;">Aksi</th>
    </tr>
    <?php foreach ($daftar as $b): ?>
    <?php
      // Resolve URL thumbnail
      $thumb = $b['gambar_pertama'] ?? null;
      $thumbUrl = '';
      if ($thumb) {
          if (str_starts_with($thumb, 'https://') || str_starts_with($thumb, 'http://')) {
              $thumbUrl = $thumb;
          } else {
              $thumbUrl = '../' . ltrim($thumb, '/');
          }
      }
      $jmlGambar = (int) $b['jumlah_gambar'];
    ?>
    <tr>
      <td>
        <?php if ($thumbUrl): ?>
        <div style="position:relative;width:56px;height:56px;">
          <img src="<?= h($thumbUrl) ?>" alt=""
               style="width:56px;height:56px;border-radius:10px;object-fit:cover;border:2px solid #E8ECF4;"
               loading="lazy">
          <?php if ($jmlGambar > 1): ?>
          <span style="position:absolute;bottom:-4px;right:-4px;background:#328C39;color:#fff;font-size:9px;font-weight:800;padding:1px 5px;border-radius:5px;line-height:1.4;">
            +<?= $jmlGambar ?>
          </span>
          <?php endif; ?>
        </div>
        <?php else: ?>
        <div style="width:56px;height:56px;border-radius:10px;background:#EEF2FF;border:2px solid #E8ECF4;display:flex;align-items:center;justify-content:center;">
          <i class="fa-solid fa-image" style="color:#C7D2FE;font-size:18px;"></i>
        </div>
        <?php endif; ?>
      </td>
      <td style="font-weight:600;max-width:220px;">
        <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= h($b['judul']) ?></div>
        <?php if ($jmlGambar > 0): ?>
        <div style="font-size:11px;color:#94A3B8;font-weight:600;margin-top:2px;">
          <i class="fa-solid fa-images" style="font-size:10px;"></i> <?= $jmlGambar ?> gambar
        </div>
        <?php endif; ?>
      </td>
      <td><span style="font-size:11.5px;background:#EEF2FF;color:#4F46E5;font-weight:700;padding:2px 9px;border-radius:7px;"><?= h($b['kategori']) ?></span></td>
      <td style="color:#6B7280;font-size:12.5px;white-space:nowrap;"><?= tgl($b['created_at']) ?></td>
      <td><span class="tag <?= $b['status'] === 'terbit' ? 'ok' : 'draft' ?>"><?= h(ucfirst($b['status'])) ?></span></td>
      <td style="text-align:right;white-space:nowrap;">
        <a class="link-edit" href="berita_form.php?id=<?= $b['id'] ?>">
          <i class="fa-solid fa-pen-to-square" style="font-size:11px;"></i> Edit
        </a>
        <a class="link-danger" href="berita.php?hapus=<?= $b['id'] ?>"
           onclick="return confirm('Hapus berita ini beserta semua gambarnya?')">
          <i class="fa-solid fa-trash" style="font-size:11px;"></i> Hapus
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
