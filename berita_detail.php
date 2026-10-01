<?php
require 'config/db.php';
require 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ? AND status = 'terbit'");
$stmt->execute([$id]);
$berita = $stmt->fetch();

// Ambil semua gambar dari tabel berita_gambar
$gambarList = [];
if ($berita) {
    try {
        $stmtG = $pdo->prepare(
            "SELECT url FROM berita_gambar WHERE berita_id = ? ORDER BY urutan ASC, id ASC"
        );
        $stmtG->execute([$id]);
        $gambarList = array_column($stmtG->fetchAll(), 'url');
    } catch (PDOException $e) {
        // Fallback ke kolom gambar lama jika tabel belum ada
        if (!empty($berita['gambar'])) {
            $gambarList[] = str_starts_with($berita['gambar'], 'https://')
                ? $berita['gambar']
                : 'assets/uploads/galeri/' . $berita['gambar'];
        }
    }

    // Jika tabel ada tapi kosong, coba fallback kolom lama
    if (empty($gambarList) && !empty($berita['gambar'])) {
        $gambarList[] = str_starts_with($berita['gambar'], 'https://')
            ? $berita['gambar']
            : 'assets/uploads/galeri/' . $berita['gambar'];
    }
}

if (!$berita) {
    http_response_code(404);
    $page_title = 'Berita Tidak Ditemukan';
} else {
    $page_title = $berita['judul'];
    // SEO meta description dari isi berita
    $meta_description = mb_strimwidth(strip_tags($berita['isi']), 0, 160, '...');
}

require 'includes/header.php';
?>

<main style="max-width:860px;margin:0 auto;padding:32px 16px 64px;">

  <?php if (!$berita): ?>
  <!-- ── 404 ────────────────────────────────────────────── -->
  <div style="text-align:center;padding:60px 20px;">
    <div style="font-size:64px;margin-bottom:16px;">📭</div>
    <h1 style="font-family:'Quicksand',sans-serif;font-size:24px;font-weight:900;color:#1E293B;margin:0 0 8px;">
      Berita Tidak Ditemukan
    </h1>
    <p style="color:#64748B;margin:0 0 24px;">
      Berita yang Anda cari tidak tersedia atau sudah tidak diterbitkan.
    </p>
    <a href="index.php#berita"
       style="display:inline-flex;align-items:center;gap:8px;background:#328C39;color:#fff;padding:11px 24px;border-radius:12px;text-decoration:none;font-weight:700;font-size:14px;">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
      Kembali ke Berita
    </a>
  </div>

  <?php else: ?>
  <!-- ── Artikel ───────────────────────────────────────── -->
  <article>

    <!-- Breadcrumb -->
    <nav style="display:flex;align-items:center;gap:6px;font-size:12px;color:#94A3B8;font-weight:600;margin-bottom:20px;flex-wrap:wrap;">
      <a href="index.php" style="color:#94A3B8;text-decoration:none;" onmouseover="this.style.color='#328C39'" onmouseout="this.style.color='#94A3B8'">Beranda</a>
      <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      <a href="index.php#berita" style="color:#94A3B8;text-decoration:none;" onmouseover="this.style.color='#328C39'" onmouseout="this.style.color='#94A3B8'">Berita</a>
      <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      <span style="color:#374151;"><?= h(mb_strimwidth($berita['judul'], 0, 40, '...')) ?></span>
    </nav>

    <!-- Header artikel -->
    <header style="margin-bottom:28px;">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
        <span style="background:#DCFCE7;color:#166534;font-size:11px;font-weight:800;padding:3px 12px;border-radius:999px;text-transform:uppercase;letter-spacing:.04em;">
          <?= h($berita['kategori']) ?>
        </span>
        <?php if (!empty($gambarList)): ?>
        <span style="background:#EEF2FF;color:#4F46E5;font-size:11px;font-weight:800;padding:3px 12px;border-radius:999px;">
          <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:2px;"><path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd"/></svg>
          <?= count($gambarList) ?> foto
        </span>
        <?php endif; ?>
      </div>

      <h1 style="font-family:'Quicksand',sans-serif;font-size:clamp(20px,4vw,30px);font-weight:900;color:#0F172A;line-height:1.3;margin:0 0 10px;">
        <?= h($berita['judul']) ?>
      </h1>

      <time style="font-size:13px;color:#64748B;font-weight:600;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:3px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
        <?= tgl($berita['created_at']) ?>
      </time>
    </header>

    <?php if (!empty($gambarList)): ?>
    <!-- ── Galeri Gambar ──────────────────────────────── -->
    <div style="margin-bottom:32px;">

      <?php if (count($gambarList) === 1): ?>
      <!-- Satu gambar: tampilkan penuh -->
      <?php $gUrl = $gambarList[0]; $gWebp = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $gUrl); ?>
      <picture>
        <source srcset="<?= h($gWebp) ?>" type="image/webp">
        <img src="<?= h($gUrl) ?>"
             alt="<?= h($berita['judul']) ?>"
             fetchpriority="high"
             style="width:100%;border-radius:20px;object-fit:cover;max-height:480px;border:3px solid #E8ECF4;display:block;">
      </picture>

      <?php else: ?>
      <!-- Banyak gambar: main image + grid thumbnail -->

      <!-- Main image (lightbox trigger) -->
      <div style="position:relative;margin-bottom:10px;cursor:pointer;border-radius:20px;overflow:hidden;border:3px solid #E8ECF4;background:#F8FAFC;"
           onclick="openLightbox(0)" id="mainImgWrap">
        <?php $g0 = $gambarList[0]; $g0webp = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $g0); ?>
        <picture>
          <source srcset="<?= h($g0webp) ?>" type="image/webp">
          <img src="<?= h($g0) ?>"
               alt="<?= h($berita['judul']) ?> — foto 1"
               fetchpriority="high"
               id="mainImg"
               style="width:100%;max-height:440px;object-fit:cover;display:block;transition:transform .3s;"
               onmouseover="this.style.transform='scale(1.02)'"
               onmouseout="this.style.transform='scale(1)'">
        </picture>
        <!-- overlay hint -->
        <div style="position:absolute;bottom:12px;right:12px;background:rgba(0,0,0,.55);color:#fff;font-size:11px;font-weight:800;padding:4px 10px;border-radius:8px;display:flex;align-items:center;gap:5px;">
          <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M15.75 4.5a3 3 0 11.825 2.066 1.125 1.125 0 010-.132A3 3 0 0115.75 4.5zm0 13.5a3 3 0 11-.001-5.998A3 3 0 0115.75 18zm-11.25-9a3 3 0 11-.001-5.998A3 3 0 014.5 9zm0 9a3 3 0 11-.001-5.999A3 3 0 014.5 18z" clip-rule="evenodd"/></svg>
          1 / <?= count($gambarList) ?>
        </div>
      </div>

      <!-- Thumbnail strip -->
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:8px;">
        <?php foreach ($gambarList as $gi => $gUrl): ?>
        <?php $gWebp = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $gUrl); ?>
        <div onclick="switchMain(<?= $gi ?>)"
             id="thumb-<?= $gi ?>"
             style="border-radius:10px;overflow:hidden;cursor:pointer;aspect-ratio:1;border:2.5px solid <?= $gi===0 ? '#328C39' : '#E8ECF4' ?>;transition:border-color .2s,transform .2s;background:#F8FAFC;"
             onmouseover="this.style.transform='scale(1.05)'"
             onmouseout="this.style.transform='scale(1)'">
          <picture>
            <source srcset="<?= h($gWebp) ?>" type="image/webp">
            <img src="<?= h($gUrl) ?>"
                 alt="Foto <?= $gi+1 ?>"
                 style="width:100%;height:100%;object-fit:cover;"
                 loading="lazy">
          </picture>
        </div>
        <?php endforeach; ?>
      </div>

      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Isi artikel ────────────────────────────────── -->
    <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;line-height:1.85;color:#334155;">
      <?= nl2br(h($berita['isi'])) ?>
    </div>

    <!-- ── Footer artikel ────────────────────────────── -->
    <footer style="margin-top:36px;padding-top:20px;border-top:1px solid #E8ECF4;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;">
      <a href="index.php#berita"
         style="display:inline-flex;align-items:center;gap:8px;background:#F1F5F9;color:#475569;padding:10px 20px;border-radius:12px;text-decoration:none;font-weight:700;font-size:13.5px;transition:background .15s;"
         onmouseover="this.style.background='#E2E8F0'" onmouseout="this.style.background='#F1F5F9'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Kembali ke Berita
      </a>

      <!-- Share WhatsApp -->
      <?php
        $shareText = rawurlencode($berita['judul'] . ' — ' . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] . '/berita_detail.php?id=' . $id : ''));
      ?>
      <a href="https://wa.me/?text=<?= $shareText ?>" target="_blank" rel="noopener noreferrer"
         style="display:inline-flex;align-items:center;gap:8px;background:#25D366;color:#fff;padding:10px 20px;border-radius:12px;text-decoration:none;font-weight:700;font-size:13.5px;">
        <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Bagikan
      </a>
    </footer>
  </article>

  <?php if (count($gambarList) > 1): ?>
  <!-- ── Lightbox ──────────────────────────────────────── -->
  <div id="lbOverlay"
       style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;align-items:center;justify-content:center;padding:16px;"
       onclick="if(event.target===this)closeLb()">
    <div style="position:relative;max-width:900px;width:100%;max-height:90vh;">

      <!-- Close -->
      <button onclick="closeLb()" aria-label="Tutup"
              style="position:absolute;top:-40px;right:0;background:rgba(255,255,255,.15);border:none;color:#fff;width:36px;height:36px;border-radius:9px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>

      <!-- Image -->
      <img id="lbImg" src="" alt=""
           style="width:100%;max-height:80vh;object-fit:contain;border-radius:16px;display:block;">

      <!-- Counter -->
      <div style="text-align:center;margin-top:10px;color:rgba(255,255,255,.7);font-size:13px;font-weight:700;">
        <span id="lbCounter"></span>
      </div>

      <!-- Prev / Next -->
      <button onclick="lbNav(-1)" aria-label="Sebelumnya"
              style="position:absolute;left:-52px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:none;color:#fff;width:40px;height:40px;border-radius:10px;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
      </button>
      <button onclick="lbNav(1)" aria-label="Berikutnya"
              style="position:absolute;right:-52px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:none;color:#fff;width:40px;height:40px;border-radius:10px;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      </button>
    </div>
  </div>

  <script>
  var lbImages = <?= json_encode($gambarList, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
  var lbCurrent = 0;

  function openLightbox(idx) {
    lbCurrent = idx;
    updateLb();
    document.getElementById('lbOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closeLb() {
    document.getElementById('lbOverlay').style.display = 'none';
    document.body.style.overflow = '';
  }

  function lbNav(dir) {
    lbCurrent = (lbCurrent + dir + lbImages.length) % lbImages.length;
    updateLb();
  }

  function updateLb() {
    document.getElementById('lbImg').src = lbImages[lbCurrent];
    document.getElementById('lbCounter').textContent = (lbCurrent + 1) + ' / ' + lbImages.length;
  }

  // Switch main image + highlight thumbnail
  var currentMainIdx = 0;
  function switchMain(idx) {
    var mainImg  = document.getElementById('mainImg');
    var oldThumb = document.getElementById('thumb-' + currentMainIdx);
    var newThumb = document.getElementById('thumb-' + idx);
    var counter  = document.querySelector('#mainImgWrap div');

    if (mainImg)  mainImg.src = lbImages[idx];
    if (oldThumb) oldThumb.style.borderColor = '#E8ECF4';
    if (newThumb) newThumb.style.borderColor = '#328C39';
    if (counter)  counter.childNodes[counter.childNodes.length-1].textContent = ' ' + (idx+1) + ' / ' + lbImages.length;

    currentMainIdx = idx;
  }

  document.addEventListener('keydown', function(e) {
    var lb = document.getElementById('lbOverlay');
    if (lb.style.display === 'none') return;
    if (e.key === 'Escape')      closeLb();
    if (e.key === 'ArrowLeft')   lbNav(-1);
    if (e.key === 'ArrowRight')  lbNav(1);
  });
  </script>
  <?php endif; ?>

  <?php endif; ?>
</main>

<?php require 'includes/footer.php'; ?>
