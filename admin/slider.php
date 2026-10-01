<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/../includes/storage.php';

$page_title = 'Slider Hero';

// ── Helper: resolve URL gambar slider ──────────────────────
// Gambar bisa berupa: URL Supabase (https://...) atau nama file lokal
function slider_img_url(string $gambar, string $prefix = '../'): string {
    if (empty($gambar)) return '';
    if (str_starts_with($gambar, 'https://')) return $gambar;
    return $prefix . 'assets/uploads/slider/' . $gambar;
}

// ── Hapus slide ──────────────────────────────────────────
if (isset($_GET['hapus'])) {
    $id  = (int) $_GET['hapus'];
    $row = $pdo->prepare("SELECT gambar FROM slider WHERE id = ?");
    $row->execute([$id]);
    $r = $row->fetch();
    if ($r && !empty($r['gambar'])) {
        if (str_starts_with($r['gambar'], 'https://')) {
            // Hapus dari Supabase Storage
            $filename = basename(parse_url($r['gambar'], PHP_URL_PATH));
            supabase_delete('slider', $filename);
        }
        // File lokal di Vercel tidak bisa dihapus (read-only), abaikan
    }
    $pdo->prepare("DELETE FROM slider WHERE id = ?")->execute([$id]);
    redirect('/admin/slider.php?hapus_sukses=1');
}

// ── Toggle aktif / nonaktif (PostgreSQL BOOLEAN) ──────────
if (isset($_GET['toggle'])) {
    $id  = (int) $_GET['toggle'];
    $pdo->prepare("UPDATE slider SET aktif = NOT aktif WHERE id = ?")->execute([$id]);
    redirect('/admin/slider.php?update=1');
}

// ── Simpan urutan via AJAX POST ───────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reorder'])) {
    $ids = array_map('intval', $_POST['ids'] ?? []);
    foreach ($ids as $pos => $sid) {
        $pdo->prepare("UPDATE slider SET urutan = ? WHERE id = ?")->execute([$pos + 1, $sid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

$daftar = $pdo->query("SELECT * FROM slider ORDER BY urutan ASC, id ASC")->fetchAll();
require __DIR__ . '/includes/admin_header.php';
?>

<?php if (isset($_GET['tersimpan'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Slide berhasil disimpan.</div>
<?php endif; ?>
<?php if (isset($_GET['hapus_sukses'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Slide berhasil dihapus.</div>
<?php endif; ?>
<?php if (isset($_GET['update'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Status slide diperbarui.</div>
<?php endif; ?>

<!-- Info banner -->
<div style="display:flex;align-items:center;gap:14px;background:linear-gradient(135deg,#EDE9FE,#DBEAFE);border:1.5px solid #C4B5FD;border-radius:16px;padding:16px 20px;margin-bottom:20px;">
  <div style="width:44px;height:44px;border-radius:13px;background:#7952D9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
    <i class="fa-solid fa-images" style="color:#fff;font-size:20px;"></i>
  </div>
  <div>
    <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:14px;color:#1E1B4B;">Slider Hero Halaman Utama</div>
    <div style="font-size:12.5px;color:#4338CA;font-weight:500;margin-top:2px;">
      Gambar yang aktif akan tampil otomatis bergantian di hero section halaman publik.
      Seret baris untuk mengatur urutan tampil. Gambar mengikuti ukuran aslinya.
    </div>
  </div>
  <a class="btn btn-primary btn-sm" href="slider_form.php" style="margin-left:auto;flex-shrink:0;white-space:nowrap;">
    <i class="fa-solid fa-circle-plus"></i> Tambah Slide
  </a>
</div>

<div class="panel">
  <div class="panel-head">
    <h3><i class="fa-solid fa-film"></i> Semua Slide <span style="font-size:12px;color:#94A3B8;font-weight:600;margin-left:6px;">(<?= count($daftar) ?> slide)</span></h3>
    <div style="display:flex;align-items:center;gap:6px;">
      <span id="reorderHint" style="font-size:11.5px;color:#94A3B8;font-weight:600;display:none;">
        <i class="fa-solid fa-arrows-up-down text-xs"></i> Seret untuk mengurutkan
      </span>
    </div>
  </div>

  <?php if (count($daftar) === 0): ?>
  <div class="empty-note">
    <i class="fa-solid fa-images"></i>
    Belum ada slide. Klik "Tambah Slide" untuk membuat slide pertama.
  </div>
  <?php else: ?>

  <!-- Drag-sort table -->
  <div style="overflow-x:auto;">
    <table style="min-width:660px;" id="sliderTable">
      <thead>
        <tr>
          <th style="width:36px;"></th>
          <th style="width:52px;">No</th>
          <th style="width:120px;">Gambar</th>
          <th>Judul & Subjudul</th>
          <th style="width:80px;text-align:center;">Status</th>
          <th style="width:120px;text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody id="sortableBody">
        <?php foreach ($daftar as $i => $sl): ?>
        <?php
          $thumbUrl = slider_img_url($sl['gambar'] ?? '', '../');
          $hasImg   = !empty($thumbUrl);
        ?>
        <tr data-id="<?= $sl['id'] ?>" style="cursor:default;">
          <!-- Drag handle -->
          <td style="text-align:center;color:#CBD5E1;cursor:grab;" class="drag-handle" title="Seret untuk ubah urutan">
            <i class="fa-solid fa-grip-vertical"></i>
          </td>
          <!-- No -->
          <td>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;background:#F1F5F9;border-radius:7px;font-size:12px;font-weight:800;color:#64748B;">
              <?= $sl['urutan'] ?: ($i + 1) ?>
            </span>
          </td>
          <!-- Thumb -->
          <td>
            <?php if ($hasImg): ?>
            <div style="width:100px;height:64px;border-radius:10px;overflow:hidden;border:2px solid #E8ECF4;background:#F8FAFC;cursor:pointer;"
                 onclick="previewSlide('<?= h($thumbUrl) ?>','<?= h(addslashes($sl['judul'] ?? '')) ?>')"
                 title="Klik untuk preview">
              <img src="<?= h($thumbUrl) ?>"
                   style="width:100%;height:100%;object-fit:cover;transition:transform .3s;"
                   onmouseenter="this.style.transform='scale(1.07)'"
                   onmouseleave="this.style.transform='scale(1)'"
                   loading="lazy">>
            </div>
            <?php else: ?>
            <div style="width:100px;height:64px;border-radius:10px;background:#EEF2FF;border:2px dashed #C7D2FE;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;">
              <i class="fa-solid fa-image" style="color:#A5B4FC;font-size:18px;"></i>
              <span style="font-size:9px;color:#A5B4FC;font-weight:600;">No Image</span>
            </div>
            <?php endif; ?>
          </td>
          <!-- Title -->
          <td>
            <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:14px;color:#1E1B4B;margin-bottom:2px;">
              <?= $sl['judul'] ? h($sl['judul']) : '<em style="color:#CBD5E1;font-style:normal;font-family:inherit;">Tanpa judul</em>' ?>
            </div>
            <?php if (!empty($sl['subjudul'])): ?>
            <div style="font-size:12px;color:#6B7280;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;">
              <?= h($sl['subjudul']) ?>
            </div>
            <?php endif; ?>
          </td>
          <!-- Toggle aktif -->
          <td style="text-align:center;">
            <a href="slider.php?toggle=<?= $sl['id'] ?>"
               title="<?= $sl['aktif'] ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' ?>"
               style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:999px;font-size:11.5px;font-weight:700;text-decoration:none;transition:all .15s;
               <?= $sl['aktif'] ? 'background:#DCFCE7;color:#16A34A;' : 'background:#F1F5F9;color:#94A3B8;' ?>">
              <i class="fa-solid <?= $sl['aktif'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>" style="font-size:14px;"></i>
              <?= $sl['aktif'] ? 'Aktif' : 'Off' ?>
            </a>
          </td>
          <!-- Actions -->
          <td style="text-align:right;white-space:nowrap;">
            <a class="link-edit" href="slider_form.php?id=<?= $sl['id'] ?>">
              <i class="fa-solid fa-pen-to-square" style="font-size:11px;"></i> Edit
            </a>
            <a class="link-danger" href="slider.php?hapus=<?= $sl['id'] ?>"
               onclick="return confirm('Hapus slide ini?')">
              <i class="fa-solid fa-trash" style="font-size:11px;"></i> Hapus
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Save order button (muncul setelah drag) -->
  <div id="reorderBar" style="display:none;margin-top:14px;padding:12px 14px;background:#EEF2FF;border-radius:12px;border:1.5px solid #C7D2FE;display:flex;align-items:center;justify-content:space-between;gap:12px;">
    <span style="font-size:13px;font-weight:600;color:#4338CA;">
      <i class="fa-solid fa-arrows-up-down" style="margin-right:5px;"></i>
      Urutan diubah — klik Simpan untuk menyimpan.
    </span>
    <button onclick="saveOrder()" class="btn btn-primary btn-sm">
      <i class="fa-solid fa-floppy-disk"></i> Simpan Urutan
    </button>
  </div>

  <?php endif; ?>
</div>

<!-- Preview lightbox -->
<div id="previewOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;align-items:center;justify-content:center;padding:24px;"
     onclick="if(event.target===this)closePreview()">
  <div style="max-width:780px;width:100%;position:relative;">
    <button onclick="closePreview()"
            style="position:absolute;top:-14px;right:-14px;width:34px;height:34px;border-radius:50%;background:#fff;border:none;cursor:pointer;font-size:16px;color:#374151;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.2);">
      <i class="fa-solid fa-xmark"></i>
    </button>
    <div style="border-radius:20px;overflow:hidden;border:4px solid #fff;box-shadow:0 20px 60px rgba(0,0,0,.4);">
      <img id="previewImg" src="" alt="" style="width:100%;height:auto;display:block;">
    </div>
    <p id="previewCaption" style="text-align:center;color:#fff;font-weight:700;font-size:14px;margin-top:12px;"></p>
  </div>
</div>

<script>
// ── Preview lightbox ──────────────────────────────────────
function previewSlide(src, title) {
  document.getElementById('previewImg').src = src;
  document.getElementById('previewCaption').textContent = title || '';
  var ol = document.getElementById('previewOverlay');
  ol.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function closePreview() {
  document.getElementById('previewOverlay').style.display = 'none';
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closePreview(); });

// ── Drag-and-drop reorder ─────────────────────────────────
(function(){
  var tbody     = document.getElementById('sortableBody');
  var reorderBar = document.getElementById('reorderBar');
  var hint      = document.getElementById('reorderHint');
  if (!tbody) return;

  // Show hint
  if (hint) hint.style.display = 'inline-flex';

  var dragging = null;

  function getRows(){ return Array.from(tbody.querySelectorAll('tr')); }

  tbody.querySelectorAll('.drag-handle').forEach(function(handle){
    handle.addEventListener('mousedown', function(e){
      var row = handle.closest('tr');
      dragging = row;
      row.style.opacity = '0.55';
      row.style.background = '#F5F3FF';
      e.preventDefault();
    });
  });

  document.addEventListener('mousemove', function(e){
    if (!dragging) return;
    var rows = getRows();
    rows.forEach(function(r){
      if (r === dragging) return;
      var rect = r.getBoundingClientRect();
      var mid  = rect.top + rect.height / 2;
      if (e.clientY < mid) {
        tbody.insertBefore(dragging, r);
      } else if (e.clientY > mid && r.nextElementSibling) {
        tbody.insertBefore(dragging, r.nextElementSibling);
      }
    });
  });

  document.addEventListener('mouseup', function(){
    if (!dragging) return;
    dragging.style.opacity = '';
    dragging.style.background = '';
    dragging = null;
    if (reorderBar) reorderBar.style.display = 'flex';
  });

  // Touch support
  tbody.querySelectorAll('.drag-handle').forEach(function(handle){
    handle.addEventListener('touchstart', function(e){
      dragging = handle.closest('tr');
      dragging.style.opacity = '0.55';
    }, {passive:true});
    handle.addEventListener('touchend', function(){
      if (!dragging) return;
      dragging.style.opacity = '';
      dragging = null;
      if (reorderBar) reorderBar.style.display = 'flex';
    }, {passive:true});
  });
})();

// ── Save order via fetch ──────────────────────────────────
function saveOrder() {
  var rows = Array.from(document.querySelectorAll('#sortableBody tr'));
  var ids  = rows.map(function(r){ return r.dataset.id; });
  var fd   = new FormData();
  fd.append('reorder', '1');
  ids.forEach(function(id){ fd.append('ids[]', id); });

  fetch('slider.php', { method:'POST', body:fd })
    .then(function(r){ return r.json(); })
    .then(function(){
      document.getElementById('reorderBar').style.display = 'none';
      // update nomor urut di kolom No
      rows.forEach(function(r, i){
        var badge = r.querySelector('td:nth-child(2) span');
        if (badge) badge.textContent = i + 1;
      });
    })
    .catch(function(){ alert('Gagal menyimpan urutan, coba lagi.'); });
}
</script>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
