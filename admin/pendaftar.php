<?php
require 'includes/auth.php';
$page_title = 'Data Pendaftar';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $status_valid = ['Baru','Diterima','Ditolak'];
    if (in_array($_POST['status'], $status_valid)) {
        $pdo->prepare("UPDATE pendaftar SET status = ? WHERE id = ?")->execute([$_POST['status'], (int)$_POST['id']]);
    }
    header('Location: pendaftar.php?update=1');
    exit;
}

if (isset($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM pendaftar WHERE id = ?")->execute([(int) $_GET['hapus']]);
    header('Location: pendaftar.php?hapus_sukses=1');
    exit;
}

$daftar = $pdo->query("SELECT * FROM pendaftar ORDER BY created_at DESC")->fetchAll();
$jml_baru     = count(array_filter($daftar, fn($p) => $p['status'] === 'Baru'));
$jml_diterima = count(array_filter($daftar, fn($p) => $p['status'] === 'Diterima'));
$jml_ditolak  = count(array_filter($daftar, fn($p) => $p['status'] === 'Ditolak'));

require 'includes/admin_header.php';
?>

<?php if (isset($_GET['update'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Status pendaftar diperbarui.</div>
<?php endif; ?>
<?php if (isset($_GET['hapus_sukses'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Data pendaftar dihapus.</div>
<?php endif; ?>

<!-- Mini stat pills -->
<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
  <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #E8ECF4;border-radius:12px;padding:10px 16px;">
    <div style="width:32px;height:32px;border-radius:9px;background:#DBEAFE;display:flex;align-items:center;justify-content:center;">
      <i class="fa-solid fa-users" style="color:#2B8FE8;font-size:14px;"></i>
    </div>
    <div>
      <div style="font-family:'Quicksand',sans-serif;font-weight:900;font-size:18px;color:#1E1B4B;line-height:1;"><?= count($daftar) ?></div>
      <div style="font-size:11px;color:#6B7280;font-weight:600;">Total</div>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #E8ECF4;border-radius:12px;padding:10px 16px;">
    <div style="width:32px;height:32px;border-radius:9px;background:#DBEAFE;display:flex;align-items:center;justify-content:center;">
      <i class="fa-solid fa-clock" style="color:#2B8FE8;font-size:14px;"></i>
    </div>
    <div>
      <div style="font-family:'Quicksand',sans-serif;font-weight:900;font-size:18px;color:#2B8FE8;line-height:1;"><?= $jml_baru ?></div>
      <div style="font-size:11px;color:#6B7280;font-weight:600;">Baru</div>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #E8ECF4;border-radius:12px;padding:10px 16px;">
    <div style="width:32px;height:32px;border-radius:9px;background:#DCFCE7;display:flex;align-items:center;justify-content:center;">
      <i class="fa-solid fa-circle-check" style="color:#16A34A;font-size:14px;"></i>
    </div>
    <div>
      <div style="font-family:'Quicksand',sans-serif;font-weight:900;font-size:18px;color:#16A34A;line-height:1;"><?= $jml_diterima ?></div>
      <div style="font-size:11px;color:#6B7280;font-weight:600;">Diterima</div>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #E8ECF4;border-radius:12px;padding:10px 16px;">
    <div style="width:32px;height:32px;border-radius:9px;background:#FEE2E2;display:flex;align-items:center;justify-content:center;">
      <i class="fa-solid fa-circle-xmark" style="color:#EF4444;font-size:14px;"></i>
    </div>
    <div>
      <div style="font-family:'Quicksand',sans-serif;font-weight:900;font-size:18px;color:#EF4444;line-height:1;"><?= $jml_ditolak ?></div>
      <div style="font-size:11px;color:#6B7280;font-weight:600;">Ditolak</div>
    </div>
  </div>
  <div style="margin-left:auto;">
    <a class="btn btn-success btn-sm" href="pendaftar_export.php">
      <i class="fa-solid fa-file-excel"></i> Unduh Excel
    </a>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h3><i class="fa-solid fa-clipboard-list"></i> Semua Pendaftar</h3>
  </div>
  <?php if (count($daftar) === 0): ?>
  <div class="empty-note">
    <i class="fa-solid fa-clipboard-list"></i>
    Belum ada yang mendaftar lewat formulir PPDB.
  </div>
  <?php else: ?>
  <div style="overflow-x:auto;">
  <table style="min-width:720px;">
    <tr>
      <th>#</th>
      <th>Nama Anak</th>
      <th>Usia</th>
      <th>Orang Tua</th>
      <th>Alamat</th>
      <th>WhatsApp</th>
      <th>Tanggal Daftar</th>
      <th>Status</th>
      <th style="text-align:right;">Aksi</th>
    </tr>
    <?php foreach ($daftar as $i => $p): ?>
    <tr>
      <td style="color:#94A3B8;font-size:12px;"><?= $i+1 ?></td>
      <td>
        <div style="font-weight:700;color:#1E1B4B;"><?= h($p['nama_anak']) ?></div>
      </td>
      <td>
        <span style="background:#F1F5F9;color:#374151;font-size:12px;font-weight:700;padding:2px 8px;border-radius:6px;">
          <?= h($p['usia_anak']) ?> th
        </span>
      </td>
      <td style="color:#6B7280;"><?= h($p['nama_ortu']) ?></td>
      <td style="color:#6B7280;min-width:180px;"><?= h($p['alamat'] ?? '') ?></td>
      <td>
        <a href="https://wa.me/<?= h(preg_replace('/\D/', '', $p['whatsapp'])) ?>" target="_blank"
           style="display:inline-flex;align-items:center;gap:5px;color:#16A34A;font-weight:700;font-size:13px;text-decoration:none;background:#F0FDF4;padding:3px 9px;border-radius:8px;">
          <i class="fa-brands fa-whatsapp"></i>
          <?= h($p['whatsapp']) ?>
        </a>
      </td>
      <td style="color:#6B7280;font-size:12.5px;white-space:nowrap;"><?= tgl($p['created_at']) ?></td>
      <td>
        <form method="post" style="display:inline;">
          <input type="hidden" name="id" value="<?= $p['id'] ?>">
          <select name="status" onchange="this.form.submit()"
                  style="border:1.5px solid #E2E8F0;border-radius:8px;padding:4px 8px;font-size:12px;font-weight:700;cursor:pointer;outline:none;background:#fff;
                  <?php
                    if ($p['status']==='Diterima') echo 'border-color:#16A34A;color:#16A34A;background:#F0FDF4;';
                    elseif ($p['status']==='Ditolak') echo 'border-color:#EF4444;color:#EF4444;background:#FEF2F2;';
                    else echo 'border-color:#2B8FE8;color:#2B8FE8;background:#EFF6FF;';
                  ?>">
            <?php foreach (['Baru','Diterima','Ditolak'] as $s): ?>
            <option value="<?= $s ?>" <?= $p['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </td>
      <td style="text-align:right;">
        <a class="link-danger" href="pendaftar.php?hapus=<?= $p['id'] ?>"
           onclick="return confirm('Hapus data pendaftar <?= h(addslashes($p['nama_anak'])) ?>?')">
          <i class="fa-solid fa-trash" style="font-size:11px;"></i> Hapus
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  </div>
  <?php endif; ?>
</div>

<?php require 'includes/admin_footer.php'; ?>
