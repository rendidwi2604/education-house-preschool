<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/../includes/storage.php';

$id   = isset($_GET['id']) ? (int) $_GET['id'] : null;
$data = ['judul' => '', 'isi' => '', 'kategori' => 'Pengumuman', 'status' => 'terbit', 'gambar' => null];
$error   = '';
$success = '';

// Load data berita + gambar existing
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) {
        $data = $found;
    } else {
        redirect('/admin/berita.php');
    }
}

// Ambil gambar existing dari tabel berita_gambar
$existingGambar = [];
if ($id) {
    $stmtG = $pdo->prepare("SELECT * FROM berita_gambar WHERE berita_id = ? ORDER BY urutan ASC, id ASC");
    $stmtG->execute([$id]);
    $existingGambar = $stmtG->fetchAll();
}

$page_title = $id ? 'Edit Berita' : 'Tambah Berita';

// ── Hapus satu gambar via AJAX ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_gambar_id'])) {
    $gid = (int) $_POST['hapus_gambar_id'];
    $stmtDel = $pdo->prepare("SELECT url FROM berita_gambar WHERE id = ? AND berita_id = ?");
    $stmtDel->execute([$gid, $id]);
    $gRow = $stmtDel->fetch();
    if ($gRow) {
        // Hapus dari Supabase Storage jika URL Supabase
        if (str_starts_with($gRow['url'], 'https://')) {
            $fname = basename(parse_url($gRow['url'], PHP_URL_PATH));
            supabase_delete('berita', $fname);
        }
        $pdo->prepare("DELETE FROM berita_gambar WHERE id = ?")->execute([$gid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

// ── Proses POST utama ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['hapus_gambar_id'])) {
    $judul    = trim($_POST['judul']    ?? '');
    $isi      = trim($_POST['isi']      ?? '');
    $kategori = trim($_POST['kategori'] ?? 'Pengumuman');
    $status   = ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'terbit';

    if ($judul === '' || $isi === '') {
        $error = 'Judul dan isi berita wajib diisi.';
    } else {
        // Simpan / update berita
        if ($id) {
            $pdo->prepare("UPDATE berita SET judul=?, isi=?, kategori=?, status=? WHERE id=?")
                ->execute([$judul, $isi, $kategori, $status, $id]);
        } else {
            $pdo->prepare("INSERT INTO berita (judul, isi, kategori, status) VALUES (?,?,?,?)")
                ->execute([$judul, $isi, $kategori, $status]);
            $id = (int) $pdo->lastInsertId();
            // PostgreSQL pakai lastInsertId berbeda — fallback
            if (!$id) {
                $id = (int) $pdo->query("SELECT lastval()")->fetchColumn();
            }
        }

        // Upload gambar-gambar baru (multiple)
        $uploadErrors  = [];
        $uploadedCount = 0;
        $files = $_FILES['gambar_baru'] ?? [];

        if (!empty($files['name'][0])) {
            $total = count($files['name']);
            for ($i = 0; $i < $total; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
                if (empty($files['name'][$i])) continue;

                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                    $uploadErrors[] = "File #{$i}: format tidak didukung ({$ext})";
                    continue;
                }
                if ($files['size'][$i] > 5 * 1024 * 1024) {
                    $uploadErrors[] = "File #{$i}: ukuran melebihi 5MB";
                    continue;
                }

                $newName = 'berita_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                $mime    = mime_from_ext($ext);

                // Coba upload ke Supabase Storage dulu
                $finalUrl = null;
                if (getenv('SUPABASE_URL') && getenv('SUPABASE_KEY')) {
                    $result = supabase_upload($files['tmp_name'][$i], 'berita', $newName, $mime);
                    if ($result['ok']) {
                        $finalUrl = $result['url'];
                    }
                }

                // Fallback: simpan ke /tmp lalu encode sebagai data URL (tidak persistent)
                // ATAU simpan path relatif jika filesystem tersedia
                if ($finalUrl === null) {
                    // Coba simpan ke assets/uploads/galeri lokal (XAMPP)
                    $localDir = defined('APP_ROOT') ? APP_ROOT . '/assets/uploads/galeri/' : __DIR__ . '/../assets/uploads/galeri/';
                    if (is_writable($localDir)) {
                        if (move_uploaded_file($files['tmp_name'][$i], $localDir . $newName)) {
                            $finalUrl = 'assets/uploads/galeri/' . $newName;
                        }
                    }
                }

                if ($finalUrl !== null) {
                    // Ambil urutan tertinggi lalu insert
                    $stmtUrutan = $pdo->prepare("SELECT COALESCE(MAX(urutan),0)+1 FROM berita_gambar WHERE berita_id = ?");
                    $stmtUrutan->execute([$id]);
                    $urutan = (int) $stmtUrutan->fetchColumn();

                    $pdo->prepare("INSERT INTO berita_gambar (berita_id, url, urutan) VALUES (?,?,?)")
                        ->execute([$id, $finalUrl, $urutan]);
                    $uploadedCount++;
                } else {
                    $uploadErrors[] = "File #{$i}: gagal upload. Pastikan SUPABASE_URL & SUPABASE_KEY sudah diset di Vercel.";
                }
            }
        }

        if (!empty($uploadErrors)) {
            // Berita tetap tersimpan, tapi tampilkan error upload
            $error = 'Berita tersimpan. Error upload gambar: ' . implode('; ', $uploadErrors);
        } elseif ($uploadedCount > 0) {
            redirect('/admin/berita.php?tersimpan=1');
        } else {
            // Tidak ada file yang dipilih — redirect langsung
            redirect('/admin/berita.php?tersimpan=1');
        }

        // Reload data & gambar setelah simpan
        $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch() ?: $data;

        $stmtG = $pdo->prepare("SELECT * FROM berita_gambar WHERE berita_id = ? ORDER BY urutan ASC, id ASC");
        $stmtG->execute([$id]);
        $existingGambar = $stmtG->fetchAll();
    }
}

require __DIR__ . '/includes/admin_header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" id="beritaForm">
<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

  <!-- ── Kolom kiri: Konten ─────────────────────────────── -->
  <div>
    <div class="panel">
      <div class="panel-head">
        <h3><i class="fa-solid <?= $id ? 'fa-pen-to-square' : 'fa-circle-plus' ?>"></i>
          <?= $id ? 'Edit Berita' : 'Tulis Berita Baru' ?>
        </h3>
      </div>
      <div class="form-group">
        <label><i class="fa-solid fa-heading" style="color:#F97316;"></i> Judul Berita</label>
        <input type="text" name="judul" value="<?= h($data['judul']) ?>" required
               placeholder="Tulis judul berita yang menarik…">
      </div>
      <div class="form-group">
        <label><i class="fa-solid fa-align-left" style="color:#F97316;"></i> Isi Berita</label>
        <textarea name="isi" rows="10" required
                  placeholder="Tulis isi berita di sini…"><?= h($data['isi']) ?></textarea>
        <span class="form-help">Gunakan paragraf singkat agar mudah dibaca orang tua siswa.</span>
      </div>
      <div style="display:flex;gap:14px;">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Berita
        </button>
        <a href="berita.php" class="btn btn-ghost">
          <i class="fa-solid fa-xmark"></i> Batal
        </a>
      </div>
    </div>

    <!-- ── Gambar existing ─────────────────────────────── -->
    <?php if (!empty($existingGambar)): ?>
    <div class="panel">
      <div class="panel-head">
        <h3><i class="fa-solid fa-images"></i> Gambar Tersimpan
          <span style="font-size:12px;color:#94A3B8;font-weight:600;margin-left:6px;">
            (<?= count($existingGambar) ?> gambar)
          </span>
        </h3>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;" id="gambarGrid">
        <?php foreach ($existingGambar as $gi => $g): ?>
        <?php
          // Resolve URL tampilan
          $dispUrl = $g['url'];
          if (!str_starts_with($dispUrl, 'https://') && !str_starts_with($dispUrl, 'http://')) {
              $dispUrl = '../' . ltrim($dispUrl, '/');
          }
        ?>
        <div id="gcard-<?= $g['id'] ?>" style="position:relative;border-radius:12px;overflow:hidden;border:2px solid #E8ECF4;background:#F8FAFC;aspect-ratio:1;">
          <img src="<?= h($dispUrl) ?>"
               style="width:100%;height:100%;object-fit:cover;" loading="lazy"
               alt="Gambar berita <?= $gi + 1 ?>">
          <!-- Badge urutan -->
          <span style="position:absolute;top:6px;left:6px;background:rgba(0,0,0,.55);color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:6px;">
            #<?= $gi + 1 ?>
          </span>
          <!-- Tombol hapus -->
          <button type="button"
                  onclick="hapusGambar(<?= $g['id'] ?>, <?= $id ?>)"
                  style="position:absolute;top:6px;right:6px;width:28px;height:28px;border-radius:7px;background:rgba(239,68,68,.85);border:none;cursor:pointer;color:#fff;font-size:12px;display:flex;align-items:center;justify-content:center;"
                  title="Hapus gambar ini">
            <i class="fa-solid fa-trash-can"></i>
          </button>
        </div>
        <?php endforeach; ?>
      </div>
      <p style="font-size:11.5px;color:#94A3B8;margin-top:10px;font-weight:500;">
        <i class="fa-solid fa-circle-info"></i>
        Klik ikon tempat sampah untuk menghapus gambar. Gambar pertama dipakai sebagai thumbnail di daftar berita.
      </p>
    </div>
    <?php endif; ?>
  </div>

  <!-- ── Kolom kanan: Pengaturan + Upload ──────────────── -->
  <div>
    <!-- Status & Kategori -->
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px;margin-bottom:14px;">
        <h3><i class="fa-solid fa-sliders"></i> Pengaturan</h3>
      </div>
      <div class="form-group" style="margin-bottom:14px;">
        <label><i class="fa-solid fa-tag" style="color:#7952D9;"></i> Kategori</label>
        <select name="kategori">
          <?php foreach (['Prestasi','Kegiatan','Pengumuman'] as $k): ?>
          <option value="<?= $k ?>" <?= $data['kategori'] === $k ? 'selected' : '' ?>><?= $k ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label><i class="fa-solid fa-toggle-on" style="color:#58A834;"></i> Status Publikasi</label>
        <select name="status">
          <option value="terbit" <?= $data['status'] === 'terbit' ? 'selected' : '' ?>>Terbit (tampil di halaman publik)</option>
          <option value="draft"  <?= $data['status'] === 'draft'  ? 'selected' : '' ?>>Draft (belum tampil)</option>
        </select>
      </div>
    </div>

    <!-- Upload Gambar Baru (multiple) -->
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px;margin-bottom:14px;">
        <h3><i class="fa-solid fa-cloud-arrow-up"></i> Upload Gambar</h3>
      </div>

      <!-- Preview area -->
      <div id="previewArea" style="display:none;margin-bottom:14px;">
        <div id="previewGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px;margin-bottom:8px;"></div>
        <p id="previewCount" style="font-size:12px;color:#6B7280;font-weight:600;margin:0;"></p>
      </div>

      <!-- Drop zone -->
      <div class="dropzone" id="dropZone"
           onclick="document.getElementById('gambarInput').click();"
           ondragover="event.preventDefault();this.style.background='#DDF0D7';"
           ondragleave="this.style.background='';"
           ondrop="handleDrop(event);"
           style="padding:20px;text-align:center;">
        <i class="fa-solid fa-images" style="font-size:28px;display:block;margin-bottom:8px;"></i>
        <strong>Klik atau seret gambar ke sini</strong>
        <div style="font-size:11.5px;color:#818CF8;font-weight:600;margin-top:5px;">Bisa pilih banyak gambar sekaligus</div>
        <div style="font-size:11px;color:#A5B4FC;margin-top:3px;">JPG · PNG · WEBP · GIF — Maks 5MB per file</div>
      </div>

      <input type="file" name="gambar_baru[]" id="gambarInput"
             accept=".jpg,.jpeg,.png,.webp,.gif"
             multiple
             style="display:none;" onchange="previewImages(this)">

      <span class="form-help" style="margin-top:8px;display:block;">
        <i class="fa-solid fa-circle-info"></i>
        Gambar diupload ke Supabase Storage saat klik "Simpan Berita".
        <?php if (!getenv('SUPABASE_URL')): ?>
        <strong style="color:#DC2626;">SUPABASE_URL belum diset di env variables!</strong>
        <?php endif; ?>
      </span>
    </div>
  </div>

</div>
</form>

<script>
// ── Akumulasi file yang dipilih ───────────────────────────
var allFiles = new DataTransfer(); // Simpan semua file yang sudah dipilih

function previewImages(input) {
  // Tambahkan file baru ke akumulasi (hindari duplikat by name+size)
  if (input.files && input.files.length > 0) {
    Array.from(input.files).forEach(function(newFile) {
      var isDupe = false;
      for (var j = 0; j < allFiles.files.length; j++) {
        if (allFiles.files[j].name === newFile.name && allFiles.files[j].size === newFile.size) {
          isDupe = true; break;
        }
      }
      if (!isDupe) allFiles.items.add(newFile);
    });
    // Sync kembali ke input agar form submit membawa semua file
    input.files = allFiles.files;
  }

  var area  = document.getElementById('previewArea');
  var grid  = document.getElementById('previewGrid');
  var count = document.getElementById('previewCount');

  if (allFiles.files.length === 0) {
    area.style.display = 'none';
    return;
  }

  // Render ulang semua preview
  grid.innerHTML = '';
  area.style.display = 'block';
  count.textContent = allFiles.files.length + ' gambar dipilih';

  Array.from(allFiles.files).forEach(function(file, i) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var div = document.createElement('div');
      div.style.cssText = 'position:relative;border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F8FAFC;border:2px solid #E8ECF4;';
      var img = document.createElement('img');
      img.src = e.target.result;
      img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
      // Badge nomor
      var badge = document.createElement('span');
      badge.textContent = '#' + (i + 1);
      badge.style.cssText = 'position:absolute;bottom:4px;right:4px;background:rgba(0,0,0,.55);color:#fff;font-size:10px;font-weight:800;padding:1px 6px;border-radius:5px;';
      // Tombol hapus dari preview
      var btnDel = document.createElement('button');
      btnDel.type = 'button';
      btnDel.innerHTML = '&times;';
      btnDel.style.cssText = 'position:absolute;top:3px;right:3px;width:22px;height:22px;border-radius:5px;background:rgba(239,68,68,.85);border:none;cursor:pointer;color:#fff;font-size:14px;font-weight:800;line-height:1;display:flex;align-items:center;justify-content:center;';
      btnDel.title = 'Hapus dari antrian';
      (function(idx) {
        btnDel.onclick = function() { removeFromQueue(idx); };
      })(i);
      div.appendChild(img);
      div.appendChild(badge);
      div.appendChild(btnDel);
      grid.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
}

// Hapus file dari antrian upload
function removeFromQueue(idx) {
  var newDT = new DataTransfer();
  Array.from(allFiles.files).forEach(function(f, i) {
    if (i !== idx) newDT.items.add(f);
  });
  allFiles = newDT;
  var input = document.getElementById('gambarInput');
  input.files = allFiles.files;
  // Re-render tanpa input baru
  previewImages({ files: new DataTransfer().files }); // trigger dengan files kosong
  if (allFiles.files.length > 0) previewImages({ files: new DataTransfer().files });

  // Render manual agar tidak reset
  var area  = document.getElementById('previewArea');
  var grid  = document.getElementById('previewGrid');
  var count = document.getElementById('previewCount');
  grid.innerHTML = '';
  if (allFiles.files.length === 0) { area.style.display='none'; return; }
  count.textContent = allFiles.files.length + ' gambar dipilih';
  area.style.display = 'block';
  Array.from(allFiles.files).forEach(function(file, i) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var div = document.createElement('div');
      div.style.cssText = 'position:relative;border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F8FAFC;border:2px solid #E8ECF4;';
      var img = document.createElement('img');
      img.src = e.target.result;
      img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
      var badge = document.createElement('span');
      badge.textContent = '#' + (i + 1);
      badge.style.cssText = 'position:absolute;bottom:4px;right:4px;background:rgba(0,0,0,.55);color:#fff;font-size:10px;font-weight:800;padding:1px 6px;border-radius:5px;';
      var btnDel = document.createElement('button');
      btnDel.type = 'button';
      btnDel.innerHTML = '&times;';
      btnDel.style.cssText = 'position:absolute;top:3px;right:3px;width:22px;height:22px;border-radius:5px;background:rgba(239,68,68,.85);border:none;cursor:pointer;color:#fff;font-size:14px;font-weight:800;line-height:1;display:flex;align-items:center;justify-content:center;';
      (function(idx2) { btnDel.onclick = function() { removeFromQueue(idx2); }; })(i);
      div.appendChild(img); div.appendChild(badge); div.appendChild(btnDel);
      grid.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
}

// ── Drag & drop ───────────────────────────────────────────
function handleDrop(e) {
  e.preventDefault();
  document.getElementById('dropZone').style.background = '';
  var dt = e.dataTransfer;
  if (dt.files.length > 0) {
    previewImages(dt);
    // Sync ke input
    var input = document.getElementById('gambarInput');
    input.files = allFiles.files;
  }
}

// ── Hapus gambar existing via fetch ──────────────────────
function hapusGambar(gambarId, beritaId) {
  if (!confirm('Hapus gambar ini?')) return;
  fetch(window.location.href, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'hapus_gambar_id=' + gambarId
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.ok) {
      var card = document.getElementById('gcard-' + gambarId);
      if (card) card.remove();
      var cards = document.querySelectorAll('#gambarGrid > div');
      cards.forEach(function(c, i) {
        var badge = c.querySelector('span');
        if (badge) badge.textContent = '#' + (i + 1);
      });
    }
  })
  .catch(function() { alert('Gagal menghapus gambar.'); });
}
</script>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
