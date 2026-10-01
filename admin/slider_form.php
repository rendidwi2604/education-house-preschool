<?php
require 'includes/auth.php';

$id   = isset($_GET['id']) ? (int) $_GET['id'] : null;
$data = ['judul' => '', 'subjudul' => '', 'gambar' => null, 'urutan' => 0, 'aktif' => 1];
$error = '';

// Load existing record untuk edit
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM slider WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $data = $found;
    else { 
        $inner = <?php
require 'includes/auth.php';

$id   = isset($_GET['id']) ? (int) $_GET['id'] : null;
$data = ['judul' => '', 'subjudul' => '', 'gambar' => null, 'urutan' => 0, 'aktif' => 1];
$error = '';

// Load existing record untuk edit
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM slider WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $data = $found;
    else { redirect('/admin/slider.php'); exit; }
}

$page_title = $id ? 'Edit Slide' : 'Tambah Slide';

// ── Proses POST ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul']    ?? '');
    $subjudul = trim($_POST['subjudul'] ?? '');
    $urutan   = (int) ($_POST['urutan'] ?? 0);
    $aktif    = isset($_POST['aktif']) ? 1 : 0;
    $gambar   = $data['gambar'];

    // Upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $izin = ['jpg','jpeg','png','webp','gif'];
        $ext  = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $izin)) {
            $error = 'Format gambar harus JPG, PNG, WEBP, atau GIF.';
        } elseif ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
            $error = 'Ukuran gambar maksimal 5MB.';
        } else {
            $folder  = __DIR__ . '/../assets/uploads/slider/';
            $newName = 'slide_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $newName)) {
                // Hapus gambar lama jika ada
                if ($id && $data['gambar'] && is_file($folder . $data['gambar'])) {
                    unlink($folder . $data['gambar']);
                }
                $gambar = $newName;
            } else {
                $error = 'Gagal menyimpan file, periksa permission folder uploads/slider.';
            }
        }
    }

    // Validasi: minimal harus ada gambar
    if ($error === '' && !$gambar) {
        $error = 'Gambar slide wajib diunggah.';
    }

    if ($error === '') {
        if ($id) {
            $pdo->prepare("UPDATE slider SET judul=?, subjudul=?, gambar=?, urutan=?, aktif=? WHERE id=?")
                ->execute([$judul ?: null, $subjudul ?: null, $gambar, $urutan, $aktif, $id]);
        } else {
            // Auto urutan terakhir jika tidak diisi
            if ($urutan === 0) {
                $max = $pdo->query("SELECT COALESCE(MAX(urutan),0)+1 FROM slider")->fetchColumn();
                $urutan = (int) $max;
            }
            $pdo->prepare("INSERT INTO slider (judul, subjudul, gambar, urutan, aktif) VALUES (?,?,?,?,?)")
                ->execute([$judul ?: null, $subjudul ?: null, $gambar, $urutan, $aktif]);
        }
        redirect('/admin/slider.php?tersimpan=1');
        exit;
    }

    // Re-populate on error
    $data = compact('judul', 'subjudul', 'gambar', 'urutan', 'aktif');
}

require 'includes/admin_header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

  <!-- ── Main form ───────────────────────────────────────── -->
  <div class="panel">
    <div class="panel-head">
      <h3><i class="fa-solid <?= $id ? 'fa-pen-to-square' : 'fa-circle-plus' ?>"></i>
        <?= $id ? 'Edit Slide' : 'Tambah Slide Baru' ?>
      </h3>
    </div>
    <form method="post" enctype="multipart/form-data" id="sliderForm">

      <div class="form-group">
        <label><i class="fa-solid fa-heading" style="color:#7952D9;"></i> Judul Slide <span style="color:#94A3B8;font-weight:500;">(opsional)</span></label>
        <input type="text" name="judul" value="<?= h($data['judul'] ?? '') ?>"
               placeholder="Contoh: Selamat Datang di Education House!">
        <span class="form-help">Tampil sebagai teks overlay di atas gambar slide.</span>
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-align-left" style="color:#7952D9;"></i> Subjudul / Deskripsi <span style="color:#94A3B8;font-weight:500;">(opsional)</span></label>
        <textarea name="subjudul" rows="3"
                  placeholder="Contoh: Tempat bermain dan belajar yang menyenangkan untuk si kecil."><?= h($data['subjudul'] ?? '') ?></textarea>
        <span class="form-help">Teks kecil di bawah judul pada overlay slide.</span>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div class="form-group">
          <label><i class="fa-solid fa-arrow-up-1-9" style="color:#F97316;"></i> Nomor Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($data['urutan'] ?? 0) ?>"
                 min="0" max="99" placeholder="0 = otomatis">
          <span class="form-help">Isi 0 untuk otomatis di akhir urutan.</span>
        </div>
        <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:4px;">
          <label><i class="fa-solid fa-toggle-on" style="color:#58A834;"></i> Status Tampil</label>
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-top:6px;">
            <div style="position:relative;display:inline-block;width:46px;height:26px;">
              <input type="checkbox" name="aktif" id="toggleAktif"
                     <?= !empty($data['aktif']) ? 'checked' : '' ?>
                     style="opacity:0;width:0;height:0;position:absolute;"
                     onchange="document.getElementById('toggleLabel').textContent=this.checked?'Aktif':'Nonaktif';">
              <span id="toggleTrack"
                    style="position:absolute;inset:0;border-radius:26px;cursor:pointer;transition:.3s;
                    background:<?= !empty($data['aktif']) ? '#58A834' : '#CBD5E1' ?>;"
                    onclick="var cb=document.getElementById('toggleAktif');cb.checked=!cb.checked;this.style.background=cb.checked?'#58A834':'#CBD5E1';document.getElementById('toggleLabel').textContent=cb.checked?'Aktif':'Nonaktif';">
                <span style="position:absolute;content:'';height:20px;width:20px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;transform:<?= !empty($data['aktif']) ? 'translateX(20px)' : 'translateX(0)' ?>;"></span>
              </span>
            </div>
            <span id="toggleLabel" style="font-weight:700;font-size:13px;color:<?= !empty($data['aktif']) ? '#16A34A' : '#94A3B8' ?>;">
              <?= !empty($data['aktif']) ? 'Aktif' : 'Nonaktif' ?>
            </span>
          </label>
        </div>
      </div>

      <div style="display:flex;gap:12px;margin-top:6px;">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Slide
        </button>
        <a href="slider.php" class="btn btn-ghost">
          <i class="fa-solid fa-xmark"></i> Batal
        </a>
      </div>
    </form>
  </div>

  <!-- ── Image upload sidebar ───────────────────────────── -->
  <div>
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px;margin-bottom:14px;">
        <h3><i class="fa-solid fa-image"></i> Gambar Slide</h3>
      </div>

      <!-- Current / preview image -->
      <div id="imgDisplayWrap" style="margin-bottom:14px;">
        <?php if (!empty($data['gambar']) && file_exists(__DIR__ . '/../assets/uploads/slider/' . $data['gambar'])): ?>
        <div style="border-radius:12px;overflow:hidden;border:2px solid #E8ECF4;background:#F8FAFC;">
          <img id="imgPreview"
               src="../assets/uploads/slider/<?= h($data['gambar']) ?>"
               style="width:100%;height:auto;display:block;max-height:220px;object-fit:contain;">
        </div>
        <div id="currentFilename" style="font-size:11.5px;color:#94A3B8;text-align:center;margin-top:6px;font-weight:600;">
          <?= h($data['gambar']) ?>
        </div>
        <?php else: ?>
        <div id="imgPlaceholder"
             style="border-radius:12px;border:2px dashed #C7D2FE;background:#EEF2FF;aspect-ratio:16/9;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;">
          <i class="fa-solid fa-image" style="font-size:36px;color:#A5B4FC;"></i>
          <span style="font-size:12px;color:#818CF8;font-weight:600;">Belum ada gambar</span>
        </div>
        <img id="imgPreview" src="" style="display:none;width:100%;height:auto;border-radius:12px;border:2px solid #E8ECF4;max-height:220px;object-fit:contain;">
        <?php endif; ?>
      </div>

      <!-- Drop zone -->
      <div class="dropzone" id="dropZone"
           onclick="document.getElementById('gambarInput').click();"
           ondragover="event.preventDefault();this.style.background='#E0E7FF';"
           ondragleave="this.style.background='';"
           ondrop="handleDrop(event);"
           style="padding:18px;">
        <i class="fa-solid fa-cloud-arrow-up" style="font-size:26px;display:block;margin-bottom:6px;"></i>
        Klik atau seret gambar ke sini
        <div style="font-size:11px;color:#818CF8;font-weight:600;margin-top:4px;">
          JPG · PNG · WEBP · GIF — Maks 5MB
        </div>
        <div style="font-size:11px;color:#A5B4FC;margin-top:2px;">
          Gambar akan ditampilkan sesuai ukuran aslinya
        </div>
      </div>
      <input type="file" id="gambarInput" name="gambar" form="sliderForm"
             accept=".jpg,.jpeg,.png,.webp,.gif"
             style="display:none;" onchange="previewImage(this)">

      <!-- Image info (muncul setelah pilih) -->
      <div id="imgInfo" style="display:none;margin-top:10px;padding:10px 12px;background:#F8FAFC;border-radius:10px;border:1px solid #E8ECF4;font-size:12px;color:#64748B;font-weight:600;">
        <div style="display:flex;justify-content:space-between;">
          <span id="imgName" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;"></span>
          <span id="imgSize" style="color:#94A3B8;flex-shrink:0;margin-left:8px;"></span>
        </div>
        <div id="imgDim" style="color:#94A3B8;margin-top:2px;font-size:11px;"></div>
      </div>

      <?php if ($id && !empty($data['gambar'])): ?>
      <span class="form-help" style="display:block;text-align:center;margin-top:8px;">
        Biarkan kosong jika tidak ingin mengganti gambar.
      </span>
      <?php endif; ?>
    </div>

    <!-- Tips card -->
    <div class="panel" style="background:#FFFBEB;border-color:#FDE68A;">
      <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:13px;color:#92400E;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-lightbulb" style="color:#F59E0B;"></i> Tips Gambar Slider
      </div>
      <ul style="font-size:12px;color:#78350F;font-weight:500;line-height:1.8;margin:0;padding-left:16px;">
        <li>Gunakan rasio <strong>16:9</strong> atau <strong>4:3</strong> agar tampil rapi</li>
        <li>Resolusi minimal <strong>800×450px</strong> untuk kualitas baik</li>
        <li>Format <strong>WEBP</strong> direkomendasikan (file lebih kecil)</li>
        <li>Gambar ditampilkan sesuai ukuran asli dengan <code>h-auto</code></li>
        <li>Judul & subjudul tampil sebagai overlay di atas gambar</li>
      </ul>
    </div>
  </div>

</div>

<script>
function previewImage(input) {
  if (!input.files || !input.files[0]) return;
  var file    = input.files[0];
  var reader  = new FileReader();
  reader.onload = function(e) {
    var img = document.getElementById('imgPreview');
    var ph  = document.getElementById('imgPlaceholder');
    if (ph) ph.style.display = 'none';
    img.src = e.target.result;
    img.style.display = 'block';

    // Show info
    var info = document.getElementById('imgInfo');
    document.getElementById('imgName').textContent = file.name;
    document.getElementById('imgSize').textContent = (file.size/1024).toFixed(0) + ' KB';
    info.style.display = 'block';

    // Get dimensions
    var tempImg = new Image();
    tempImg.onload = function(){
      document.getElementById('imgDim').textContent = tempImg.naturalWidth + ' × ' + tempImg.naturalHeight + ' px';
    };
    tempImg.src = e.target.result;
  };
  reader.readAsDataURL(file);
}

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('dropZone').style.background = '';
  var files = e.dataTransfer.files;
  if (files.length) {
    document.getElementById('gambarInput').files = files; // won't work in all browsers but try
    // Fallback: just preview
    var reader = new FileReader();
    reader.onload = function(ev){
      var img = document.getElementById('imgPreview');
      var ph  = document.getElementById('imgPlaceholder');
      if (ph) ph.style.display = 'none';
      img.src = ev.target.result;
      img.style.display = 'block';
    };
    reader.readAsDataURL(files[0]);
  }
}

// Sync toggle visual
document.getElementById('toggleAktif').addEventListener('change', function(){
  var track = document.getElementById('toggleTrack');
  var label = document.getElementById('toggleLabel');
  var knob  = track.querySelector('span');
  track.style.background = this.checked ? '#58A834' : '#CBD5E1';
  label.style.color = this.checked ? '#16A34A' : '#94A3B8';
  label.textContent = this.checked ? 'Aktif' : 'Nonaktif';
  knob.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';
});
</script>

<?php require 'includes/admin_footer.php'; ?>
.Groups[1].Value
        # Jika path sudah mulai dengan / biarkan, kalau tidak tambah /admin/
        if ($inner -match '^/') {
            "redirect('$inner')"
        } else {
            "redirect('/admin/$inner')"
        }
    ; exit; }
}

$page_title = $id ? 'Edit Slide' : 'Tambah Slide';

// ── Proses POST ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul']    ?? '');
    $subjudul = trim($_POST['subjudul'] ?? '');
    $urutan   = (int) ($_POST['urutan'] ?? 0);
    $aktif    = isset($_POST['aktif']) ? 1 : 0;
    $gambar   = $data['gambar'];

    // Upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $izin = ['jpg','jpeg','png','webp','gif'];
        $ext  = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $izin)) {
            $error = 'Format gambar harus JPG, PNG, WEBP, atau GIF.';
        } elseif ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
            $error = 'Ukuran gambar maksimal 5MB.';
        } else {
            $folder  = __DIR__ . '/../assets/uploads/slider/';
            $newName = 'slide_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $newName)) {
                // Hapus gambar lama jika ada
                if ($id && $data['gambar'] && is_file($folder . $data['gambar'])) {
                    unlink($folder . $data['gambar']);
                }
                $gambar = $newName;
            } else {
                $error = 'Gagal menyimpan file, periksa permission folder uploads/slider.';
            }
        }
    }

    // Validasi: minimal harus ada gambar
    if ($error === '' && !$gambar) {
        $error = 'Gambar slide wajib diunggah.';
    }

    if ($error === '') {
        if ($id) {
            $pdo->prepare("UPDATE slider SET judul=?, subjudul=?, gambar=?, urutan=?, aktif=? WHERE id=?")
                ->execute([$judul ?: null, $subjudul ?: null, $gambar, $urutan, $aktif, $id]);
        } else {
            // Auto urutan terakhir jika tidak diisi
            if ($urutan === 0) {
                $max = $pdo->query("SELECT COALESCE(MAX(urutan),0)+1 FROM slider")->fetchColumn();
                $urutan = (int) $max;
            }
            $pdo->prepare("INSERT INTO slider (judul, subjudul, gambar, urutan, aktif) VALUES (?,?,?,?,?)")
                ->execute([$judul ?: null, $subjudul ?: null, $gambar, $urutan, $aktif]);
        }
        
        $inner = <?php
require 'includes/auth.php';

$id   = isset($_GET['id']) ? (int) $_GET['id'] : null;
$data = ['judul' => '', 'subjudul' => '', 'gambar' => null, 'urutan' => 0, 'aktif' => 1];
$error = '';

// Load existing record untuk edit
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM slider WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $data = $found;
    else { redirect('/admin/slider.php'); exit; }
}

$page_title = $id ? 'Edit Slide' : 'Tambah Slide';

// ── Proses POST ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul']    ?? '');
    $subjudul = trim($_POST['subjudul'] ?? '');
    $urutan   = (int) ($_POST['urutan'] ?? 0);
    $aktif    = isset($_POST['aktif']) ? 1 : 0;
    $gambar   = $data['gambar'];

    // Upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $izin = ['jpg','jpeg','png','webp','gif'];
        $ext  = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $izin)) {
            $error = 'Format gambar harus JPG, PNG, WEBP, atau GIF.';
        } elseif ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
            $error = 'Ukuran gambar maksimal 5MB.';
        } else {
            $folder  = __DIR__ . '/../assets/uploads/slider/';
            $newName = 'slide_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $newName)) {
                // Hapus gambar lama jika ada
                if ($id && $data['gambar'] && is_file($folder . $data['gambar'])) {
                    unlink($folder . $data['gambar']);
                }
                $gambar = $newName;
            } else {
                $error = 'Gagal menyimpan file, periksa permission folder uploads/slider.';
            }
        }
    }

    // Validasi: minimal harus ada gambar
    if ($error === '' && !$gambar) {
        $error = 'Gambar slide wajib diunggah.';
    }

    if ($error === '') {
        if ($id) {
            $pdo->prepare("UPDATE slider SET judul=?, subjudul=?, gambar=?, urutan=?, aktif=? WHERE id=?")
                ->execute([$judul ?: null, $subjudul ?: null, $gambar, $urutan, $aktif, $id]);
        } else {
            // Auto urutan terakhir jika tidak diisi
            if ($urutan === 0) {
                $max = $pdo->query("SELECT COALESCE(MAX(urutan),0)+1 FROM slider")->fetchColumn();
                $urutan = (int) $max;
            }
            $pdo->prepare("INSERT INTO slider (judul, subjudul, gambar, urutan, aktif) VALUES (?,?,?,?,?)")
                ->execute([$judul ?: null, $subjudul ?: null, $gambar, $urutan, $aktif]);
        }
        redirect('/admin/slider.php?tersimpan=1');
        exit;
    }

    // Re-populate on error
    $data = compact('judul', 'subjudul', 'gambar', 'urutan', 'aktif');
}

require 'includes/admin_header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

  <!-- ── Main form ───────────────────────────────────────── -->
  <div class="panel">
    <div class="panel-head">
      <h3><i class="fa-solid <?= $id ? 'fa-pen-to-square' : 'fa-circle-plus' ?>"></i>
        <?= $id ? 'Edit Slide' : 'Tambah Slide Baru' ?>
      </h3>
    </div>
    <form method="post" enctype="multipart/form-data" id="sliderForm">

      <div class="form-group">
        <label><i class="fa-solid fa-heading" style="color:#7952D9;"></i> Judul Slide <span style="color:#94A3B8;font-weight:500;">(opsional)</span></label>
        <input type="text" name="judul" value="<?= h($data['judul'] ?? '') ?>"
               placeholder="Contoh: Selamat Datang di Education House!">
        <span class="form-help">Tampil sebagai teks overlay di atas gambar slide.</span>
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-align-left" style="color:#7952D9;"></i> Subjudul / Deskripsi <span style="color:#94A3B8;font-weight:500;">(opsional)</span></label>
        <textarea name="subjudul" rows="3"
                  placeholder="Contoh: Tempat bermain dan belajar yang menyenangkan untuk si kecil."><?= h($data['subjudul'] ?? '') ?></textarea>
        <span class="form-help">Teks kecil di bawah judul pada overlay slide.</span>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div class="form-group">
          <label><i class="fa-solid fa-arrow-up-1-9" style="color:#F97316;"></i> Nomor Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($data['urutan'] ?? 0) ?>"
                 min="0" max="99" placeholder="0 = otomatis">
          <span class="form-help">Isi 0 untuk otomatis di akhir urutan.</span>
        </div>
        <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:4px;">
          <label><i class="fa-solid fa-toggle-on" style="color:#58A834;"></i> Status Tampil</label>
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-top:6px;">
            <div style="position:relative;display:inline-block;width:46px;height:26px;">
              <input type="checkbox" name="aktif" id="toggleAktif"
                     <?= !empty($data['aktif']) ? 'checked' : '' ?>
                     style="opacity:0;width:0;height:0;position:absolute;"
                     onchange="document.getElementById('toggleLabel').textContent=this.checked?'Aktif':'Nonaktif';">
              <span id="toggleTrack"
                    style="position:absolute;inset:0;border-radius:26px;cursor:pointer;transition:.3s;
                    background:<?= !empty($data['aktif']) ? '#58A834' : '#CBD5E1' ?>;"
                    onclick="var cb=document.getElementById('toggleAktif');cb.checked=!cb.checked;this.style.background=cb.checked?'#58A834':'#CBD5E1';document.getElementById('toggleLabel').textContent=cb.checked?'Aktif':'Nonaktif';">
                <span style="position:absolute;content:'';height:20px;width:20px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;transform:<?= !empty($data['aktif']) ? 'translateX(20px)' : 'translateX(0)' ?>;"></span>
              </span>
            </div>
            <span id="toggleLabel" style="font-weight:700;font-size:13px;color:<?= !empty($data['aktif']) ? '#16A34A' : '#94A3B8' ?>;">
              <?= !empty($data['aktif']) ? 'Aktif' : 'Nonaktif' ?>
            </span>
          </label>
        </div>
      </div>

      <div style="display:flex;gap:12px;margin-top:6px;">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Slide
        </button>
        <a href="slider.php" class="btn btn-ghost">
          <i class="fa-solid fa-xmark"></i> Batal
        </a>
      </div>
    </form>
  </div>

  <!-- ── Image upload sidebar ───────────────────────────── -->
  <div>
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px;margin-bottom:14px;">
        <h3><i class="fa-solid fa-image"></i> Gambar Slide</h3>
      </div>

      <!-- Current / preview image -->
      <div id="imgDisplayWrap" style="margin-bottom:14px;">
        <?php if (!empty($data['gambar']) && file_exists(__DIR__ . '/../assets/uploads/slider/' . $data['gambar'])): ?>
        <div style="border-radius:12px;overflow:hidden;border:2px solid #E8ECF4;background:#F8FAFC;">
          <img id="imgPreview"
               src="../assets/uploads/slider/<?= h($data['gambar']) ?>"
               style="width:100%;height:auto;display:block;max-height:220px;object-fit:contain;">
        </div>
        <div id="currentFilename" style="font-size:11.5px;color:#94A3B8;text-align:center;margin-top:6px;font-weight:600;">
          <?= h($data['gambar']) ?>
        </div>
        <?php else: ?>
        <div id="imgPlaceholder"
             style="border-radius:12px;border:2px dashed #C7D2FE;background:#EEF2FF;aspect-ratio:16/9;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;">
          <i class="fa-solid fa-image" style="font-size:36px;color:#A5B4FC;"></i>
          <span style="font-size:12px;color:#818CF8;font-weight:600;">Belum ada gambar</span>
        </div>
        <img id="imgPreview" src="" style="display:none;width:100%;height:auto;border-radius:12px;border:2px solid #E8ECF4;max-height:220px;object-fit:contain;">
        <?php endif; ?>
      </div>

      <!-- Drop zone -->
      <div class="dropzone" id="dropZone"
           onclick="document.getElementById('gambarInput').click();"
           ondragover="event.preventDefault();this.style.background='#E0E7FF';"
           ondragleave="this.style.background='';"
           ondrop="handleDrop(event);"
           style="padding:18px;">
        <i class="fa-solid fa-cloud-arrow-up" style="font-size:26px;display:block;margin-bottom:6px;"></i>
        Klik atau seret gambar ke sini
        <div style="font-size:11px;color:#818CF8;font-weight:600;margin-top:4px;">
          JPG · PNG · WEBP · GIF — Maks 5MB
        </div>
        <div style="font-size:11px;color:#A5B4FC;margin-top:2px;">
          Gambar akan ditampilkan sesuai ukuran aslinya
        </div>
      </div>
      <input type="file" id="gambarInput" name="gambar" form="sliderForm"
             accept=".jpg,.jpeg,.png,.webp,.gif"
             style="display:none;" onchange="previewImage(this)">

      <!-- Image info (muncul setelah pilih) -->
      <div id="imgInfo" style="display:none;margin-top:10px;padding:10px 12px;background:#F8FAFC;border-radius:10px;border:1px solid #E8ECF4;font-size:12px;color:#64748B;font-weight:600;">
        <div style="display:flex;justify-content:space-between;">
          <span id="imgName" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;"></span>
          <span id="imgSize" style="color:#94A3B8;flex-shrink:0;margin-left:8px;"></span>
        </div>
        <div id="imgDim" style="color:#94A3B8;margin-top:2px;font-size:11px;"></div>
      </div>

      <?php if ($id && !empty($data['gambar'])): ?>
      <span class="form-help" style="display:block;text-align:center;margin-top:8px;">
        Biarkan kosong jika tidak ingin mengganti gambar.
      </span>
      <?php endif; ?>
    </div>

    <!-- Tips card -->
    <div class="panel" style="background:#FFFBEB;border-color:#FDE68A;">
      <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:13px;color:#92400E;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-lightbulb" style="color:#F59E0B;"></i> Tips Gambar Slider
      </div>
      <ul style="font-size:12px;color:#78350F;font-weight:500;line-height:1.8;margin:0;padding-left:16px;">
        <li>Gunakan rasio <strong>16:9</strong> atau <strong>4:3</strong> agar tampil rapi</li>
        <li>Resolusi minimal <strong>800×450px</strong> untuk kualitas baik</li>
        <li>Format <strong>WEBP</strong> direkomendasikan (file lebih kecil)</li>
        <li>Gambar ditampilkan sesuai ukuran asli dengan <code>h-auto</code></li>
        <li>Judul & subjudul tampil sebagai overlay di atas gambar</li>
      </ul>
    </div>
  </div>

</div>

<script>
function previewImage(input) {
  if (!input.files || !input.files[0]) return;
  var file    = input.files[0];
  var reader  = new FileReader();
  reader.onload = function(e) {
    var img = document.getElementById('imgPreview');
    var ph  = document.getElementById('imgPlaceholder');
    if (ph) ph.style.display = 'none';
    img.src = e.target.result;
    img.style.display = 'block';

    // Show info
    var info = document.getElementById('imgInfo');
    document.getElementById('imgName').textContent = file.name;
    document.getElementById('imgSize').textContent = (file.size/1024).toFixed(0) + ' KB';
    info.style.display = 'block';

    // Get dimensions
    var tempImg = new Image();
    tempImg.onload = function(){
      document.getElementById('imgDim').textContent = tempImg.naturalWidth + ' × ' + tempImg.naturalHeight + ' px';
    };
    tempImg.src = e.target.result;
  };
  reader.readAsDataURL(file);
}

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('dropZone').style.background = '';
  var files = e.dataTransfer.files;
  if (files.length) {
    document.getElementById('gambarInput').files = files; // won't work in all browsers but try
    // Fallback: just preview
    var reader = new FileReader();
    reader.onload = function(ev){
      var img = document.getElementById('imgPreview');
      var ph  = document.getElementById('imgPlaceholder');
      if (ph) ph.style.display = 'none';
      img.src = ev.target.result;
      img.style.display = 'block';
    };
    reader.readAsDataURL(files[0]);
  }
}

// Sync toggle visual
document.getElementById('toggleAktif').addEventListener('change', function(){
  var track = document.getElementById('toggleTrack');
  var label = document.getElementById('toggleLabel');
  var knob  = track.querySelector('span');
  track.style.background = this.checked ? '#58A834' : '#CBD5E1';
  label.style.color = this.checked ? '#16A34A' : '#94A3B8';
  label.textContent = this.checked ? 'Aktif' : 'Nonaktif';
  knob.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';
});
</script>

<?php require 'includes/admin_footer.php'; ?>
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

    // Re-populate on error
    $data = compact('judul', 'subjudul', 'gambar', 'urutan', 'aktif');
}

require 'includes/admin_header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

  <!-- ── Main form ───────────────────────────────────────── -->
  <div class="panel">
    <div class="panel-head">
      <h3><i class="fa-solid <?= $id ? 'fa-pen-to-square' : 'fa-circle-plus' ?>"></i>
        <?= $id ? 'Edit Slide' : 'Tambah Slide Baru' ?>
      </h3>
    </div>
    <form method="post" enctype="multipart/form-data" id="sliderForm">

      <div class="form-group">
        <label><i class="fa-solid fa-heading" style="color:#7952D9;"></i> Judul Slide <span style="color:#94A3B8;font-weight:500;">(opsional)</span></label>
        <input type="text" name="judul" value="<?= h($data['judul'] ?? '') ?>"
               placeholder="Contoh: Selamat Datang di Education House!">
        <span class="form-help">Tampil sebagai teks overlay di atas gambar slide.</span>
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-align-left" style="color:#7952D9;"></i> Subjudul / Deskripsi <span style="color:#94A3B8;font-weight:500;">(opsional)</span></label>
        <textarea name="subjudul" rows="3"
                  placeholder="Contoh: Tempat bermain dan belajar yang menyenangkan untuk si kecil."><?= h($data['subjudul'] ?? '') ?></textarea>
        <span class="form-help">Teks kecil di bawah judul pada overlay slide.</span>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div class="form-group">
          <label><i class="fa-solid fa-arrow-up-1-9" style="color:#F97316;"></i> Nomor Urutan</label>
          <input type="number" name="urutan" value="<?= (int)($data['urutan'] ?? 0) ?>"
                 min="0" max="99" placeholder="0 = otomatis">
          <span class="form-help">Isi 0 untuk otomatis di akhir urutan.</span>
        </div>
        <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:4px;">
          <label><i class="fa-solid fa-toggle-on" style="color:#58A834;"></i> Status Tampil</label>
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-top:6px;">
            <div style="position:relative;display:inline-block;width:46px;height:26px;">
              <input type="checkbox" name="aktif" id="toggleAktif"
                     <?= !empty($data['aktif']) ? 'checked' : '' ?>
                     style="opacity:0;width:0;height:0;position:absolute;"
                     onchange="document.getElementById('toggleLabel').textContent=this.checked?'Aktif':'Nonaktif';">
              <span id="toggleTrack"
                    style="position:absolute;inset:0;border-radius:26px;cursor:pointer;transition:.3s;
                    background:<?= !empty($data['aktif']) ? '#58A834' : '#CBD5E1' ?>;"
                    onclick="var cb=document.getElementById('toggleAktif');cb.checked=!cb.checked;this.style.background=cb.checked?'#58A834':'#CBD5E1';document.getElementById('toggleLabel').textContent=cb.checked?'Aktif':'Nonaktif';">
                <span style="position:absolute;content:'';height:20px;width:20px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;transform:<?= !empty($data['aktif']) ? 'translateX(20px)' : 'translateX(0)' ?>;"></span>
              </span>
            </div>
            <span id="toggleLabel" style="font-weight:700;font-size:13px;color:<?= !empty($data['aktif']) ? '#16A34A' : '#94A3B8' ?>;">
              <?= !empty($data['aktif']) ? 'Aktif' : 'Nonaktif' ?>
            </span>
          </label>
        </div>
      </div>

      <div style="display:flex;gap:12px;margin-top:6px;">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Slide
        </button>
        <a href="slider.php" class="btn btn-ghost">
          <i class="fa-solid fa-xmark"></i> Batal
        </a>
      </div>
    </form>
  </div>

  <!-- ── Image upload sidebar ───────────────────────────── -->
  <div>
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px;margin-bottom:14px;">
        <h3><i class="fa-solid fa-image"></i> Gambar Slide</h3>
      </div>

      <!-- Current / preview image -->
      <div id="imgDisplayWrap" style="margin-bottom:14px;">
        <?php if (!empty($data['gambar']) && file_exists(__DIR__ . '/../assets/uploads/slider/' . $data['gambar'])): ?>
        <div style="border-radius:12px;overflow:hidden;border:2px solid #E8ECF4;background:#F8FAFC;">
          <img id="imgPreview"
               src="../assets/uploads/slider/<?= h($data['gambar']) ?>"
               style="width:100%;height:auto;display:block;max-height:220px;object-fit:contain;">
        </div>
        <div id="currentFilename" style="font-size:11.5px;color:#94A3B8;text-align:center;margin-top:6px;font-weight:600;">
          <?= h($data['gambar']) ?>
        </div>
        <?php else: ?>
        <div id="imgPlaceholder"
             style="border-radius:12px;border:2px dashed #C7D2FE;background:#EEF2FF;aspect-ratio:16/9;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;">
          <i class="fa-solid fa-image" style="font-size:36px;color:#A5B4FC;"></i>
          <span style="font-size:12px;color:#818CF8;font-weight:600;">Belum ada gambar</span>
        </div>
        <img id="imgPreview" src="" style="display:none;width:100%;height:auto;border-radius:12px;border:2px solid #E8ECF4;max-height:220px;object-fit:contain;">
        <?php endif; ?>
      </div>

      <!-- Drop zone -->
      <div class="dropzone" id="dropZone"
           onclick="document.getElementById('gambarInput').click();"
           ondragover="event.preventDefault();this.style.background='#E0E7FF';"
           ondragleave="this.style.background='';"
           ondrop="handleDrop(event);"
           style="padding:18px;">
        <i class="fa-solid fa-cloud-arrow-up" style="font-size:26px;display:block;margin-bottom:6px;"></i>
        Klik atau seret gambar ke sini
        <div style="font-size:11px;color:#818CF8;font-weight:600;margin-top:4px;">
          JPG · PNG · WEBP · GIF — Maks 5MB
        </div>
        <div style="font-size:11px;color:#A5B4FC;margin-top:2px;">
          Gambar akan ditampilkan sesuai ukuran aslinya
        </div>
      </div>
      <input type="file" id="gambarInput" name="gambar" form="sliderForm"
             accept=".jpg,.jpeg,.png,.webp,.gif"
             style="display:none;" onchange="previewImage(this)">

      <!-- Image info (muncul setelah pilih) -->
      <div id="imgInfo" style="display:none;margin-top:10px;padding:10px 12px;background:#F8FAFC;border-radius:10px;border:1px solid #E8ECF4;font-size:12px;color:#64748B;font-weight:600;">
        <div style="display:flex;justify-content:space-between;">
          <span id="imgName" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;"></span>
          <span id="imgSize" style="color:#94A3B8;flex-shrink:0;margin-left:8px;"></span>
        </div>
        <div id="imgDim" style="color:#94A3B8;margin-top:2px;font-size:11px;"></div>
      </div>

      <?php if ($id && !empty($data['gambar'])): ?>
      <span class="form-help" style="display:block;text-align:center;margin-top:8px;">
        Biarkan kosong jika tidak ingin mengganti gambar.
      </span>
      <?php endif; ?>
    </div>

    <!-- Tips card -->
    <div class="panel" style="background:#FFFBEB;border-color:#FDE68A;">
      <div style="font-family:'Quicksand',sans-serif;font-weight:800;font-size:13px;color:#92400E;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-lightbulb" style="color:#F59E0B;"></i> Tips Gambar Slider
      </div>
      <ul style="font-size:12px;color:#78350F;font-weight:500;line-height:1.8;margin:0;padding-left:16px;">
        <li>Gunakan rasio <strong>16:9</strong> atau <strong>4:3</strong> agar tampil rapi</li>
        <li>Resolusi minimal <strong>800×450px</strong> untuk kualitas baik</li>
        <li>Format <strong>WEBP</strong> direkomendasikan (file lebih kecil)</li>
        <li>Gambar ditampilkan sesuai ukuran asli dengan <code>h-auto</code></li>
        <li>Judul & subjudul tampil sebagai overlay di atas gambar</li>
      </ul>
    </div>
  </div>

</div>

<script>
function previewImage(input) {
  if (!input.files || !input.files[0]) return;
  var file    = input.files[0];
  var reader  = new FileReader();
  reader.onload = function(e) {
    var img = document.getElementById('imgPreview');
    var ph  = document.getElementById('imgPlaceholder');
    if (ph) ph.style.display = 'none';
    img.src = e.target.result;
    img.style.display = 'block';

    // Show info
    var info = document.getElementById('imgInfo');
    document.getElementById('imgName').textContent = file.name;
    document.getElementById('imgSize').textContent = (file.size/1024).toFixed(0) + ' KB';
    info.style.display = 'block';

    // Get dimensions
    var tempImg = new Image();
    tempImg.onload = function(){
      document.getElementById('imgDim').textContent = tempImg.naturalWidth + ' × ' + tempImg.naturalHeight + ' px';
    };
    tempImg.src = e.target.result;
  };
  reader.readAsDataURL(file);
}

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('dropZone').style.background = '';
  var files = e.dataTransfer.files;
  if (files.length) {
    document.getElementById('gambarInput').files = files; // won't work in all browsers but try
    // Fallback: just preview
    var reader = new FileReader();
    reader.onload = function(ev){
      var img = document.getElementById('imgPreview');
      var ph  = document.getElementById('imgPlaceholder');
      if (ph) ph.style.display = 'none';
      img.src = ev.target.result;
      img.style.display = 'block';
    };
    reader.readAsDataURL(files[0]);
  }
}

// Sync toggle visual
document.getElementById('toggleAktif').addEventListener('change', function(){
  var track = document.getElementById('toggleTrack');
  var label = document.getElementById('toggleLabel');
  var knob  = track.querySelector('span');
  track.style.background = this.checked ? '#58A834' : '#CBD5E1';
  label.style.color = this.checked ? '#16A34A' : '#94A3B8';
  label.textContent = this.checked ? 'Aktif' : 'Nonaktif';
  knob.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';
});
</script>

<?php require 'includes/admin_footer.php'; ?>
