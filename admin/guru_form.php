<?php
require __DIR__ . '/includes/auth.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$data = ['nama'=>'','jabatan'=>'','bidang'=>'','pendidikan'=>'','pengalaman'=>'','bio'=>'','foto'=>null];
$error = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM guru WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $data = $found;
}

$page_title = $id ? 'Edit Guru' : 'Tambah Guru';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama'] ?? '');
    $jabatan    = trim($_POST['jabatan'] ?? '');
    $bidang     = trim($_POST['bidang'] ?? '');
    $pendidikan = trim($_POST['pendidikan'] ?? '');
    $pengalaman = trim($_POST['pengalaman'] ?? '');
    $bio        = trim($_POST['bio'] ?? '');
    $foto       = $data['foto'];

    if ($nama === '') {
        $error = 'Nama wajib diisi.';
    } else {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $izin = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
          if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Foto gagal diupload. Silakan coba lagi.';
          } elseif (!in_array($ext, $izin, true)) {
                $error = 'Format foto harus JPG, PNG, atau WEBP.';
            } elseif ($_FILES['foto']['size'] > 3 * 1024 * 1024) {
                $error = 'Ukuran foto maksimal 3MB.';
            } else {
                $namaBaru = 'guru_' . time() . '_' . rand(100,999) . '.' . $ext;
            $tujuanFoto = __DIR__ . '/../assets/uploads/guru/' . $namaBaru;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $tujuanFoto)) {
              $foto = $namaBaru;
            } else {
              $error = 'Foto gagal disimpan. Periksa folder upload.';
            }
            }
        }
        if ($error === '') {
          $fotoLama = $data['foto'];
            if ($id) {
                $pdo->prepare("UPDATE guru SET nama=?,jabatan=?,bidang=?,pendidikan=?,pengalaman=?,bio=?,foto=? WHERE id=?")
                    ->execute([$nama,$jabatan,$bidang,$pendidikan,$pengalaman,$bio,$foto,$id]);
            } else {
                $pdo->prepare("INSERT INTO guru (nama,jabatan,bidang,pendidikan,pengalaman,bio,foto) VALUES (?,?,?,?,?,?,?)")
                    ->execute([$nama,$jabatan,$bidang,$pendidikan,$pengalaman,$bio,$foto]);
            }
                if ($id && $foto !== $fotoLama && $fotoLama) {
                  $pathFotoLama = __DIR__ . '/../assets/uploads/guru/' . $fotoLama;
                  if (is_file($pathFotoLama)) unlink($pathFotoLama);
                }
            redirect('/admin/guru.php?tersimpan=1');
            exit;
        }
    }
    $data = compact('nama','jabatan','bidang','pendidikan','pengalaman','bio','foto');
}

require __DIR__ . '/includes/admin_header.php';
?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

  <!-- Main form -->
  <div class="panel">
    <div class="panel-head">
      <h3><i class="fa-solid <?= $id ? 'fa-pen-to-square' : 'fa-user-plus' ?>"></i>
        <?= $id ? 'Edit Data Guru' : 'Tambah Guru Baru' ?>
      </h3>
    </div>
    <form id="guruForm" method="post" enctype="multipart/form-data">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="form-group">
          <label><i class="fa-solid fa-user" style="color:#58A834;"></i> Nama Lengkap *</label>
          <input type="text" name="nama" value="<?= h($data['nama']) ?>" required
                 placeholder="Contoh: Bunda Sarah Wijaya, S.Pd.">
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-id-badge" style="color:#58A834;"></i> Jabatan</label>
          <input type="text" name="jabatan" value="<?= h($data['jabatan']) ?>"
                 placeholder="Contoh: Guru Kelas A">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="form-group">
          <label><i class="fa-solid fa-book" style="color:#7952D9;"></i> Mata Pelajaran / Bidang</label>
          <input type="text" name="bidang" value="<?= h($data['bidang']) ?>"
                 placeholder="Contoh: Bahasa Inggris dan Seni">
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-graduation-cap" style="color:#2B8FE8;"></i> Pendidikan Terakhir</label>
          <input type="text" name="pendidikan" value="<?= h($data['pendidikan']) ?>"
                 placeholder="Contoh: S1 PAUD">
        </div>
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-clock-rotate-left" style="color:#F97316;"></i> Pengalaman Mengajar</label>
        <input type="text" name="pengalaman" value="<?= h($data['pengalaman']) ?>"
               placeholder="Contoh: 5 tahun">
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-comment-dots" style="color:#EC4899;"></i> Biodata Singkat</label>
        <textarea name="bio" rows="5"
                  placeholder="Tulis bio singkat guru…"><?= h($data['bio']) ?></textarea>
        <span class="form-help">Tampil di halaman publik. Maksimal 2-3 kalimat.</span>
      </div>

      <div style="display:flex;gap:12px;">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Data
        </button>
        <a href="guru.php" class="btn btn-ghost">
          <i class="fa-solid fa-xmark"></i> Batal
        </a>
      </div>
    </form>
  </div>

  <!-- Photo sidebar -->
  <div class="panel">
    <div class="panel-head" style="padding-bottom:12px;margin-bottom:14px;">
      <h3><i class="fa-solid fa-camera"></i> Foto Guru</h3>
    </div>

    <!-- Current photo preview -->
    <div style="text-align:center;margin-bottom:14px;">
      <?php if ($data['foto']): ?>
      <img src="../assets/uploads/guru/<?= h($data['foto']) ?>"
           id="fotoPreview"
           style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #7952D9;box-shadow:0 4px 14px rgba(121,82,217,.25);">
      <?php else: ?>
      <div id="fotoPreviewPlaceholder"
           style="width:100px;height:100px;border-radius:50%;background:#EDE9FE;border:3px solid #7952D9;display:flex;align-items:center;justify-content:center;margin:0 auto;">
        <i class="fa-solid fa-user" style="color:#7952D9;font-size:38px;"></i>
      </div>
      <img id="fotoPreview" src="" style="display:none;width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #7952D9;margin:0 auto;">
      <?php endif; ?>
    </div>

    <div class="form-group" style="margin-bottom:0;">
      <div class="dropzone" onclick="document.getElementById('fotoInput').click();" style="padding:16px;">
        <i class="fa-solid fa-cloud-arrow-up" style="font-size:22px;display:block;margin-bottom:5px;"></i>
        Klik untuk upload foto
        <div style="font-size:11px;color:#818CF8;font-weight:600;margin-top:3px;">JPG, PNG, WEBP · Maks 3MB</div>
      </div>
      <input type="file" name="foto" id="fotoInput" form="guruForm" accept=".jpg,.jpeg,.png,.webp"
             style="display:none;" onchange="previewFoto(this)">
      <span class="form-help" style="text-align:center;display:block;margin-top:6px;">Biarkan kosong jika tidak ingin mengubah foto.</span>
    </div>
  </div>

</div>

<script>
function previewFoto(input){
  var preview = document.getElementById('fotoPreview');
  var placeholder = document.getElementById('fotoPreviewPlaceholder');
  if(input.files && input.files[0]){
    var reader = new FileReader();
    reader.onload = function(e){
      preview.src = e.target.result;
      preview.style.display = 'block';
      if(placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
