<?php
require __DIR__ . '/includes/auth.php';
$page_title = 'Berita & Pengumuman';

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $item = $pdo->prepare("SELECT gambar FROM berita WHERE id = ?");
    $item->execute([$id]);
    $row = $item->fetch();
    if ($row && $row['gambar'] && file_exists(__DIR__ . '/../assets/uploads/galeri/' . $row['gambar'])) {
        unlink(__DIR__ . '/../assets/uploads/galeri/' . $row['gambar']);
    }
    $pdo->prepare("DELETE FROM berita WHERE id = ?")->execute([$id]);
    redirect('/admin/berita.php?hapus_sukses=1');
    exit;
}

$daftar = $pdo->query("SELECT * FROM berita ORDER BY created_at DESC")->fetchAll();
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
      <th></th>
      <th>Judul</th>
      <th>Kategori</th>
      <th>Tanggal</th>
      <th>Status</th>
      <th style="text-align:right;">Aksi</th>
    </tr>
    <?php foreach ($daftar as $b): ?>
    <tr>
      <td style="width:52px;">
        <?php if ($b['gambar']): ?>
        <img class="thumb" src="../assets/uploads/galeri/<?= h($b['gambar']) ?>" alt="">
        <?php else: ?>
        <div class="thumb" style="display:flex;align-items:center;justify-content:center;background:#EEF2FF;">
          <i class="fa-solid fa-image" style="color:#C7D2FE;font-size:16px;"></i>
        </div>
        <?php endif; ?>
      </td>
      <td style="font-weight:600;max-width:220px;">
        <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= h($b['judul']) ?></div>
      </td>
      <td><span style="font-size:11.5px;background:#EEF2FF;color:#4F46E5;font-weight:700;padding:2px 9px;border-radius:7px;"><?= h($b['kategori']) ?></span></td>
      <td style="color:#6B7280;font-size:12.5px;white-space:nowrap;"><?= tgl($b['created_at']) ?></td>
      <td><span class="tag <?= $b['status'] === 'terbit' ? 'ok' : 'draft' ?>"><?= h(ucfirst($b['status'])) ?></span></td>
      <td style="text-align:right;white-space:nowrap;">
        <a class="link-edit" href="berita_form.php?id=<?= $b['id'] ?>">
          <i class="fa-solid fa-pen-to-square" style="font-size:11px;"></i> Edit
        </a>
        <a class="link-danger" href="berita.php?hapus=<?= $b['id'] ?>"
           onclick="return confirm('Hapus berita \'<?= h(addslashes($b['judul'])) ?>\'?')">
          <i class="fa-solid fa-trash" style="font-size:11px;"></i> Hapus
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
