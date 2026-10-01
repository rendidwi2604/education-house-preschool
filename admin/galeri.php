<?php
require __DIR__ . '/includes/auth.php';
$page_title = 'Galeri & Instagram';
$error = '';
$success = '';

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $item = $pdo->prepare("SELECT gambar FROM galeri WHERE id = ?");
    $item->execute([$id]);
    $row = $item->fetch();
    if ($row && file_exists(__DIR__ . '/../assets/uploads/galeri/' . $row['gambar'])) {
        unlink(__DIR__ . '/../assets/uploads/galeri/' . $row['gambar']);
    }
    $pdo->prepare("DELETE FROM galeri WHERE id = ?")->execute([$id]);
    redirect('/admin/galeri.php?hapus_sukses=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_instagram_id'])) {
    $pdo->prepare('DELETE FROM instagram_posts WHERE id = ?')->execute([(int) $_POST['hapus_instagram_id']]);
    redirect('/admin/galeri.php?instagram_hapus=1#instagramAdmin');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_url'])) {
    $postPath = instagram_post_path(trim($_POST['post_url']));
    if ($postPath === null) {
      $error = 'Masukkan link Instagram publik untuk postingan, Reel, atau video.';
    } else {
      $postUrl = 'https://www.instagram.com' . $postPath . '/';
      $stmt = $pdo->prepare('INSERT IGNORE INTO instagram_posts (post_url) VALUES (?)');
      $stmt->execute([$postUrl]);
      if ($stmt->rowCount() > 0) {
        redirect('/admin/galeri.php?instagram_tersimpan=1#instagramAdmin');
        exit;
      }
      $error = 'Link postingan tersebut sudah ada di daftar.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['foto']['name'])) {
    $keterangan = trim($_POST['keterangan'] ?? '');
    $izin = ['jpg','jpeg','png','webp'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $izin)) {
        $error = 'Format foto harus JPG, PNG, atau WEBP.';
    } elseif ($_FILES['foto']['size'] > 3 * 1024 * 1024) {
        $error = 'Ukuran foto maksimal 3MB.';
    } else {
        $namaBaru = 'galeri_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../assets/uploads/galeri/' . $namaBaru);
        $pdo->prepare("INSERT INTO galeri (keterangan, gambar) VALUES (?, ?)")->execute([$keterangan, $namaBaru]);
        redirect('/admin/galeri.php?tersimpan=1');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_kegiatan_islami'])) {
    $judul = trim($_POST['judul_islami'] ?? '');
    $keterangan = trim($_POST['keterangan_islami'] ?? '');
    $instagramInput = trim($_POST['instagram_islami'] ?? '');
    $instagramPath = $instagramInput !== '' ? instagram_post_path($instagramInput) : null;
    $file = $_FILES['gambar_islami'] ?? null;
    $hasFile = $file && $file['error'] !== UPLOAD_ERR_NO_FILE;
    $izin = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    $ext = $file ? strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) : '';
    if ($judul === '' || mb_strlen($judul) > 150 || mb_strlen($keterangan) > 500) {
      $error = 'Judul wajib diisi (maksimal 150 karakter) dan keterangan maksimal 500 karakter.';
    } elseif (($hasFile && $instagramInput !== '') || (!$hasFile && $instagramInput === '')) {
      $error = 'Pilih salah satu sumber konten: unggah foto atau masukkan link Instagram.';
    } elseif (!$hasFile && $instagramInput !== '' && $instagramPath === null) {
      $error = 'Masukkan link Instagram publik untuk postingan, Reel, atau video.';
    } elseif ($hasFile && $file['error'] !== UPLOAD_ERR_OK) {
      $error = 'Foto kegiatan gagal diunggah.';
    } elseif ($hasFile && (!isset($izin[$ext]) || !class_exists('finfo') || (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) !== $izin[$ext])) {
      $error = 'Format foto harus JPG, PNG, atau WEBP yang valid.';
    } elseif ($hasFile && $file['size'] > 3 * 1024 * 1024) {
      $error = 'Ukuran foto maksimal 3MB.';
    } else {
      $gambar = null;
      $instagramUrl = null;
      $thumbnail = null;
      $tersimpan = true;
      if ($hasFile) {
        $folder = __DIR__ . '/../assets/uploads/islami/';
        if (!is_dir($folder)) mkdir($folder, 0755, true);
        $gambar = bin2hex(random_bytes(16)) . '.' . $ext;
        $tersimpan = move_uploaded_file($file['tmp_name'], $folder . $gambar);
      } else {
        $instagramUrl = 'https://www.instagram.com' . $instagramPath . '/';
      }
      if ($tersimpan) {
        $pdo->prepare('INSERT INTO kegiatan_islami (judul, keterangan, gambar, instagram_url) VALUES (?, ?, ?, ?)')->execute([$judul, $keterangan ?: null, $gambar, $instagramUrl]);
        redirect('/admin/galeri.php?islami_tersimpan=1#kegiatanIslamiAdmin');
        exit;
      }
      $error = 'Konten kegiatan gagal disimpan.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_testimoni'])) {
    $keterangan = trim($_POST['keterangan_testimoni'] ?? '');
    $instagramInput = trim($_POST['instagram_testimoni'] ?? '');
    $instagramPath = $instagramInput !== '' ? instagram_post_path($instagramInput) : null;
    $tiktokInput = trim($_POST['tiktok_testimoni'] ?? '');
    $tiktokUrl = $tiktokInput !== '' ? tiktok_canonical_video_url($tiktokInput) : null;
    $file = $_FILES['video_testimoni'] ?? null;
    $hasFile = $file && $file['error'] !== UPLOAD_ERR_NO_FILE;
    $izin = ['mp4' => 'video/mp4', 'webm' => 'video/webm'];
    $ext = $file ? strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) : '';
    if (mb_strlen($keterangan) > 500) {
      $error = 'Keterangan maksimal 500 karakter.';
    } elseif ((int) $hasFile + (int) ($instagramInput !== '') + (int) ($tiktokInput !== '') !== 1) {
      $error = 'Pilih tepat satu sumber testimoni: video, link Instagram, atau link TikTok.';
    } elseif (!$hasFile && $instagramInput !== '' && $instagramPath === null) {
      $error = 'Masukkan link Instagram publik untuk postingan, Reel, atau video.';
    } elseif (!$hasFile && $tiktokInput !== '' && $tiktokUrl === null) {
      $error = 'Masukkan link video TikTok yang valid dan diawali https://.';
    } elseif ($hasFile && $file['error'] !== UPLOAD_ERR_OK) {
      $error = 'Video testimoni gagal diunggah.';
    } elseif (empty($_POST['izin_publikasi'])) {
      $error = 'Konfirmasi izin publikasi video terlebih dahulu.';
    } elseif ($hasFile && (!isset($izin[$ext]) || !class_exists('finfo') || (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) !== $izin[$ext])) {
      $error = 'Format video harus MP4 atau WebM yang valid.';
    } elseif ($hasFile && $file['size'] > 50 * 1024 * 1024) {
      $error = 'Ukuran video maksimal 50MB.';
    } else {
      $video = null;
      $instagramUrl = null;
      $savedTiktokUrl = null;
      $thumbnail = null;
      $tersimpan = true;
      if ($hasFile) {
        $folder = __DIR__ . '/../assets/uploads/testimoni/';
        if (!is_dir($folder)) mkdir($folder, 0755, true);
        $video = bin2hex(random_bytes(16)) . '.' . $ext;
        $tersimpan = move_uploaded_file($file['tmp_name'], $folder . $video);
      } else {
        if ($instagramInput !== '') {
          $instagramUrl = 'https://www.instagram.com' . $instagramPath . '/';
        } else {
          $savedTiktokUrl = $tiktokUrl;
          $thumbnail = tiktok_thumbnail_file($tiktokUrl);
        }
      }
      if ($savedTiktokUrl !== null && $thumbnail === null) {
        $error = 'Gambar sampul TikTok gagal diambil. Pastikan videonya publik lalu coba lagi.';
      } elseif ($tersimpan) {
        $pdo->prepare('INSERT INTO testimoni_orangtua (keterangan, video, instagram_url, tiktok_url) VALUES (?, ?, ?, ?)')->execute([$keterangan ?: null, $video, $instagramUrl, $savedTiktokUrl]);
        redirect('/admin/galeri.php?testimoni_tersimpan=1#testimoniAdmin');
        exit;
      }
      $error = 'Testimoni gagal disimpan.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_kegiatan_islami_id'])) {
    $stmt = $pdo->prepare('SELECT gambar FROM kegiatan_islami WHERE id = ?');
    $stmt->execute([(int) $_POST['hapus_kegiatan_islami_id']]);
    $row = $stmt->fetch();
    if ($row && !empty($row['gambar'])) {
      $file = __DIR__ . '/../assets/uploads/islami/' . basename($row['gambar']);
      if (is_file($file)) unlink($file);
      $pdo->prepare('DELETE FROM kegiatan_islami WHERE id = ?')->execute([(int) $_POST['hapus_kegiatan_islami_id']]);
    }
    redirect('/admin/galeri.php?islami_dihapus=1#kegiatanIslamiAdmin');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_testimoni_id'])) {
    $stmt = $pdo->prepare('SELECT video, tiktok_url FROM testimoni_orangtua WHERE id = ?');
    $stmt->execute([(int) $_POST['hapus_testimoni_id']]);
    $row = $stmt->fetch();
    if ($row) {
      if (!empty($row['video'])) {
        $file = __DIR__ . '/../assets/uploads/testimoni/' . basename($row['video']);
        if (is_file($file)) unlink($file);
      }
      $tiktokVideoId = tiktok_video_id($row['tiktok_url'] ?? null);
      if ($tiktokVideoId) {
        foreach (glob(__DIR__ . '/../assets/uploads/testimoni/tiktok-' . $tiktokVideoId . '.*') ?: [] as $thumbnailFile) {
          if (is_file($thumbnailFile)) unlink($thumbnailFile);
        }
      }
      $pdo->prepare('DELETE FROM testimoni_orangtua WHERE id = ?')->execute([(int) $_POST['hapus_testimoni_id']]);
    }
    redirect('/admin/galeri.php?testimoni_dihapus=1#testimoniAdmin');
    exit;
}

$daftar = $pdo->query("SELECT * FROM galeri ORDER BY created_at DESC")->fetchAll();
$kegiatanIslami = $pdo->query('SELECT * FROM kegiatan_islami ORDER BY created_at DESC')->fetchAll();
$testimoniOrangtua = $pdo->query('SELECT * FROM testimoni_orangtua ORDER BY created_at DESC')->fetchAll();
$instagramPosts = $pdo->query('SELECT id, post_url, created_at FROM instagram_posts ORDER BY created_at DESC, id DESC')->fetchAll();
require __DIR__ . '/includes/admin_header.php';
?>

<?php if (isset($_GET['tersimpan'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Foto berhasil diunggah.</div>
<?php endif; ?>
<?php if (isset($_GET['hapus_sukses'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Foto berhasil dihapus.</div>
<?php endif; ?>
<?php if (isset($_GET['instagram_tersimpan'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Postingan Instagram berhasil ditambahkan.</div>
<?php endif; ?>
<?php if (isset($_GET['instagram_hapus'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Postingan Instagram berhasil dihapus.</div>
<?php endif; ?>
<?php if (isset($_GET['islami_tersimpan']) || isset($_GET['testimoni_tersimpan'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Konten berhasil disimpan dan ditampilkan di website.</div>
<?php endif; ?>
<?php if (isset($_GET['islami_dihapus']) || isset($_GET['testimoni_dihapus'])): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Konten berhasil dihapus.</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:340px 1fr;gap:20px;align-items:start;">

  <!-- Upload form -->
  <div class="panel" style="position:sticky;top:80px;">
    <div class="panel-head" style="padding-bottom:12px;margin-bottom:16px;">
      <h3><i class="fa-solid fa-cloud-arrow-up"></i> Upload Foto Baru</h3>
    </div>
    <form method="post" enctype="multipart/form-data">
      <!-- Drop area -->
      <div class="dropzone" id="dropZone" onclick="document.getElementById('fotoInput').click();">
        <i class="fa-solid fa-images" style="font-size:28px;display:block;margin-bottom:8px;"></i>
        Klik atau seret foto ke sini
        <div style="font-size:11px;color:#818CF8;font-weight:600;margin-top:4px;">JPG, PNG, WEBP · Maksimal 3MB</div>
      </div>
      <input type="file" id="fotoInput" name="foto" accept=".jpg,.jpeg,.png,.webp"
             required style="display:none;" onchange="previewFoto(this)">

      <!-- Preview -->
      <div id="previewWrap" style="display:none;margin-bottom:14px;">
        <img id="previewImg" style="width:100%;border-radius:12px;aspect-ratio:4/3;object-fit:cover;">
        <div id="previewName" style="font-size:12px;color:#6B7280;font-weight:600;margin-top:6px;text-align:center;"></div>
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-pen" style="color:#EC4899;"></i> Keterangan Foto</label>
        <input type="text" name="keterangan" placeholder="Contoh: Lomba mewarnai 17 Agustus">
        <span class="form-help">Opsional — ditampilkan saat foto diklik di galeri.</span>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;">
        <i class="fa-solid fa-cloud-arrow-up"></i> Unggah Foto
      </button>
    </form>
  </div>

  <!-- Gallery grid -->
  <div class="panel">
    <div class="panel-head">
      <h3><i class="fa-solid fa-images"></i> Foto Tersimpan</h3>
      <span style="font-size:12px;font-weight:700;color:#94A3B8;background:#F1F5F9;padding:3px 10px;border-radius:8px;">
        <?= count($daftar) ?> foto
      </span>
    </div>
    <?php if (count($daftar) === 0): ?>
    <div class="empty-note">
      <i class="fa-solid fa-images"></i>
      Belum ada foto. Upload foto pertama di panel kiri.
    </div>
    <?php else: ?>
    <div class="gal-grid">
      <?php foreach ($daftar as $g): ?>
      <div class="item">
        <div style="position:relative;overflow:hidden;">
          <img src="../assets/uploads/galeri/<?= h($g['gambar']) ?>"
               alt="<?= h($g['keterangan']) ?>"
               style="transition:transform .3s;"
               onmouseenter="this.style.transform='scale(1.06)'"
               onmouseleave="this.style.transform='scale(1)'">
          <!-- delete overlay -->
          <div style="position:absolute;top:6px;right:6px;">
            <a href="galeri.php?hapus=<?= $g['id'] ?>"
               onclick="return confirm('Hapus foto ini?')"
               style="width:28px;height:28px;background:rgba(239,68,68,.85);color:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;text-decoration:none;transition:background .15s;"
               onmouseenter="this.style.background='rgba(239,68,68,1)'"
               onmouseleave="this.style.background='rgba(239,68,68,.85)'">
              <i class="fa-solid fa-trash"></i>
            </a>
          </div>
        </div>
        <p class="cap">
          <?= $g['keterangan'] ? h($g['keterangan']) : '<em style="color:#C4C4C4;">Tanpa keterangan</em>' ?>
        </p>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

</div>

<div class="panel" id="kegiatanIslamiAdmin" style="margin-top:20px;">
  <div class="panel-head"><h3><i class="fa-solid fa-moon"></i> Kegiatan Islami Anak</h3></div>
  <form method="post" enctype="multipart/form-data" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;align-items:end;margin-bottom:20px;">
    <div class="form-group" style="margin:0;"><label for="judul_islami">Judul Kegiatan</label><input id="judul_islami" name="judul_islami" maxlength="150" required placeholder="Contoh: Belajar doa harian"></div>
    <div class="form-group" style="margin:0;"><label for="keterangan_islami">Keterangan</label><input id="keterangan_islami" name="keterangan_islami" maxlength="500" placeholder="Keterangan singkat (opsional)"></div>
    <div class="form-group" style="margin:0;"><label for="gambar_islami">Foto manual (JPG, PNG, WEBP · maks. 3MB)</label><input id="gambar_islami" type="file" name="gambar_islami" accept=".jpg,.jpeg,.png,.webp"></div>
    <div class="form-group" style="margin:0;"><label for="instagram_islami">Atau link Instagram</label><input id="instagram_islami" type="url" name="instagram_islami" maxlength="500" placeholder="https://www.instagram.com/p/..."></div>
    <div style="grid-column:1/-1;font-size:12px;color:#64748B;">Pilih satu sumber saja: foto manual atau link postingan/Reel Instagram publik.</div>
    <button type="submit" name="upload_kegiatan_islami" value="1" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i> Simpan Kegiatan</button>
  </form>
  <?php if (!$kegiatanIslami): ?><div class="empty-note">Belum ada kegiatan Islami yang diunggah.</div><?php else: ?>
  <div class="gal-grid">
    <?php foreach ($kegiatanIslami as $kegiatan): ?>
    <div class="item">
      <?php if (!empty($kegiatan['instagram_url'])): ?><a href="<?= h($kegiatan['instagram_url']) ?>" target="_blank" rel="noopener" class="cap">Lihat postingan Instagram</a><?php else: ?><img src="../assets/uploads/islami/<?= h($kegiatan['gambar']) ?>" alt="<?= h($kegiatan['judul']) ?>"><?php endif; ?>
      <p class="cap"><?= h($kegiatan['judul']) ?></p>
      <form method="post" onsubmit="return confirm('Hapus kegiatan ini dari website?')"><input type="hidden" name="hapus_kegiatan_islami_id" value="<?= (int) $kegiatan['id'] ?>"><button type="submit" class="btn btn-ghost btn-sm" style="color:#DC2626;"><i class="fa-solid fa-trash"></i> Hapus</button></form>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<div class="panel" id="testimoniAdmin" style="margin-top:20px;">
  <div class="panel-head"><h3><i class="fa-solid fa-video"></i> Video Testimoni Orang Tua</h3></div>
  <form method="post" enctype="multipart/form-data" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;align-items:end;margin-bottom:20px;">
    <div class="form-group" style="margin:0;"><label for="keterangan_testimoni">Cerita Perkembangan Anak</label><input id="keterangan_testimoni" name="keterangan_testimoni" maxlength="500" placeholder="Ringkasan testimoni (opsional)"></div>
    <div class="form-group" style="margin:0;"><label for="video_testimoni">Video manual (MP4/WebM · maks. 50MB)</label><input id="video_testimoni" type="file" name="video_testimoni" accept="video/mp4,video/webm,.mp4,.webm"></div>
    <div class="form-group" style="margin:0;"><label for="instagram_testimoni">Atau link Instagram</label><input id="instagram_testimoni" type="url" name="instagram_testimoni" maxlength="500" placeholder="https://www.instagram.com/reel/..."></div>
    <div class="form-group" style="margin:0;"><label for="tiktok_testimoni">Atau link TikTok</label><input id="tiktok_testimoni" type="url" name="tiktok_testimoni" maxlength="500" placeholder="https://www.tiktok.com/@.../video/..."></div>
    <div style="grid-column:1/-1;font-size:12px;color:#64748B;">Pilih satu sumber saja. Postingan Instagram harus publik; link TikTok akan dibuka di TikTok.</div>
    <label style="display:flex;gap:8px;align-items:flex-start;font-size:12px;line-height:1.5;color:#475569;"><input type="checkbox" name="izin_publikasi" value="1" required style="margin-top:3px;"> Saya telah mendapat izin untuk menampilkan testimoni ini di website.</label>
    <button type="submit" name="upload_testimoni" value="1" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i> Simpan Testimoni</button>
  </form>
  <?php if (!$testimoniOrangtua): ?><div class="empty-note">Belum ada video testimoni yang diunggah.</div><?php else: ?>
  <div style="overflow-x:auto;"><table style="min-width:520px;"><thead><tr><th>Konten</th><th>Keterangan</th><th>Aksi</th></tr></thead><tbody>
    <?php foreach ($testimoniOrangtua as $testimoni): ?>
    <tr><td><?php if (!empty($testimoni['instagram_url'])): ?><a href="<?= h($testimoni['instagram_url']) ?>" target="_blank" rel="noopener">Lihat Instagram</a><?php elseif (!empty($testimoni['tiktok_url'])): ?><a href="<?= h($testimoni['tiktok_url']) ?>" target="_blank" rel="noopener">Lihat TikTok</a><?php else: ?><video controls preload="metadata" style="width:160px;max-height:100px;background:#111;"><source src="../assets/uploads/testimoni/<?= h($testimoni['video']) ?>"></video><?php endif; ?></td><td><?= h($testimoni['keterangan'] ?? '-') ?></td><td><form method="post" onsubmit="return confirm('Hapus testimoni ini?')"><input type="hidden" name="hapus_testimoni_id" value="<?= (int) $testimoni['id'] ?>"><button type="submit" class="btn btn-ghost btn-sm" style="color:#DC2626;"><i class="fa-solid fa-trash"></i> Hapus</button></form></td></tr>
    <?php endforeach; ?>
  </tbody></table></div>
  <?php endif; ?>
</div>

<div class="panel" id="instagramAdmin" style="margin-top:20px;">
  <div class="panel-head">
    <h3><i class="fa-brands fa-instagram"></i> Postingan Instagram</h3>
    <span style="font-size:12px;font-weight:700;color:#94A3B8;background:#F1F5F9;padding:3px 10px;border-radius:8px;">
      <?= count($instagramPosts) ?> postingan
    </span>
  </div>
  <p style="font-size:13px;color:#64748B;line-height:1.6;margin:0 0 18px;">
    Tambahkan link postingan publik di sini. Semua postingan akan tampil bersama foto di Momen Ceria.
  </p>
  <form method="post" style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
    <div class="form-group" style="flex:1;min-width:240px;margin:0;">
      <label for="post_url"><i class="fa-solid fa-link" style="color:#7952D9;"></i> Link Instagram</label>
      <input type="url" id="post_url" name="post_url" required maxlength="500"
             placeholder="https://www.instagram.com/reel/.../">
      <span class="form-help">Postingan harus publik dan mengizinkan penyematan.</span>
    </div>
    <button type="submit" class="btn btn-primary">
      <i class="fa-solid fa-plus"></i> Tambahkan
    </button>
  </form>

  <?php if (!$instagramPosts): ?>
  <div class="empty-note"><i class="fa-brands fa-instagram"></i> Belum ada postingan Instagram.</div>
  <?php else: ?>
  <div style="overflow-x:auto;">
    <table style="min-width:520px;">
      <thead>
        <tr><th>Link Postingan</th><th>Tanggal Ditambahkan</th><th style="text-align:right;">Aksi</th></tr>
      </thead>
      <tbody>
        <?php foreach ($instagramPosts as $post): ?>
        <tr>
          <td><a href="<?= h($post['post_url']) ?>" target="_blank" rel="noopener" style="color:#7952D9;font-weight:700;">Buka Instagram</a></td>
          <td><?= h(tgl($post['created_at'])) ?></td>
          <td style="text-align:right;">
            <form method="post" style="display:inline;" onsubmit="return confirm('Hapus postingan ini dari website?')">
              <input type="hidden" name="hapus_instagram_id" value="<?= (int) $post['id'] ?>">
              <button type="submit" class="btn btn-ghost btn-sm" style="color:#DC2626;">
                <i class="fa-solid fa-trash"></i> Hapus
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script>
function previewFoto(input){
  var wrap=document.getElementById('previewWrap');
  var img=document.getElementById('previewImg');
  var name=document.getElementById('previewName');
  var dz=document.getElementById('dropZone');
  if(input.files && input.files[0]){
    var reader=new FileReader();
    reader.onload=function(e){
      img.src=e.target.result;
      name.textContent=input.files[0].name;
      wrap.style.display='block';
      dz.style.display='none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
