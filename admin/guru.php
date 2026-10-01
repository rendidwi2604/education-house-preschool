<?php
require __DIR__ . '/includes/auth.php';
$page_title = 'Guru & Staf';

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $item = $pdo->prepare("SELECT foto FROM guru WHERE id = ?");
    $item->execute([$id]);
    $row = $item->fetch();
    if ($row && $row['foto'] && file_exists(__DIR__ . '/../assets/uploads/guru/' . $row['foto'])) {
        unlink(__DIR__ . '/../assets/uploads/guru/' . $row['foto']);
    }
    $pdo->prepare("DELETE FROM guru WHERE id = ?")->execute([$id]);
    
        $inner = <?php
require __DIR__ . '/includes/auth.php';
$page_title = 'Guru & Staf';

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $item = $pdo->prepare("SELECT foto FROM guru WHERE id = ?");
    $item->execute([$id]);
    $row = $item->fetch();
    if ($row && $row['foto'] && file_exists(__DIR__ . '/../assets/uploads/guru/' . $row['foto'])) {
        unlink(__DIR__ . '/../assets/uploads/guru/' . $row['foto']);
    }
    $pdo->prepare("DELETE FROM guru WHERE id = ?")->execute([$id]);
    redirect('/admin/guru.php?hapus_sukses=1');
    exit;
}

$daftar = $pdo->query("SELECT * FROM guru ORDER BY created_at ASC")->fetchAll();
require __DIR__ . '/includes/admin_header.php';
?>

<?php if (isset($_GET['tersimpan'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Data guru berhasil disimpan.</div>
<?php endif; ?>
<?php if (isset($_GET['hapus_sukses'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Data guru berhasil dihapus.</div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <h3><i class="fa-solid fa-chalkboard-user"></i> Semua Guru & Staf</h3>
    <a class="btn btn-primary btn-sm" href="guru_form.php">
      <i class="fa-solid fa-user-plus"></i> Tambah Guru
    </a>
  </div>

  <?php if (count($daftar) === 0): ?>
  <div class="empty-note">
    <i class="fa-solid fa-chalkboard-user"></i>
    Belum ada data guru. Klik "Tambah Guru" untuk menambahkan.
  </div>
  <?php else: ?>

  <?php
  $colors = ['#7952D9','#F97316','#58A834','#2B8FE8','#EC4899','#F59E0B'];
  ?>

  <!-- Card grid view -->
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:8px;">
    <?php foreach ($daftar as $idx => $g):
      $c = $colors[$idx % count($colors)];
    ?>
    <div style="background:#FAFBFF;border:1.5px solid #E8ECF4;border-radius:18px;overflow:hidden;transition:box-shadow .18s,transform .18s;"
         onmouseenter="this.style.boxShadow='0 6px 20px rgba(0,0,0,.09)';this.style.transform='translateY(-3px)';"
         onmouseleave="this.style.boxShadow='none';this.style.transform='none';">
      <!-- Color top band -->
      <div style="height:6px;background:<?= $c ?>;"></div>
      <div style="padding:18px 16px;">
        <!-- Photo -->
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
          <?php if ($g['foto']): ?>
          <img src="../assets/uploads/guru/<?= h($g['foto']) ?>"
               style="width:50px;height:50px;border-radius:50%;object-fit:cover;border:2.5px solid <?= $c ?>;flex-shrink:0;">
          <?php else: ?>
          <div style="width:50px;height:50px;border-radius:50%;background:<?= $c ?>18;border:2.5px solid <?= $c ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-user" style="color:<?= $c ?>;font-size:20px;"></i>
          </div>
          <?php endif; ?>
          <div style="min-width:0;">
            <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:14px;color:#1E1B4B;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= h($g['nama']) ?></div>
            <span style="font-size:11px;font-weight:700;color:<?= $c ?>;background:<?= $c ?>15;padding:2px 8px;border-radius:6px;"><?= h($g['jabatan']) ?></span>
          </div>
        </div>
        <?php if (!empty($g['bidang'])): ?>
        <div style="font-size:12px;color:#6B7280;font-weight:600;display:flex;align-items:center;gap:5px;margin-bottom:10px;">
          <i class="fa-solid fa-book" style="color:<?= $c ?>;font-size:11px;"></i>
          <?= h($g['bidang']) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($g['bio'])): ?>
        <div style="font-size:12px;color:#9CA3AF;line-height:1.5;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
          <?= h($g['bio']) ?>
        </div>
        <?php endif; ?>
        <div style="display:flex;gap:6px;">
          <a class="link-edit" href="guru_form.php?id=<?= $g['id'] ?>" style="flex:1;text-align:center;">
            <i class="fa-solid fa-pen-to-square" style="font-size:11px;"></i> Edit
          </a>
          <a class="link-danger" href="guru.php?hapus=<?= $g['id'] ?>"
             onclick="return confirm('Hapus data guru <?= h(addslashes($g['nama'])) ?>?')" style="flex:1;text-align:center;">
            <i class="fa-solid fa-trash" style="font-size:11px;"></i> Hapus
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
.Groups[1].Value
        # Jika path sudah mulai dengan / biarkan, kalau tidak tambah /admin/
        if ($inner -match '^/') {
            "redirect('$inner')"
        } else {
            "redirect('/admin/$inner')"
        }
    ;
    exit;
}

$daftar = $pdo->query("SELECT * FROM guru ORDER BY created_at ASC")->fetchAll();
require __DIR__ . '/includes/admin_header.php';
?>

<?php if (isset($_GET['tersimpan'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Data guru berhasil disimpan.</div>
<?php endif; ?>
<?php if (isset($_GET['hapus_sukses'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Data guru berhasil dihapus.</div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <h3><i class="fa-solid fa-chalkboard-user"></i> Semua Guru & Staf</h3>
    <a class="btn btn-primary btn-sm" href="guru_form.php">
      <i class="fa-solid fa-user-plus"></i> Tambah Guru
    </a>
  </div>

  <?php if (count($daftar) === 0): ?>
  <div class="empty-note">
    <i class="fa-solid fa-chalkboard-user"></i>
    Belum ada data guru. Klik "Tambah Guru" untuk menambahkan.
  </div>
  <?php else: ?>

  <?php
  $colors = ['#7952D9','#F97316','#58A834','#2B8FE8','#EC4899','#F59E0B'];
  ?>

  <!-- Card grid view -->
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:8px;">
    <?php foreach ($daftar as $idx => $g):
      $c = $colors[$idx % count($colors)];
    ?>
    <div style="background:#FAFBFF;border:1.5px solid #E8ECF4;border-radius:18px;overflow:hidden;transition:box-shadow .18s,transform .18s;"
         onmouseenter="this.style.boxShadow='0 6px 20px rgba(0,0,0,.09)';this.style.transform='translateY(-3px)';"
         onmouseleave="this.style.boxShadow='none';this.style.transform='none';">
      <!-- Color top band -->
      <div style="height:6px;background:<?= $c ?>;"></div>
      <div style="padding:18px 16px;">
        <!-- Photo -->
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
          <?php if ($g['foto']): ?>
          <img src="../assets/uploads/guru/<?= h($g['foto']) ?>"
               style="width:50px;height:50px;border-radius:50%;object-fit:cover;border:2.5px solid <?= $c ?>;flex-shrink:0;">
          <?php else: ?>
          <div style="width:50px;height:50px;border-radius:50%;background:<?= $c ?>18;border:2.5px solid <?= $c ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-user" style="color:<?= $c ?>;font-size:20px;"></i>
          </div>
          <?php endif; ?>
          <div style="min-width:0;">
            <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:14px;color:#1E1B4B;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= h($g['nama']) ?></div>
            <span style="font-size:11px;font-weight:700;color:<?= $c ?>;background:<?= $c ?>15;padding:2px 8px;border-radius:6px;"><?= h($g['jabatan']) ?></span>
          </div>
        </div>
        <?php if (!empty($g['bidang'])): ?>
        <div style="font-size:12px;color:#6B7280;font-weight:600;display:flex;align-items:center;gap:5px;margin-bottom:10px;">
          <i class="fa-solid fa-book" style="color:<?= $c ?>;font-size:11px;"></i>
          <?= h($g['bidang']) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($g['bio'])): ?>
        <div style="font-size:12px;color:#9CA3AF;line-height:1.5;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
          <?= h($g['bio']) ?>
        </div>
        <?php endif; ?>
        <div style="display:flex;gap:6px;">
          <a class="link-edit" href="guru_form.php?id=<?= $g['id'] ?>" style="flex:1;text-align:center;">
            <i class="fa-solid fa-pen-to-square" style="font-size:11px;"></i> Edit
          </a>
          <a class="link-danger" href="guru.php?hapus=<?= $g['id'] ?>"
             onclick="return confirm('Hapus data guru <?= h(addslashes($g['nama'])) ?>?')" style="flex:1;text-align:center;">
            <i class="fa-solid fa-trash" style="font-size:11px;"></i> Hapus
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
