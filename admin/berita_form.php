<?php
require 'includes/auth.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$data = ['judul' => '', 'isi' => '', 'kategori' => 'Pengumuman', 'status' => 'terbit', 'gambar' => null];
$error = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $data = $found;
}

$page_title = $id ? 'Edit Berita' : 'Tambah Berita';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul'] ?? '');
    $isi      = trim($_POST['isi'] ?? '');
    $kategori = trim($_POST['kategori'] ?? 'Pengumuman');
    $status   = $_POST['status'] === 'draft' ? 'draft' : 'terbit';
    $gambar   = $data['gambar'];

    if ($judul === '' || $isi === '') {
        $error = 'Judul dan isi berita wajib diisi.';
    } else {
        if (!empty($_FILES['gambar']['name'])) {
            $izin = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $izin)) {
                $error = 'Format gambar harus JPG, PNG, atau WEBP.';
            } elseif ($_FILES['gambar']['size'] > 3 * 1024 * 1024) {
                $error = 'Ukuran gambar maksimal 3MB.';
            } else {
                $namaBaru = 'berita_' . time() . '_' . rand(100,999) . '.' . $ext;
                move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../assets/uploads/galeri/' . $namaBaru);
                $gambar = $namaBaru;
            }
        }
        if ($error === '') {
            if ($id) {
                $pdo->prepare("UPDATE berita SET judul=?, isi=?, kategori=?, status=?, gambar=? WHERE id=?")
                    ->execute([$judul, $isi, $kategori, $status, $gambar, $id]);
            } else {
                $pdo->prepare("INSERT INTO berita (judul, isi, kategori, status, gambar) VALUES (?,?,?,?,?)")
                    ->execute([$judul, $isi, $kategori, $status, $gambar]);
            }
            header('Location: berita.php?tersimpan=1');
            exit;
        }
    }
    $data = ['judul'=>$judul,'isi'=>$isi,'kategori'=>$kategori,'status'=>$status,'gambar'=>$gambar];
}

require 'includes/admin_header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" id="beritaForm">
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

  <!-- Main form -->
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

  <!-- Sidebar options -->
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
          <option value="terbit" <?= $data['status'] === 'terbit' ? 'selected' : '' ?>>
            Terbit (tampil di halaman publik)
          </option>
          <option value="draft" <?= $data['status'] === 'draft' ? 'selected' : '' ?>>
            Draft (belum tampil)
          </option>
        </select>
      </div>
    </div>

    <!-- Gambar — INSIDE the same <form> -->
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px;margin-bottom:14px;">
        <h3><i class="fa-solid fa-image"></i> Gambar Berita</h3>
      </div>
      <?php if ($data['gambar']): ?>
      <img src="../assets/uploads/galeri/<?= h($data['gambar']) ?>"
           id="imgPreview"
           style="width:100%;border-radius:12px;object-fit:cover;aspect-ratio:16/9;margin-bottom:12px;">
      <?php else: ?>
      <div id="imgPlaceholder" style="width:100%;aspect-ratio:16/9;border-radius:12px;background:#F8FAFC;border:2px dashed #E2E8F0;display:flex;flex-direction:column;align-items:center;justify-content:center;margin-bottom:12px;color:#94A3B8;">
        <i class="fa-solid fa-image" style="font-size:28px;margin-bottom:6px;"></i>
        <span style="font-size:12px;font-weight:600;">Belum ada gambar</span>
      </div>
      <img id="imgPreview" style="display:none;width:100%;border-radius:12px;aspect-ratio:16/9;object-fit:cover;margin-bottom:12px;">
      <?php endif; ?>

      <div class="form-group" style="margin-bottom:0;">
        <div class="dropzone" onclick="document.getElementById('gambarInput').click();"
             style="cursor:pointer;">
          <i class="fa-solid fa-cloud-arrow-up" style="font-size:20px;display:block;margin-bottom:5px;"></i>
          Klik untuk pilih gambar
          <div style="font-size:11px;color:#818CF8;font-weight:600;margin-top:3px;">JPG, PNG, WEBP · Maks 3MB</div>
        </div>
        <!-- ✅ File input ADA di dalam <form id="beritaForm"> — tidak pakai form="" attribute -->
        <input type="file" name="gambar" id="gambarInput"
               accept=".jpg,.jpeg,.png,.webp"
               style="display:none;" onchange="previewImg(this)">
        <span class="form-help" style="margin-top:6px;display:block;">
          Biarkan kosong jika tidak ingin mengubah gambar.
        </span>
      </div>
    </div>
  </div>

</div>
</form>

<script>
function previewImg(input) {
  var preview     = document.getElementById('imgPreview');
  var placeholder = document.getElementById('imgPlaceholder');
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      preview.src          = e.target.result;
      preview.style.display = 'block';
      if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require 'includes/admin_footer.php'; ?>
