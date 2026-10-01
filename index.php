<?php
require 'config/db.php';
require 'includes/functions.php';

$galeri  = $pdo->query("SELECT * FROM galeri ORDER BY created_at DESC LIMIT 12")->fetchAll();
$kegiatanIslami = [];
$testimoniOrangtua = [];
try {
  $kegiatanIslami = $pdo->query('SELECT * FROM kegiatan_islami ORDER BY created_at DESC')->fetchAll();
  $testimoniOrangtua = $pdo->query('SELECT * FROM testimoni_orangtua ORDER BY created_at DESC')->fetchAll();
} catch (PDOException $e) { /* migrasi kegiatan dan testimoni belum dijalankan */ }
$berita  = $pdo->query("SELECT * FROM berita WHERE status = 'terbit' AND kategori = 'Prestasi' ORDER BY created_at DESC")->fetchAll();

// Ambil semua gambar untuk berita yang ditampilkan
$beritaGambarMap = [];
try {
    $beritaIds = array_column($berita, 'id');
    if (!empty($beritaIds)) {
        $placeholders = implode(',', array_fill(0, count($beritaIds), '?'));
        $stmtBG = $pdo->prepare(
            "SELECT * FROM berita_gambar WHERE berita_id IN ($placeholders) ORDER BY berita_id, urutan ASC, id ASC"
        );
        $stmtBG->execute($beritaIds);
        foreach ($stmtBG->fetchAll() as $bg) {
            $beritaGambarMap[$bg['berita_id']][] = $bg['url'];
        }
    }
} catch (PDOException $e) {
    // Tabel belum ada, fallback ke kolom gambar lama
    foreach ($berita as $b) {
        if (!empty($b['gambar'])) {
            $beritaGambarMap[$b['id']][] = str_starts_with($b['gambar'], 'https://')
                ? $b['gambar']
                : 'assets/uploads/galeri/' . $b['gambar'];
        }
    }
}
$guru    = $pdo->query("SELECT * FROM guru ORDER BY created_at ASC")->fetchAll();
$instagramPosts = [];
try {
  $savedInstagramPosts = $pdo->query('SELECT id, post_url FROM instagram_posts ORDER BY created_at DESC, id DESC')->fetchAll();
  foreach ($savedInstagramPosts as $post) {
    $instagramPath = instagram_post_path($post['post_url']);
    if ($instagramPath) {
      $instagramPosts[] = [
        'id' => (int) $post['id'],
        'embed_url' => 'https://www.instagram.com' . $instagramPath . '/embed/',
      ];
    }
  }
} catch (PDOException $e) { /* pengaturan belum dimigrasikan */ }

// Slider hero — ambil yang aktif, urut sesuai urutan
$sliders = [];
try {
    $sliders = $pdo->query("SELECT * FROM slider WHERE aktif = TRUE ORDER BY urutan ASC, id ASC")->fetchAll();
} catch (PDOException $e) { /* tabel belum ada, abaikan */ }

// Fallback: satu slide default jika tabel kosong / belum ada
if (empty($sliders)) {
    $sliders = [[
        'id'       => 0,
        'judul'    => 'Selamat Datang di Education House!',
        'subjudul' => 'Tempat bermain dan belajar yang menyenangkan untuk si kecil.',
        'gambar'   => '',
        'aktif'    => 1,
    ]];
}

$sukses = isset($_GET['sukses']);
$gagal  = isset($_GET['gagal']);
$nomor_whatsapp_sekolah = '6285863649047';
$nomor_whatsapp_tampil = '0858-6364-9047';
$pesan_whatsapp = rawurlencode('Halo Admin, saya ingin menanyakan info tentang Education House Preschool.');

require 'includes/header.php';
?>

<!-- ══════════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════════ -->
<section id="beranda" class="relative overflow-hidden py-8 sm:py-10 lg:py-16 bg-gradient-to-br from-green-50 via-lime-50/70 to-orange-50/80">
  <!-- Playful 3D-style doodles -->
  <!-- Star top-left -->
  <svg class="absolute top-10 left-10 w-20 h-20 pointer-events-none opacity-40" viewBox="0 0 100 100">
    <defs>
      <linearGradient id="starGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" style="stop-color:#FCD34D;stop-opacity:1" />
        <stop offset="100%" style="stop-color:#F59E0B;stop-opacity:1" />
      </linearGradient>
    </defs>
    <path d="M50 5 L61 38 L95 38 L68 58 L79 91 L50 71 L21 91 L32 58 L5 38 L39 38 Z" fill="url(#starGrad)" stroke="#D97706" stroke-width="2"/>
    <ellipse cx="50" cy="75" rx="30" ry="8" fill="#000" opacity="0.15"/>
  </svg>

  <!-- Heart bottom-left -->
  <svg class="absolute bottom-16 left-1/4 w-16 h-16 pointer-events-none opacity-50" viewBox="0 0 100 100">
    <defs>
      <linearGradient id="heartGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" style="stop-color:#FB7185;stop-opacity:1" />
        <stop offset="100%" style="stop-color:#EC4899;stop-opacity:1" />
      </linearGradient>
    </defs>
    <path d="M50 85 C50 85, 20 60, 20 40 C20 25, 30 20, 40 25 C45 27, 50 32, 50 32 C50 32, 55 27, 60 25 C70 20, 80 25, 80 40 C80 60, 50 85, 50 85 Z" fill="url(#heartGrad)" stroke="#BE185D" stroke-width="2"/>
    <ellipse cx="50" cy="90" rx="20" ry="5" fill="#000" opacity="0.1"/>
    <!-- shine -->
    <circle cx="35" cy="35" r="5" fill="#fff" opacity="0.7"/>
  </svg>

  <!-- Balloon top-right -->
  <svg class="absolute top-1/3 right-10 w-16 h-20 pointer-events-none opacity-40" viewBox="0 0 80 100">
    <defs>
      <linearGradient id="balloonGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" style="stop-color:#67E8F9;stop-opacity:1" />
        <stop offset="100%" style="stop-color:#06B6D4;stop-opacity:1" />
      </linearGradient>
    </defs>
    <ellipse cx="40" cy="35" rx="28" ry="35" fill="url(#balloonGrad)" stroke="#0891B2" stroke-width="2"/>
    <circle cx="28" cy="22" r="8" fill="#fff" opacity="0.5"/>
    <path d="M40 70 Q35 85 40 95" stroke="#0891B2" stroke-width="2" fill="none"/>
    <polygon points="38,95 40,100 42,95" fill="#BE185D"/>
  </svg>

  <!-- Paper plane top-right -->
  <svg class="absolute top-16 right-1/4 w-16 h-16 pointer-events-none opacity-35" viewBox="0 0 100 100">
    <defs>
      <linearGradient id="planeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" style="stop-color:#FED7AA;stop-opacity:1" />
        <stop offset="100%" style="stop-color:#FB923C;stop-opacity:1" />
      </linearGradient>
    </defs>
    <g transform="rotate(-15 50 50)">
      <path d="M10 50 L90 20 L50 50 L90 80 Z" fill="url(#planeGrad)" stroke="#EA580C" stroke-width="2"/>
      <path d="M50 50 L40 70 L50 65 Z" fill="#F97316" stroke="#EA580C" stroke-width="1.5"/>
    </g>
  </svg>

  <!-- Cloud decoration -->
  <svg class="absolute top-20 left-1/3 w-24 h-16 pointer-events-none opacity-20" viewBox="0 0 120 80">
    <ellipse cx="30" cy="50" rx="25" ry="20" fill="#CBD5E1"/>
    <ellipse cx="60" cy="40" rx="35" ry="28" fill="#CBD5E1"/>
    <ellipse cx="90" cy="50" rx="25" ry="20" fill="#CBD5E1"/>
  </svg>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl lg:rounded-5xl shadow-xl shadow-slate-200/70 border border-slate-100 p-5 sm:p-10 lg:p-14 relative overflow-hidden">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">

        <!-- Left: Text -->
        <div class="lg:col-span-6 space-y-5 sm:space-y-6 text-center lg:text-left">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-amber-50 border border-amber-200/80 text-amber-900 text-xs font-bold shadow-sm">
            <span class="w-6 h-6 rounded-full bg-gradient-to-r from-red-400 via-yellow-400 to-blue-400 flex items-center justify-center text-white text-[11px] font-black">
              <i class="fa-solid fa-rainbow"></i>
            </span>
            <span>Penerimaan Peserta Baru Dibuka Setiap Hari!</span>
          </div>

          <!-- Heading -->
          <h1 class="font-heading font-black text-3xl sm:text-4xl lg:text-5xl text-slate-800 leading-[1.2]">
            Sekolah Calon Pemimpin Masa Depan dan Ramah anak
           
            <span class="text-kid-pink inline-block ml-1">♥</span>
          </h1>

          <p class="text-slate-600 text-base sm:text-lg leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
            Tempat ideal dan terpercaya bagi buah hati Anda untuk belajar membaca, berhitung, bereksplorasi sensorik, dan bersinar dengan penuh keceriaan setiap hari.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 sm:gap-4 pt-2">
            <a href="#pendaftaran" class="px-3 py-3 sm:px-8 sm:py-3.5 rounded-full bg-kid-purple hover:bg-kid-purple-dark text-white font-heading font-bold text-sm sm:text-base shadow-lg shadow-green-300/50 transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
              Daftar Sekarang <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
            <a href="#kegiatan" class="px-3 py-3 sm:px-7 sm:py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-sm sm:text-base transition-all hover:scale-105">
              Lihat Kegiatan
            </a>
          </div>

          <!-- Value Props -->
          <div class="pt-5 sm:pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="flex items-center gap-2.5 p-1.5 sm:p-2 rounded-xl bg-emerald-50/70 border border-emerald-100">
              <div class="w-8 h-8 rounded-full bg-kid-green text-white flex items-center justify-center text-sm">
                <i class="fa-solid fa-shield-cat"></i>
              </div>
              <div class="text-left leading-tight">
                <span class="block text-xs font-bold text-slate-800">Lingkungan Aman</span>
                <span class="text-[11px] text-slate-500 font-medium">CCTV & Ber-AC</span>
              </div>
            </div>
            <div class="flex items-center gap-2.5 p-1.5 sm:p-2 rounded-xl bg-rose-50/70 border border-rose-100">
              <div class="w-8 h-8 rounded-full bg-kid-pink text-white flex items-center justify-center text-sm">
                <i class="fa-solid fa-chalkboard-user"></i>
              </div>
              <div class="text-left leading-tight">
                <span class="block text-xs font-bold text-slate-800">Guru Berpengalaman</span>
                <span class="text-[11px] text-slate-500 font-medium"> Pendidikan & Ramah</span>
              </div>
            </div>
            <div class="flex items-center gap-2.5 p-1.5 sm:p-2 rounded-xl bg-amber-50/70 border border-amber-100">
              <div class="w-8 h-8 rounded-full bg-kid-amber text-white flex items-center justify-center text-sm">
                <i class="fa-solid fa-shapes"></i>
              </div>
              <div class="text-left leading-tight">
                <span class="block text-xs font-bold text-slate-800">Bermain Sambil Belajar</span>
                <span class="text-[11px] text-slate-500 font-medium">Fun & Karakter</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Hero Slider -->
        <div class="lg:col-span-6 relative">
          <div class="relative mx-auto max-w-[280px] sm:max-w-xs lg:max-w-sm" id="heroSliderWrap">

            <!-- Gradient backdrop glow -->
            <div id="heroGlow" class="absolute -inset-2 bg-gradient-to-tr from-kid-purple via-kid-amber to-kid-blue rounded-3xl opacity-30 blur-lg pointer-events-none transition-all duration-700"></div>

            <!-- Slides container -->
            <div class="relative rounded-3xl overflow-hidden border-4 border-white shadow-2xl bg-slate-100" id="heroSlider">

              <?php foreach ($sliders as $si => $sl): ?>
              <?php
                $gambarVal = $sl['gambar'] ?? '';
                // Cek apakah gambar adalah URL Supabase Storage atau nama file lokal
                if (str_starts_with($gambarVal, 'https://')) {
                    // Supabase Storage URL langsung
                    $rawSrc = $gambarVal;
                    $imgSrc = $gambarVal;
                } elseif (!empty($gambarVal)) {
                    // File lokal (data lama dari assets/uploads/slider)
                    $rawSrc = 'assets/uploads/slider/' . $gambarVal;
                    $imgSrc = webp_src('assets/uploads/slider', $gambarVal);
                } else {
                    $rawSrc = 'assets/img/Ref.jpg';
                    $imgSrc = 'assets/img/Ref.webp';
                }
                $imgAlt  = h($sl['judul'] ?? 'Slide ' . ($si+1));
                $isFirst = ($si === 0);
              ?>
              <div class="hero-slide <?= $isFirst ? 'active' : '' ?>"
                   data-index="<?= $si ?>"
                   style="display:<?= $isFirst ? 'block' : 'none' ?>;position:relative;">
                <picture>
                  <source srcset="<?= h($imgSrc) ?>" type="image/webp">
                  <img src="<?= h($rawSrc) ?>"
                       alt="<?= $imgAlt ?>"
                       class="w-full h-auto block"
                       style="width:100%;height:auto;max-height:none;object-fit:contain;object-position:center top;"
                       <?= $isFirst ? 'fetchpriority="high"' : 'loading="lazy"' ?>
                       width="480" height="480">
                </picture>

                <?php if (!empty($sl['judul']) || !empty($sl['subjudul'])): ?>
                <!-- Caption overlay -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent px-5 pt-10 pb-4">
                  <?php if (!empty($sl['judul'])): ?>
                  <p class="font-heading font-black text-white text-base sm:text-lg leading-tight drop-shadow">
                    <?= h($sl['judul']) ?>
                  </p>
                  <?php endif; ?>
                  <?php if (!empty($sl['subjudul'])): ?>
                  <p class="text-white/80 text-xs sm:text-sm font-medium mt-1 drop-shadow">
                    <?= h($sl['subjudul']) ?>
                  </p>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>

              <!-- Nav arrows (tampil hanya jika > 1 slide) -->
              <?php if (count($sliders) > 1): ?>
              <button onclick="heroNav(-1)"
                      class="absolute left-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/80 hover:bg-white shadow-md flex items-center justify-center transition-all hover:scale-110"
                      aria-label="Slide sebelumnya">
                <i class="fa-solid fa-chevron-left text-slate-700 text-xs"></i>
              </button>
              <button onclick="heroNav(1)"
                      class="absolute right-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/80 hover:bg-white shadow-md flex items-center justify-center transition-all hover:scale-110"
                      aria-label="Slide berikutnya">
                <i class="fa-solid fa-chevron-right text-slate-700 text-xs"></i>
              </button>
              <?php endif; ?>

              <!-- Floating pill -->
              <div class="absolute top-3 right-3 z-20 bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-xl shadow border border-slate-100 flex items-center gap-2">
                <span class="flex h-2.5 w-2.5 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span class="text-[11px] font-bold text-slate-700">Belajar Aktif Setiap Hari</span>
              </div>
            </div>

            <!-- Dot indicators + slide counter -->
            <?php if (count($sliders) > 1): ?>
            <div class="flex justify-center items-center gap-2 mt-4" id="heroDots">
              <?php foreach ($sliders as $si => $_): ?>
              <button onclick="heroGoTo(<?= $si ?>)"
                      class="hero-dot transition-all duration-300 rounded-full <?= $si === 0 ? 'w-6 h-3 bg-kid-purple' : 'w-2.5 h-2.5 bg-slate-300 hover:bg-slate-400' ?>"
                      aria-label="Slide <?= $si+1 ?>"></button>
              <?php endforeach; ?>
            </div>
            <!-- Slide counter pill -->
            <div class="flex justify-center mt-2">
              <span id="heroCounter" class="text-[11px] font-bold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">
                1 / <?= count($sliders) ?>
              </span>
            </div>
            <?php else: ?>
            <div class="flex justify-center items-center gap-2 mt-4">
              <span class="w-6 h-3 rounded-full bg-kid-purple"></span>
            </div>
            <?php endif; ?>

          </div><!-- /slider wrap -->
        </div>

      </div>
    </div>
  </div>
</section>

<?php /* ── Hero Slider JS ── */ ?>
<?php if (count($sliders) > 1): ?>
<script>
(function(){
  const total   = <?= count($sliders) ?>;
  let   cur     = 0;
  let   timer   = null;
  const DELAY   = 5000; // 5 detik auto-slide

  const slides  = document.querySelectorAll('.hero-slide');
  const dots    = document.querySelectorAll('.hero-dot');
  const counter = document.getElementById('heroCounter');

  function show(idx) {
    slides[cur].style.opacity = '0';
    slides[cur].style.display = 'none';
    slides[cur].classList.remove('active');
    dots[cur].className = 'hero-dot transition-all duration-300 rounded-full w-2.5 h-2.5 bg-slate-300 hover:bg-slate-400';

    cur = (idx + total) % total;

    slides[cur].style.display = 'block';
    slides[cur].style.opacity = '1';
    slides[cur].classList.add('active');
    dots[cur].className = 'hero-dot transition-all duration-300 rounded-full w-6 h-3 bg-[#7952D9]';
    if (counter) counter.textContent = (cur + 1) + ' / ' + total;
  }

  slides.forEach(function(slide) {
    slide.style.transition = 'opacity .45s ease';
    slide.style.opacity = slide.classList.contains('active') ? '1' : '0';
  });

  function startTimer() {
    clearInterval(timer);
    timer = setInterval(function(){ show(cur + 1); }, DELAY);
  }

  window.heroNav    = function(dir){ show(cur + dir); startTimer(); };
  window.heroGoTo   = function(idx){ show(idx);       startTimer(); };

  // Pause on hover
  var wrap = document.getElementById('heroSliderWrap');
  if (wrap) {
    wrap.addEventListener('mouseenter', function(){ clearInterval(timer); });
    wrap.addEventListener('mouseleave', startTimer);
  }

  // Touch / swipe support
  var startX = null;
  var slider = document.getElementById('heroSlider');
  if (slider) {
    slider.addEventListener('touchstart', function(e){ startX = e.touches[0].clientX; }, {passive:true});
    slider.addEventListener('touchend',   function(e){
      if (startX === null) return;
      var diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 40) { show(cur + (diff > 0 ? 1 : -1)); startTimer(); }
      startX = null;
    }, {passive:true});
  }

  startTimer();
})();
</script>
<?php endif; ?>


<!-- ══════════════════════════════════════════════
     PROGRAM CARDS — 4 solid colors
══════════════════════════════════════════════ -->
<section id="tentang" class="py-14 bg-slate-50/70 border-y border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto mb-10">
      <span class="text-xs font-extrabold tracking-widest text-kid-purple uppercase px-3.5 py-1 rounded-full bg-purple-100">Pilihan Program Terbaik</span>
      <h2 class="font-heading font-black text-3xl sm:text-4xl text-slate-900 mt-2">Program Ceria untuk Tiap Tahap Usia</h2>
      <p class="text-slate-600 text-sm sm:text-base mt-2">Kurikulum seimbang yang mengasah kognitif, motorik halus, kecerdasan sosial, dan kemandirian anak.</p>
    </div>

    <!-- 4 Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

      <!-- Card 1: Purple -->
      <article class="program-card bg-kid-purple text-white rounded-3xl p-5 flex flex-col justify-between shadow-lg shadow-purple-200">
        <div>
          <div class="text-center mb-4">
            <h3 class="font-heading font-black text-xl leading-tight">Toodler</h3>
            <p class="text-purple-200 text-xs font-medium">Usia (2 - 3 Thn)</p>
          </div>
          <div class="rounded-2xl overflow-hidden border-2 border-white/40 shadow-inner aspect-[4/3] mb-4 bg-purple-900/20">
            <picture><source srcset="assets/img/lingkungan.webp" type="image/webp"><img src="assets/img/lingkungan.png" alt="Kelompok anak" class="w-full h-full object-cover" loading="lazy" width="400" height="300"></picture>
          </div>
          <p class="text-purple-100 text-xs sm:text-sm leading-relaxed mb-4">
           Fokus pada stimulasi motorik, bahasa, sosial emosional, dan kemandirian melalui kegiatan bermain yang menyenangkan dan edukatif.
          </p>
        </div>
        <a href="#pendaftaran" class="w-full py-2.5 px-4 rounded-xl bg-white/20 hover:bg-white text-white hover:text-kid-purple font-heading font-bold text-xs text-center border border-white/40 transition-colors flex items-center justify-center gap-1.5">
          Pelajari Lebih Lanjut <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </article>

      <!-- Card 2: Orange -->
      <article class="program-card bg-kid-orange text-white rounded-3xl p-5 flex flex-col justify-between shadow-lg shadow-orange-200">
        <div>
          <div class="text-center mb-4">
            <h3 class="font-heading font-black text-xl leading-tight">PlayGrup</h3>
            <p class="text-orange-100 text-xs font-medium">Kelompok Bermain (3-4 Thn)</p>
          </div>
          <div class="rounded-2xl overflow-hidden border-2 border-white/40 shadow-inner aspect-[4/3] mb-4 bg-orange-900/20">
            <picture><source srcset="assets/img/playgrup.webp" type="image/webp"><img src="assets/img/playgrup.jpeg" alt="Anak perempuan riang" class="w-full h-full object-cover" loading="lazy" width="400" height="300"></picture>
          </div>
          <p class="text-orange-50 text-xs sm:text-sm leading-relaxed mb-4">
            Aktivitas seru mulai mengenal konsep dasar belajar, bersosialisasi, kreativitas,serta explorasi lingkungan sekitar. </p>
        </div>
        <a href="#pendaftaran" class="w-full py-2.5 px-4 rounded-xl bg-white/20 hover:bg-white text-white hover:text-kid-orange font-heading font-bold text-xs text-center border border-white/40 transition-colors flex items-center justify-center gap-1.5">
          Pelajari Lebih Lanjut <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </article>

      <!-- Card 3: Green -->
      <article class="program-card bg-kid-green text-white rounded-3xl p-5 flex flex-col justify-between shadow-lg shadow-green-200">
        <div>
          <div class="text-center mb-4">
            <h3 class="font-heading font-black text-xl leading-tight">Kindergarten A</h3>
            <p class="text-green-100 text-xs font-medium">Usia (4-5 Thn)</p>
          </div>
          <div class="rounded-2xl overflow-hidden border-2 border-white/40 shadow-inner aspect-[4/3] mb-4 bg-green-900/20">
            <picture><source srcset="assets/img/kind_A.webp" type="image/webp"><img src="assets/img/kind_A.png" alt="Anak-anak belajar membaca" class="w-full h-full object-cover" loading="lazy" width="400" height="300"></picture>
          </div>
          <p class="text-emerald-50 text-xs sm:text-sm leading-relaxed mb-4">
            Mengembangkan kemampuan bahasa, kognitif, motorik ,kreativitas dan kesiapan belajar melalui berbagai aktivitas edukatif.
          </p>
        </div>
        <a href="#pendaftaran" class="w-full py-2.5 px-4 rounded-xl bg-white/20 hover:bg-white text-white hover:text-kid-green font-heading font-bold text-xs text-center border border-white/40 transition-colors flex items-center justify-center gap-1.5">
          Pelajari Lebih Lanjut <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </article>

      <!-- Card 4: Orange -->
      <article class="program-card bg-kid-orange text-white rounded-3xl p-5 flex flex-col justify-between shadow-lg shadow-orange-200">
        <div>
          <div class="text-center mb-4">
            <h3 class="font-heading font-black text-xl leading-tight">Kindergarten B</h3>
            <p class="text-orange-100 text-xs font-medium">Usia (5 - 6 Thn)</p>
          </div>
          <div class="rounded-2xl overflow-hidden border-2 border-white/40 shadow-inner aspect-[4/3] mb-4 bg-orange-900/20">
            <picture><source srcset="assets/img/kind_B.webp" type="image/webp"><img src="assets/img/kind_B.png" alt="Anak perempuan ceria" class="w-full h-full object-cover" loading="lazy" width="400" height="300"></picture>
          </div>
          <p class="text-orange-50 text-xs sm:text-sm leading-relaxed mb-4">
            Mempersiapkan anak menuju jenjang sekolah dasar dan pembelajaran yang menyenangkan, meliputi membaca, menulis, berhitung, karakter, dan kemandirian.
        </div>
        <a href="#pendaftaran" class="w-full py-2.5 px-4 rounded-xl bg-white/20 hover:bg-white text-white hover:text-kid-orange font-heading font-bold text-xs text-center border border-white/40 transition-colors flex items-center justify-center gap-1.5">
          Pelajari Lebih Lanjut <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </article>

    </div>
  </div>
</section>


<!-- ══════════════════════════════════════════════
     ADMISSION BANNER
══════════════════════════════════════════════ -->
<section class="py-8 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="rounded-3xl border-2 border-dashed border-amber-300 bg-amber-50/70 p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm relative overflow-hidden">
      <!-- Cute clouds background -->
      <svg class="absolute top-2 right-10 w-20 h-14 pointer-events-none opacity-20" viewBox="0 0 100 70">
        <ellipse cx="25" cy="40" rx="18" ry="14" fill="#CBD5E1"/>
        <ellipse cx="50" cy="32" rx="25" ry="20" fill="#CBD5E1"/>
        <ellipse cx="75" cy="40" rx="18" ry="14" fill="#CBD5E1"/>
      </svg>
      
      <div class="flex items-center gap-5 text-center md:text-left">
        <!-- Paper plane character -->
        <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center flex-shrink-0 shadow-inner relative">
          <svg class="w-10 h-10" viewBox="0 0 80 80">
            <defs>
              <linearGradient id="planeBody" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#FDBA74;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#FB923C;stop-opacity:1" />
              </linearGradient>
            </defs>
            <g transform="rotate(-15 40 40)">
              <!-- plane body -->
              <path d="M10 40 L70 18 L45 40 L70 62 Z" fill="url(#planeBody)" stroke="#EA580C" stroke-width="2.5"/>
              <path d="M45 40 L38 58 L45 54 Z" fill="#F97316" stroke="#EA580C" stroke-width="2"/>
              <!-- window -->
              <circle cx="35" cy="32" r="5" fill="#FFF" opacity="0.4"/>
              <!-- cute face on plane -->
              <circle cx="28" cy="36" r="2" fill="#7C2D12"/>
              <circle cx="35" cy="38" r="2" fill="#7C2D12"/>
              <path d="M28 42 Q31.5 45 35 42" stroke="#7C2D12" stroke-width="1.5" fill="none"/>
            </g>
            <!-- motion lines -->
            <line x1="5" y1="35" x2="12" y2="35" stroke="#F97316" stroke-width="2" opacity="0.3"/>
            <line x1="3" y1="42" x2="10" y2="42" stroke="#F97316" stroke-width="2" opacity="0.3"/>
          </svg>
        </div>
        <div>
          <h3 class="font-heading font-bold text-lg sm:text-xl text-slate-800">
            Berikan Si Kecil Awal Terbaik dalam Hidupnya!
          </h3>
          <p class="text-slate-600 text-xs sm:text-sm mt-0.5">
            Pendaftaran Siswa Baru (PPDB) Tahun Ajaran <strong class="text-kid-orange">2024-2025</strong> Gelombang 1 Telah Resmi Dibuka.
          </p>
        </div>
      </div>
      <a href="https://wa.me/<?= $nomor_whatsapp_sekolah ?>?text=<?= $pesan_whatsapp ?>" target="_blank" class="px-7 py-3 rounded-full bg-kid-orange hover:bg-kid-orange-dark text-white font-heading font-bold text-sm sm:text-base shadow-md shadow-orange-300 flex items-center gap-2.5 transition-all hover:scale-105 active:scale-95 flex-shrink-0">
        <span>Hubungi Kami Hari Ini!</span>
        <i class="fa-solid fa-phone"></i>
      </a>
    </div>
  </div>
</section>


<!-- ══════════════════════════════════════════════
     PENGAJAR
══════════════════════════════════════════════ -->
<section id="pengajar" class="py-14 bg-gradient-to-br from-green-50/80 via-white to-orange-50/50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <span class="text-xs font-extrabold tracking-widest text-kid-green uppercase px-3.5 py-1 rounded-full bg-emerald-100">Pendidik Berhati Hangat</span>
      <h2 class="font-heading font-black text-3xl text-slate-900 mt-2">Guru & Fasilitator Kami</h2>
      <p class="text-slate-600 text-sm mt-1">Berpengalaman dalam psikologi perkembangan anak, sabar, dan penuh kasih sayang.</p>
    </div>

    <?php if (count($guru) === 0): ?>
    <div class="text-center py-12">
      <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-3xl mb-4">
        <i class="fa-solid fa-chalkboard-user"></i>
      </div>
      <p class="text-slate-500 font-medium">Profil pengajar akan segera tersedia.</p>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php
      $teacher_colors = [
        ['kid-purple', 'purple-100'],
        ['kid-orange', 'orange-100'],
        ['kid-green', 'green-100'],
        ['kid-blue', 'sky-100'],
        ['kid-pink', 'rose-100'],
        ['kid-amber', 'amber-100'],
      ];
      foreach ($guru as $idx => $g):
        [$border_color, $badge_bg] = $teacher_colors[$idx % count($teacher_colors)];
      ?>
      <article class="teacher-card p-6 rounded-3xl border border-slate-100 bg-slate-50/60 text-center hover:shadow-lg transition-all cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-green-600"
           role="button" tabindex="0" aria-haspopup="dialog" data-teacher-id="<?= (int) $g['id'] ?>"
           aria-label="Lihat profil lengkap <?= h($g['nama']) ?>">
        <?php if ($g['foto']): ?>
        <div class="w-24 h-24 mx-auto rounded-full overflow-hidden border-4 border-<?= $border_color ?> shadow-md mb-4">
          <?php $guruWebp = webp_src('assets/uploads/guru', $g['foto']); ?>
          <picture>
            <source srcset="<?= h($guruWebp) ?>" type="image/webp">
            <img src="assets/uploads/guru/<?= h($g['foto']) ?>" alt="<?= h($g['nama']) ?>" class="w-full h-full object-cover" loading="lazy" width="96" height="96">
          </picture>
        </div>
        <?php else: ?>
        <div class="w-24 h-24 mx-auto rounded-full bg-<?= $badge_bg ?> border-4 border-<?= $border_color ?> shadow-md mb-4 flex items-center justify-center text-slate-400 text-2xl">
          <i class="fa-solid fa-user"></i>
        </div>
        <?php endif; ?>
        <h3 class="font-heading font-bold text-lg text-slate-800"><?= h($g['nama']) ?></h3>
        <span class="inline-block text-xs font-semibold text-<?= $border_color ?> bg-<?= $badge_bg ?> px-3 py-0.5 rounded-full mt-1 mb-2"><?= h($g['jabatan']) ?></span>
        <?php if (!empty($g['bidang'])): ?>
        <p class="text-xs text-slate-600 font-semibold mb-2"><i class="fa-solid fa-book text-<?= $border_color ?>"></i> <?= h($g['bidang']) ?></p>
        <?php endif; ?>
        <?php if (!empty($g['bio'])): ?>
        <p class="text-xs text-slate-500 leading-relaxed"><?= h(mb_strimwidth($g['bio'], 0, 100, '...')) ?></p>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>

    <div id="teacherModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/70 p-4" role="dialog" aria-modal="true" aria-labelledby="teacherModalName" onclick="if(event.target === this) closeTeacherModal()">
      <article class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
        <button type="button" onclick="closeTeacherModal()" aria-label="Tutup profil pengajar" class="absolute right-3 top-3 z-10 flex h-10 w-10 items-center justify-center rounded-lg bg-white/95 text-slate-700 shadow hover:bg-white">
          <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="grid sm:grid-cols-[220px_1fr]">
          <div class="flex min-h-56 items-center justify-center bg-green-50 p-6">
            <img id="teacherModalPhoto" src="" alt="" class="hidden h-40 w-40 rounded-full border-4 border-white object-cover shadow-md">
            <div id="teacherModalPlaceholder" class="flex h-40 w-40 items-center justify-center rounded-full border-4 border-white bg-emerald-100 text-5xl text-emerald-700 shadow-md"><i class="fa-solid fa-user"></i></div>
          </div>
          <div class="p-6 sm:p-8">
            <h2 id="teacherModalName" class="font-heading text-2xl font-black text-slate-900"></h2>
            <p id="teacherModalRole" class="mt-1 text-sm font-bold text-green-700"></p>
            <dl class="mt-6 grid gap-4 text-sm">
              <div><dt class="font-bold text-slate-500">Mata Pelajaran / Bidang</dt><dd id="teacherModalField" class="mt-1 text-slate-800"></dd></div>
              <div><dt class="font-bold text-slate-500">Pendidikan Terakhir</dt><dd id="teacherModalEducation" class="mt-1 text-slate-800"></dd></div>
              <div><dt class="font-bold text-slate-500">Pengalaman Mengajar</dt><dd id="teacherModalExperience" class="mt-1 text-slate-800"></dd></div>
              <div><dt class="font-bold text-slate-500">Biodata</dt><dd id="teacherModalBio" class="mt-1 whitespace-pre-line leading-relaxed text-slate-700"></dd></div>
            </dl>
          </div>
        </div>
      </article>
    </div>
    <script>
    const teacherDetails = <?= json_encode(array_map(static function ($teacher) {
      return [
        'id' => (int) $teacher['id'],
        'nama' => $teacher['nama'],
        'jabatan' => $teacher['jabatan'] ?? '',
        'bidang' => $teacher['bidang'] ?? '',
        'pendidikan' => $teacher['pendidikan'] ?? '',
        'pengalaman' => $teacher['pengalaman'] ?? '',
        'bio' => $teacher['bio'] ?? '',
        'foto' => $teacher['foto'] ?? '',
      ];
    }, $guru), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const teacherById = Object.fromEntries(teacherDetails.map(teacher => [teacher.id, teacher]));
    const teacherModal = document.getElementById('teacherModal');
    const teacherModalPhoto = document.getElementById('teacherModalPhoto');
    const teacherModalPlaceholder = document.getElementById('teacherModalPlaceholder');

    function openTeacherModal(teacherId) {
      const teacher = teacherById[teacherId];
      if (!teacher) return;
      document.getElementById('teacherModalName').textContent = teacher.nama;
      document.getElementById('teacherModalRole').textContent = teacher.jabatan || 'Pengajar';
      document.getElementById('teacherModalField').textContent = teacher.bidang || 'Belum diisi';
      document.getElementById('teacherModalEducation').textContent = teacher.pendidikan || 'Belum diisi';
      document.getElementById('teacherModalExperience').textContent = teacher.pengalaman || 'Belum diisi';
      document.getElementById('teacherModalBio').textContent = teacher.bio || 'Profil pengajar di Education House Preschool.';
      if (teacher.foto) {
        teacherModalPhoto.src = 'assets/uploads/guru/' + encodeURIComponent(teacher.foto);
        teacherModalPhoto.alt = 'Foto ' + teacher.nama;
        teacherModalPhoto.classList.remove('hidden');
        teacherModalPlaceholder.classList.add('hidden');
      } else {
        teacherModalPhoto.classList.add('hidden');
        teacherModalPlaceholder.classList.remove('hidden');
      }
      teacherModal.classList.remove('hidden');
      teacherModal.classList.add('flex');
      document.body.style.overflow = 'hidden';
      teacherModal.querySelector('button').focus();
    }

    function closeTeacherModal() {
      teacherModal.classList.add('hidden');
      teacherModal.classList.remove('flex');
      document.body.style.overflow = '';
    }

    document.querySelectorAll('.teacher-card').forEach(card => {
      card.addEventListener('click', () => openTeacherModal(card.dataset.teacherId));
      card.addEventListener('keydown', event => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          openTeacherModal(card.dataset.teacherId);
        }
      });
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && !teacherModal.classList.contains('hidden')) closeTeacherModal();
    });
    </script>
    <?php endif; ?>
  </div>
</section>


<!-- ══════════════════════════════════════════════
     GALERI — 6 photos with white borders
══════════════════════════════════════════════ -->
<section id="kegiatan" class="py-14 bg-gradient-to-br from-green-50/80 via-lime-50/60 to-orange-50/60 border-t border-green-100 relative overflow-hidden">
  <!-- Decorative butterflies -->
  <svg class="absolute top-10 left-10 w-16 h-16 pointer-events-none opacity-40" viewBox="0 0 80 80">
    <defs>
      <linearGradient id="butterfly1" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" style="stop-color:#C084FC;stop-opacity:1" />
        <stop offset="100%" style="stop-color:#A855F7;stop-opacity:1" />
      </linearGradient>
    </defs>
    <!-- left wing -->
    <ellipse cx="28" cy="30" rx="18" ry="22" fill="url(#butterfly1)" stroke="#7C3AED" stroke-width="2" transform="rotate(-20 28 30)"/>
    <ellipse cx="25" cy="35" rx="12" ry="16" fill="#E9D5FF" opacity="0.6"/>
    <!-- right wing -->
    <ellipse cx="52" cy="30" rx="18" ry="22" fill="url(#butterfly1)" stroke="#7C3AED" stroke-width="2" transform="rotate(20 52 30)"/>
    <ellipse cx="55" cy="35" rx="12" ry="16" fill="#E9D5FF" opacity="0.6"/>
    <!-- body -->
    <ellipse cx="40" cy="35" rx="5" ry="15" fill="#4C1D95"/>
    <circle cx="40" cy="25" r="4" fill="#581C87"/>
    <!-- antennae -->
    <path d="M38 22 Q35 18 33 15" stroke="#4C1D95" stroke-width="2" fill="none" stroke-linecap="round"/>
    <path d="M42 22 Q45 18 47 15" stroke="#4C1D95" stroke-width="2" fill="none" stroke-linecap="round"/>
    <circle cx="33" cy="15" r="2" fill="#F97316"/>
    <circle cx="47" cy="15" r="2" fill="#F97316"/>
  </svg>

  <svg class="absolute top-16 right-12 w-14 h-14 pointer-events-none opacity-40" viewBox="0 0 80 80">
    <defs>
      <linearGradient id="butterfly2" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" style="stop-color:#FDE68A;stop-opacity:1" />
        <stop offset="100%" style="stop-color:#FCD34D;stop-opacity:1" />
      </linearGradient>
    </defs>
    <g transform="rotate(15 40 40)">
      <ellipse cx="28" cy="30" rx="16" ry="20" fill="url(#butterfly2)" stroke="#D97706" stroke-width="2" transform="rotate(-25 28 30)"/>
      <ellipse cx="52" cy="30" rx="16" ry="20" fill="url(#butterfly2)" stroke="#D97706" stroke-width="2" transform="rotate(25 52 30)"/>
      <ellipse cx="40" cy="35" rx="4" ry="12" fill="#92400E"/>
      <circle cx="40" cy="26" r="3.5" fill="#92400E"/>
    </g>
  </svg>

  <!-- Rainbow decoration bottom -->
  <svg class="absolute bottom-10 left-1/4 w-32 h-20 pointer-events-none opacity-30" viewBox="0 0 160 100">
    <path d="M10 80 Q80 20 150 80" stroke="#EF4444" stroke-width="6" fill="none" stroke-linecap="round"/>
    <path d="M10 86 Q80 26 150 86" stroke="#F97316" stroke-width="6" fill="none" stroke-linecap="round"/>
    <path d="M10 92 Q80 32 150 92" stroke="#FCD34D" stroke-width="6" fill="none" stroke-linecap="round"/>
    <path d="M10 98 Q80 38 150 98" stroke="#4ADE80" stroke-width="6" fill="none" stroke-linecap="round"/>
  </svg>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-8 flex items-center justify-center gap-3">
      <span class="text-kid-purple text-lg hidden sm:inline"><i class="fa-solid fa-butterfly"></i></span>
      <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-800 flex items-center gap-2">
        <span class="text-kid-pink text-base">♥</span> Momen Ceria Buah Hati <span class="text-kid-pink text-base">♥</span>
      </h2>
      <span class="text-kid-teal text-lg hidden sm:inline"><i class="fa-solid fa-butterfly"></i></span>
    </div>

    <?php if (count($galeri) === 0 && count($instagramPosts) === 0): ?>
    <div class="text-center py-12">
      <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-3xl mb-4">
        <i class="fa-solid fa-images"></i>
      </div>
      <p class="text-slate-500 font-medium">Belum ada momen kegiatan.</p>
    </div>
    <?php else: ?>
    <style>
      .instagram-moment { width:100%; max-width:260px; justify-self:center; }
      .instagram-moment iframe { transform:scale(.24); }
      @media (min-width:381px) and (max-width:639px) { .instagram-moment iframe { transform:scale(.30); } }
      @media (min-width:640px) and (max-width:1023px) { .instagram-moment iframe { transform:scale(.35); } }
      @media (min-width:1024px) and (max-width:1279px) { .instagram-moment iframe { transform:scale(.42); } }
      @media (min-width:1280px) { .instagram-moment iframe { transform:scale(.41); } }
    </style>
    <?php $momentIndex = 0; $momentCount = count($galeri) + count($instagramPosts); ?>
    <div class="grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5" id="galeriGrid">
      <?php foreach ($galeri as $idx => $g): ?>
      <div class="group relative rounded-2xl overflow-hidden border-4 border-white shadow-md aspect-square bg-white transition-all hover:scale-105 hover:shadow-xl cursor-pointer <?= $momentIndex >= 5 ? 'moment-extra hidden' : '' ?>"
           onclick="openLightbox(<?= $idx ?>)" title="<?= h($g['keterangan']) ?>">
        <img src="assets/uploads/galeri/<?= h($g['gambar']) ?>" alt="<?= h($g['keterangan']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" loading="lazy" width="300" height="300">
        <!-- Overlay on hover -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
          <?php if ($g['keterangan']): ?>
          <p class="text-white text-xs font-bold"><?= h($g['keterangan']) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php $momentIndex++; ?>
      <?php endforeach; ?>
      <?php foreach ($instagramPosts as $post): ?>
      <div class="instagram-moment group relative aspect-square cursor-pointer overflow-hidden rounded-2xl border-4 border-white bg-white shadow-md transition-all hover:scale-105 hover:shadow-xl <?= $momentIndex >= 5 ? 'moment-extra hidden' : '' ?>">
        <iframe src="<?= h($post['embed_url']) ?>" title="Momen Instagram Education House"
                class="absolute left-0 top-0 block border-0"
                style="width:540px;height:760px;transform-origin:top left;"
                loading="lazy" allow="encrypted-media; picture-in-picture" allowfullscreen>
        </iframe>
        <button type="button" onclick="openInstagramZoom('<?= h($post['embed_url']) ?>')"
                aria-label="Perbesar video Instagram" title="Perbesar video"
                class="absolute right-2 top-2 z-20 flex h-9 w-9 items-center justify-center rounded-lg bg-white/95 text-slate-700 shadow-md transition hover:bg-white">
          <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
        </button>
      </div>
      <?php $momentIndex++; ?>
      <?php endforeach; ?>
    </div>
    <?php if ($momentCount > 5): ?>
    <div class="mt-7 flex justify-center">
      <button type="button" id="momentMoreButton" onclick="showMoreMoments()"
              class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-kid-purple shadow-md transition hover:bg-purple-50">
        Lihat lebih banyak <i class="fa-solid fa-chevron-down text-xs"></i>
      </button>
    </div>
    <script>
    function showMoreMoments() {
      document.querySelectorAll('.moment-extra').forEach(function(card) {
        card.classList.remove('hidden');
      });
      document.getElementById('momentMoreButton').remove();
    }
    </script>
    <?php endif; ?>

    <!-- Lightbox -->
    <div id="lbOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:9999;align-items:center;justify-content:center;" onclick="if(event.target === this) closeLb()">
      <div class="relative max-w-4xl w-11/12 max-h-[90vh]">
        <button type="button" onclick="closeLb()" aria-label="Tutup foto" class="absolute top-3 right-3 z-30 bg-black/45 hover:bg-black/65 text-white w-10 h-10 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
        </button>
        <img id="lbImg" src="" alt="" class="w-full max-h-[70vh] object-contain rounded-2xl">
        <div class="text-center mt-4">
          <p id="lbCap" class="text-white font-bold"></p>
          <p id="lbCnt" class="text-slate-400 text-sm"></p>
        </div>
        <button onclick="lbNav(-1)" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-14 bg-white/10 hover:bg-white/20 text-white w-12 h-12 rounded-xl">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button onclick="lbNav(1)" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-14 bg-white/10 hover:bg-white/20 text-white w-12 h-12 rounded-xl">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </div>
    <script>
const galData=<?= json_encode(array_map(fn($g)=>['src'=>'assets/uploads/galeri/'.$g['gambar'],'cap'=>$g['keterangan']??''],$galeri)) ?>;
let lbIdx=0;
function openLightbox(i){lbIdx=i;updateLb();document.getElementById('lbOverlay').style.display='flex';document.body.style.overflow='hidden';}
function closeLb(){document.getElementById('lbOverlay').style.display='none';document.body.style.overflow='';}
function lbNav(d){lbIdx=(lbIdx+d+galData.length)%galData.length;updateLb();}
function updateLb(){const d=galData[lbIdx];document.getElementById('lbImg').src=d.src;document.getElementById('lbCap').textContent=d.cap;document.getElementById('lbCnt').textContent=(lbIdx+1)+' / '+galData.length;}
document.addEventListener('keydown',e=>{if(document.getElementById('lbOverlay').style.display==='none')return;if(e.key==='Escape')closeLb();if(e.key==='ArrowLeft')lbNav(-1);if(e.key==='ArrowRight')lbNav(1);});
    </script>
    <?php endif; ?>
  </div>
</section>


<style>
  .social-card-instagram { position:absolute;left:0;top:0;display:block;border:0;transform:scale(.24); }
  @media (min-width:381px) { .social-card-instagram { transform:scale(.30); } }
  @media (min-width:640px) { .social-card-instagram { transform:scale(.35); } }
  @media (min-width:1024px) { .social-card-instagram { transform:scale(.42); } }
  @media (min-width:1280px) { .social-card-instagram { transform:scale(.41); } }
</style>

<section id="kegiatan-islami" class="py-14 bg-gradient-to-br from-white via-green-50/70 to-orange-50/70">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-8">
      <span class="text-xs font-extrabold tracking-widest text-kid-green uppercase px-3.5 py-1 rounded-full bg-emerald-100">Pembiasaan Baik Sejak Dini</span>
      <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 mt-3">Kegiatan Islami Anak</h2>
      <p class="text-slate-600 text-sm mt-2">Belajar nilai-nilai kebaikan melalui kegiatan yang menyenangkan dan sesuai usia.</p>
    </div>
    <?php if (!$kegiatanIslami): ?>
    <p class="text-center py-8 text-slate-500">Kegiatan Islami akan segera ditampilkan.</p>
    <?php else: ?>
    <div id="kegiatanIslamiGrid" class="grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
      <?php foreach ($kegiatanIslami as $activityIndex => $kegiatan): ?>
      <article class="group relative aspect-square overflow-hidden rounded-2xl border border-green-100 bg-green-50 shadow-sm transition-shadow hover:shadow-lg <?= $activityIndex >= 5 ? 'more-content-card hidden' : '' ?>">
        <?php if (!empty($kegiatan['instagram_url'])): ?>
        <?php $instagramPath = instagram_post_path($kegiatan['instagram_url']); ?>
        <?php if ($instagramPath): ?><iframe src="https://www.instagram.com<?= h($instagramPath) ?>/embed/" title="<?= h($kegiatan['judul']) ?>" class="social-card-instagram" style="width:540px;height:760px;transform-origin:top left;" loading="lazy" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe><?php endif; ?>
        <?php elseif (!empty($kegiatan['gambar'])): ?>
        <img src="assets/uploads/islami/<?= h($kegiatan['gambar']) ?>" alt="<?= h($kegiatan['judul']) ?>" class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
        <?php endif; ?>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 via-slate-900/55 to-transparent p-3 pt-10 text-white">
          <h3 class="font-heading text-xs font-bold sm:text-sm"><?= h($kegiatan['judul']) ?></h3>
          <?php if (!empty($kegiatan['keterangan'])): ?><p class="mt-1 text-[11px] leading-snug text-white/90 line-clamp-2"><?= h($kegiatan['keterangan']) ?></p><?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php if (count($kegiatanIslami) > 5): ?>
    <div class="mt-6 flex justify-center">
      <button type="button" onclick="showMoreContent('kegiatanIslamiGrid', this)" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-green-700 shadow-md transition hover:bg-green-50">
        Lihat lebih banyak <i class="fa-solid fa-chevron-down text-xs"></i>
      </button>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<section id="testimoni" class="py-14 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-8">
      <span class="text-xs font-extrabold tracking-widest text-kid-orange uppercase px-3.5 py-1 rounded-full bg-orange-100">Cerita Keluarga Education House</span>
      <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 mt-3">Testimoni Orang Tua</h2>
      <p class="text-slate-600 text-sm mt-2">Cerita orang tua tentang pengalaman dan perkembangan buah hati.</p>
    </div>
    <?php if (!$testimoniOrangtua): ?>
    <p class="text-center py-8 text-slate-500">Konten testimoni akan segera ditampilkan.</p>
    <?php else: ?>
    <div id="testimoniGrid" class="grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
      <?php foreach ($testimoniOrangtua as $testimonialIndex => $testimoni): ?>
      <article class="group relative aspect-square overflow-hidden rounded-2xl border border-orange-100 bg-slate-900 shadow-sm transition-shadow hover:shadow-lg <?= $testimonialIndex >= 5 ? 'more-content-card hidden' : '' ?>">
        <?php if (!empty($testimoni['instagram_url'])): ?>
        <?php $instagramPath = instagram_post_path($testimoni['instagram_url']); ?>
        <?php if ($instagramPath): ?><iframe src="https://www.instagram.com<?= h($instagramPath) ?>/embed/" title="Video testimoni orang tua" class="social-card-instagram" style="width:540px;height:760px;transform-origin:top left;" loading="lazy" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe><?php endif; ?>
        <?php elseif (!empty($testimoni['tiktok_url'])): ?>
        <?php $tiktokVideoId = tiktok_video_id($testimoni['tiktok_url']); ?>
        <?php $tiktokThumbnail = tiktok_thumbnail_file($testimoni['tiktok_url']); ?>
        <?php if ($tiktokVideoId): ?>
        <?php if ($tiktokThumbnail): ?>
        <button type="button" onclick="this.nextElementSibling.classList.remove('hidden'); this.remove();" class="absolute inset-0 z-10 flex h-full w-full items-center justify-center bg-slate-950" aria-label="Putar video testimoni orang tua">
          <img src="assets/uploads/testimoni/<?= h($tiktokThumbnail) ?>" alt="Cuplikan video testimoni orang tua" class="absolute inset-0 h-full w-full object-cover">
          <span class="relative flex h-14 w-14 items-center justify-center rounded-full bg-white/95 text-slate-900 shadow-lg"><i class="fa-solid fa-play ml-1" aria-hidden="true"></i></span>
        </button>
        <?php endif; ?>
        <iframe src="https://www.tiktok.com/player/v1/<?= h($tiktokVideoId) ?>?controls=1&amp;description=0&amp;music_info=0" title="Video testimoni orang tua di TikTok" class="<?= $tiktokThumbnail ? 'hidden ' : '' ?>absolute inset-0 h-full w-full border-0" loading="lazy" allow="fullscreen; encrypted-media; picture-in-picture" allowfullscreen></iframe>
        <?php else: ?>
        <a href="<?= h($testimoni['tiktok_url']) ?>" target="_blank" rel="noopener noreferrer" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-slate-950 text-white transition-colors hover:bg-slate-800" aria-label="Tonton testimoni orang tua di TikTok">
          <i class="fa-brands fa-tiktok text-5xl" aria-hidden="true"></i>
          <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-bold text-slate-900">Tonton di TikTok <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i></span>
        </a>
        <?php endif; ?>
        <?php elseif (!empty($testimoni['video'])): ?>
        <video class="absolute inset-0 h-full w-full object-cover" controls preload="metadata" playsinline>
          <source src="assets/uploads/testimoni/<?= h($testimoni['video']) ?>" type="<?= strtolower(pathinfo($testimoni['video'], PATHINFO_EXTENSION)) === 'webm' ? 'video/webm' : 'video/mp4' ?>">
          Browser Anda tidak mendukung pemutar video.
        </video>
        <?php endif; ?>
        <?php if (!empty($testimoni['keterangan'])): ?><p class="pointer-events-none absolute inset-x-0 top-0 bg-gradient-to-b from-slate-950/90 via-slate-900/65 to-transparent p-3 pb-7 text-xs leading-relaxed text-white line-clamp-3"><?= h($testimoni['keterangan']) ?></p><?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php if (count($testimoniOrangtua) > 5): ?>
    <div class="mt-6 flex justify-center">
      <button type="button" onclick="showMoreContent('testimoniGrid', this)" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-orange-700 shadow-md transition hover:bg-orange-50">
        Lihat lebih banyak <i class="fa-solid fa-chevron-down text-xs"></i>
      </button>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<script>
function showMoreContent(gridId, button) {
  document.querySelectorAll('#' + gridId + ' .more-content-card').forEach(card => card.classList.remove('hidden'));
  button.remove();
}
</script>


<!-- ══════════════════════════════════════════════
     BERITA & TENTANG
══════════════════════════════════════════════ -->
<section id="berita" class="py-12 bg-gradient-to-br from-white via-green-50/50 to-orange-50/50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

      <!-- Left: About -->
      <div class="lg:col-span-6 space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 flex items-center gap-2">
            Selamat Datang di toddler & preschool Kami!
          </h2>
          <!-- Cute animated sun -->
          <svg class="w-16 h-16 sun-shine" viewBox="0 0 100 100">
            <defs>
              <radialGradient id="sunGrad" cx="50%" cy="50%">
                <stop offset="0%" style="stop-color:#FEF3C7;stop-opacity:1" />
                <stop offset="50%" style="stop-color:#FCD34D;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#F59E0B;stop-opacity:1" />
              </radialGradient>
            </defs>
            <!-- rays -->
            <g transform="translate(50 50)">
              <line x1="0" y1="-45" x2="0" y2="-35" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
              <line x1="31" y1="-31" x2="25" y2="-25" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
              <line x1="45" y1="0" x2="35" y2="0" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
              <line x1="31" y1="31" x2="25" y2="25" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
              <line x1="0" y1="45" x2="0" y2="35" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
              <line x1="-31" y1="31" x2="-25" y2="25" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
              <line x1="-45" y1="0" x2="-35" y2="0" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
              <line x1="-31" y1="-31" x2="-25" y2="-25" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
            </g>
            <!-- sun body -->
            <circle cx="50" cy="50" r="22" fill="url(#sunGrad)" stroke="#D97706" stroke-width="2"/>
            <!-- cute face -->
            <circle cx="42" cy="46" r="3" fill="#92400E"/>
            <circle cx="58" cy="46" r="3" fill="#92400E"/>
            <path d="M42 56 Q50 62 58 56" stroke="#92400E" stroke-width="3" fill="none" stroke-linecap="round"/>
            <!-- cheeks -->
            <ellipse cx="35" cy="50" rx="4" ry="3" fill="#FB923C" opacity="0.5"/>
            <ellipse cx="65" cy="50" rx="4" ry="3" fill="#FB923C" opacity="0.5"/>
            <!-- shine -->
            <circle cx="44" cy="40" r="4" fill="#fff" opacity="0.7"/>
          </svg>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center">
          <div class="sm:col-span-5 rounded-2xl overflow-hidden border-2 border-slate-100 shadow-md aspect-[4/3]">
            <img src="assets/img/logoHD.webp" alt="Logo Education House Preschool" class="w-full h-full object-cover" loading="lazy" width="400" height="300">
          </div>
          <div class="sm:col-span-7">
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
              Di <strong class="text-kid-purple">Education House Preschool</strong>, kami menyediakan pendidikan komprehensif yang akan membantu anak anda mencapai tujuan. Welcome to Education House Preschool!
            </p>
          </div>
        </div>
        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
         Menjadi sekolah toodler dan preschool unggulan yang menciptakan generasi anak usia dini yang kreatif berkarakter dan memiliki pondasi kuat untuk belajar sepanjang hayat. </p>
        <div>
          <button type="button" onclick="openAboutModal()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-kid-green hover:bg-kid-green-dark text-white font-heading font-bold text-xs sm:text-sm shadow-sm transition-all hover:scale-105">
            Pelajari Lebih Tentang Kami <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </button>
        </div>
        <!-- Decorative illustrations -->
        <div class="flex items-center justify-center gap-6 pt-6">
          <!-- Seedling pot -->
          <svg class="w-12 h-14" viewBox="0 0 60 70">
            <defs>
              <linearGradient id="potGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" style="stop-color:#F97316;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#EA580C;stop-opacity:1" />
              </linearGradient>
            </defs>
            <!-- pot -->
            <path d="M15 45 L20 65 L40 65 L45 45 Z" fill="url(#potGrad)" stroke="#C2410C" stroke-width="1.5"/>
            <ellipse cx="30" cy="45" rx="15" ry="5" fill="#FB923C"/>
            <!-- stem -->
            <path d="M30 45 Q28 35 30 25" stroke="#11df5c" stroke-width="3" fill="none" stroke-linecap="round"/>
            <!-- leaves -->
            <ellipse cx="24" cy="32" rx="8" ry="5" fill="#22C55E" stroke="#0bf05f" stroke-width="1" transform="rotate(-30 24 32)"/>
            <ellipse cx="36" cy="28" rx="8" ry="5" fill="#4ADE80" stroke="#0ff162" stroke-width="1" transform="rotate(30 36 28)"/>
            <!-- flower -->
            <circle cx="30" cy="22" r="4" fill="#FCD34D" stroke="#F59E0B" stroke-width="1"/>
            <circle cx="26" cy="20" r="3" fill="#FBBF24"/>
            <circle cx="34" cy="20" r="3" fill="#FBBF24"/>
            <circle cx="30" cy="18" r="3" fill="#FBBF24"/>
            <circle cx="30" cy="22" r="2" fill="#FB923C"/>
          </svg>

          <!-- Heart character -->
          <svg class="w-14 h-14" viewBox="0 0 70 70">
            <defs>
              <linearGradient id="heartChar" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#FCA5A5;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#F87171;stop-opacity:1" />
              </linearGradient>
            </defs>
            <!-- heart body -->
            <path d="M35 60 C35 60, 10 40, 10 25 C10 15, 18 12, 25 16 C30 18, 35 23, 35 23 C35 23, 40 18, 45 16 C52 12, 60 15, 60 25 C60 40, 35 60, 35 60 Z" fill="url(#heartChar)" stroke="#DC2626" stroke-width="2"/>
            <circle cx="25" cy="22" r="4" fill="#fff" opacity="0.6"/>
            <!-- cute face -->
            <circle cx="25" cy="28" r="2" fill="#991B1B"/>
            <circle cx="45" cy="28" r="2" fill="#991B1B"/>
            <path d="M25 35 Q35 42 45 35" stroke="#991B1B" stroke-width="2" fill="none" stroke-linecap="round"/>
            <!-- blush -->
            <ellipse cx="18" cy="32" rx="4" ry="3" fill="#FCA5A5" opacity="0.6"/>
            <ellipse cx="52" cy="32" rx="4" ry="3" fill="#FCA5A5" opacity="0.6"/>
          </svg>

          <!-- Star character -->
          <svg class="w-14 h-14" viewBox="0 0 70 70">
            <defs>
              <linearGradient id="starChar" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#FDE68A;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#FCD34D;stop-opacity:1" />
              </linearGradient>
            </defs>
            <!-- star body -->
            <path d="M35 8 L42 28 L63 28 L46 41 L53 61 L35 48 L17 61 L24 41 L7 28 L28 28 Z" fill="url(#starChar)" stroke="#D97706" stroke-width="2"/>
            <circle cx="28" cy="18" r="3" fill="#fff" opacity="0.7"/>
            <!-- cute face -->
            <circle cx="28" cy="32" r="2" fill="#92400E"/>
            <circle cx="42" cy="32" r="2" fill="#92400E"/>
            <path d="M28 38 Q35 43 42 38" stroke="#92400E" stroke-width="2" fill="none" stroke-linecap="round"/>
            <!-- sparkles -->
            <circle cx="10" cy="15" r="2" fill="#FCD34D"/>
            <circle cx="60" cy="20" r="1.5" fill="#FCD34D"/>
            <circle cx="55" cy="50" r="2" fill="#FCD34D"/>
          </svg>
        </div>
      </div>

      <!-- Right: News -->
      <div class="lg:col-span-6 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-2 border-b border-slate-100">
          <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900">
            Siswa Berprestasi
          </h2>
          <?php if (count($berita) > 3): ?>
          <button type="button" id="beritaMoreButton" onclick="showMoreBerita()" class="inline-flex items-center gap-2 text-sm font-bold text-green-700 transition hover:text-orange-600">
            Lihat lebih banyak <i class="fa-solid fa-arrow-right text-xs"></i>
          </button>
          <?php endif; ?>
        </div>

        <?php if (count($berita) === 0): ?>
        <div class="text-center py-8">
          <i class="fa-regular fa-newspaper text-4xl text-slate-300 mb-3"></i>
          <p class="text-slate-500">Belum ada siswa berprestasi yang ditampilkan.</p>
        </div>
        <?php else: ?>
        <?php foreach ($berita as $index => $b): ?>
        <?php $bGambar = $beritaGambarMap[$b['id']] ?? []; ?>
        <div class="p-3.5 rounded-2xl bg-slate-50 hover:bg-purple-50/50 border border-slate-200/70 transition-all <?= $index >= 3 ? 'berita-extra hidden' : '' ?>">

          <?php if (count($bGambar) > 1): ?>
          <!-- Multi gambar: scroll horizontal -->
          <div class="flex gap-2 mb-3 overflow-x-auto pb-1" style="scrollbar-width:thin;">
            <?php foreach ($bGambar as $gi => $gUrl): ?>
            <?php $gWebp = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $gUrl); ?>
            <div class="flex-shrink-0 rounded-xl overflow-hidden border-2 <?= $gi===0 ? 'border-green-400' : 'border-slate-200' ?>" style="width:100px;height:75px;">
              <picture>
                <source srcset="<?= h($gWebp) ?>" type="image/webp">
                <img src="<?= h($gUrl) ?>" alt="Foto <?= $gi+1 ?>" class="w-full h-full object-cover" loading="<?= $gi===0?'eager':'lazy' ?>" width="100" height="75">
              </picture>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="space-y-1 text-left">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="text-[11px] font-bold text-slate-400"><?= tgl($b['created_at']) ?></span>
              <span style="font-size:10px;background:#DCFCE7;color:#166534;font-weight:800;padding:1px 7px;border-radius:5px;"><?= count($bGambar) ?> foto</span>
            </div>
            <h4 class="font-heading font-bold text-base text-slate-800 hover:text-kid-purple transition-colors"><?= h($b['judul']) ?></h4>
            <p class="text-xs text-slate-500 line-clamp-2"><?= h(mb_strimwidth($b['isi'], 0, 100, '...')) ?></p>
            <a href="berita_detail.php?id=<?= (int)$b['id'] ?>" class="inline-flex items-center text-xs font-bold text-kid-purple hover:underline pt-1">
              Baca Selengkapnya <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
            </a>
          </div>

          <?php elseif (count($bGambar) === 1): ?>
          <!-- Satu gambar: layout lama -->
          <div class="flex flex-col sm:flex-row gap-4 items-center">
            <div class="w-full sm:w-36 h-24 rounded-xl overflow-hidden flex-shrink-0 border-2 border-slate-200">
              <?php $gUrl = $bGambar[0]; $gWebp = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $gUrl); ?>
              <picture>
                <source srcset="<?= h($gWebp) ?>" type="image/webp">
                <img src="<?= h($gUrl) ?>" alt="<?= h($b['judul']) ?>" class="w-full h-full object-cover" loading="lazy" width="144" height="96">
              </picture>
            </div>
            <div class="space-y-1 w-full text-left">
              <span class="text-[11px] font-bold text-slate-400"><?= tgl($b['created_at']) ?></span>
              <h4 class="font-heading font-bold text-base text-slate-800 hover:text-kid-purple transition-colors"><?= h($b['judul']) ?></h4>
              <p class="text-xs text-slate-500 line-clamp-2"><?= h(mb_strimwidth($b['isi'], 0, 100, '...')) ?></p>
              <a href="berita_detail.php?id=<?= (int)$b['id'] ?>" class="inline-flex items-center text-xs font-bold text-kid-purple hover:underline pt-1">
                Baca Selengkapnya <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
              </a>
            </div>
          </div>

          <?php else: ?>
          <!-- Tidak ada gambar -->
          <div class="flex flex-col sm:flex-row gap-4 items-center">
            <div class="w-full sm:w-36 h-24 rounded-xl bg-slate-200 flex items-center justify-center text-slate-400 flex-shrink-0">
              <i class="fa-regular fa-image text-2xl"></i>
            </div>
            <div class="space-y-1 w-full text-left">
              <span class="text-[11px] font-bold text-slate-400"><?= tgl($b['created_at']) ?></span>
              <h4 class="font-heading font-bold text-base text-slate-800 hover:text-kid-purple transition-colors"><?= h($b['judul']) ?></h4>
              <p class="text-xs text-slate-500 line-clamp-2"><?= h(mb_strimwidth($b['isi'], 0, 100, '...')) ?></p>
              <a href="berita_detail.php?id=<?= (int)$b['id'] ?>" class="inline-flex items-center text-xs font-bold text-kid-purple hover:underline pt-1">
                Baca Selengkapnya <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
              </a>
            </div>
          </div>
          <?php endif; ?>

        </div>
        <?php endforeach; ?>
        <?php if (count($berita) > 3): ?>
        <script>
        function showMoreBerita() {
          document.querySelectorAll('.berita-extra').forEach(function(item) {
            item.classList.remove('hidden');
          });
          document.getElementById('beritaMoreButton').remove();
        }
        </script>
        <?php endif; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php if ($instagramPosts): ?>
<div>
  <div id="instagramZoom" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/90 p-3 sm:p-6"
       role="dialog" aria-modal="true" aria-label="Postingan Instagram diperbesar"
       onclick="if(event.target === this) closeInstagramZoom()">
    <div class="relative max-h-[90vh] w-full max-w-[540px] overflow-y-auto rounded-2xl bg-white shadow-2xl">
      <button type="button" onclick="closeInstagramZoom()" aria-label="Tutup postingan Instagram"
              class="sticky right-3 top-3 z-20 ml-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-900/80 text-white shadow-md">
        <i class="fa-solid fa-xmark"></i>
      </button>
            <iframe id="instagramZoomFrame" src="" title="Postingan Instagram Education House diperbesar"
              class="block w-full border-0" style="height:760px;margin-top:-40px;"
              allow="encrypted-media; picture-in-picture" allowfullscreen>
      </iframe>
    </div>
  </div>

  <script>
  function openInstagramZoom(embedUrl) {
    document.getElementById('instagramZoomFrame').src = embedUrl;
    document.getElementById('instagramZoom').classList.remove('hidden');
    document.getElementById('instagramZoom').classList.add('flex');
    document.body.style.overflow = 'hidden';
  }
  function closeInstagramZoom() {
    document.getElementById('instagramZoomFrame').src = '';
    document.getElementById('instagramZoom').classList.add('hidden');
    document.getElementById('instagramZoom').classList.remove('flex');
    document.body.style.overflow = '';
  }
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeInstagramZoom();
  });
  </script>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════
     FORM PENDAFTARAN
══════════════════════════════════════════════ -->
<section id="pendaftaran" class="py-14 bg-gradient-to-b from-green-50/80 via-lime-50/50 to-orange-50/70 relative overflow-hidden">
  <!-- Pencil character top-right -->
  <svg class="absolute top-12 right-16 w-20 h-28 pointer-events-none opacity-40" viewBox="0 0 80 120">
    <defs>
      <linearGradient id="pencilBody" x1="0%" y1="0%" x2="0%" y2="100%">
        <stop offset="0%" style="stop-color:#FBBF24;stop-opacity:1" />
        <stop offset="100%" style="stop-color:#F59E0B;stop-opacity:1" />
      </linearGradient>
    </defs>
    <!-- eraser -->
    <rect x="20" y="8" width="40" height="15" rx="3" fill="#EC4899" stroke="#BE185D" stroke-width="2"/>
    <rect x="22" y="20" width="36" height="3" fill="#9333EA"/>
    <!-- body -->
    <rect x="25" y="23" width="30" height="65" fill="url(#pencilBody)" stroke="#D97706" stroke-width="2"/>
    <!-- wood texture lines -->
    <line x1="30" y1="30" x2="50" y2="30" stroke="#D97706" stroke-width="1" opacity="0.3"/>
    <line x1="30" y1="40" x2="50" y2="40" stroke="#D97706" stroke-width="1" opacity="0.3"/>
    <line x1="30" y1="50" x2="50" y2="50" stroke="#D97706" stroke-width="1" opacity="0.3"/>
    <!-- tip -->
    <polygon points="40,88 25,88 30,105 40,110 50,105 55,88" fill="#EA580C" stroke="#9A3412" stroke-width="1.5"/>
    <polygon points="40,105 35,110 40,115 45,110" fill="#18181B"/>
    <!-- cute face -->
    <circle cx="33" cy="50" r="2.5" fill="#92400E"/>
    <circle cx="47" cy="50" r="2.5" fill="#92400E"/>
    <path d="M33 58 Q40 63 47 58" stroke="#92400E" stroke-width="2" fill="none" stroke-linecap="round"/>
    <ellipse cx="28" cy="52" rx="3" ry="2" fill="#FCD34D" opacity="0.5"/>
    <ellipse cx="52" cy="52" rx="3" ry="2" fill="#FCD34D" opacity="0.5"/>
  </svg>

  <!-- Book character bottom-left -->
  <svg class="absolute bottom-16 left-12 w-24 h-20 pointer-events-none opacity-35" viewBox="0 0 100 80">
    <defs>
      <linearGradient id="bookCover" x1="0%" y1="0%" x2="100%" y2="0%">
        <stop offset="0%" style="stop-color:#7C3AED;stop-opacity:1" />
        <stop offset="100%" style="stop-color:#6366F1;stop-opacity:1" />
      </linearGradient>
    </defs>
    <!-- book cover -->
    <rect x="15" y="15" width="70" height="55" rx="4" fill="url(#bookCover)" stroke="#5B21B6" stroke-width="2"/>
    <!-- pages -->
    <rect x="18" y="18" width="64" height="49" rx="2" fill="#FEF3C7"/>
    <line x1="25" y1="30" x2="70" y2="30" stroke="#D97706" stroke-width="1.5" opacity="0.3"/>
    <line x1="25" y1="38" x2="70" y2="38" stroke="#D97706" stroke-width="1.5" opacity="0.3"/>
    <line x1="25" y1="46" x2="65" y2="46" stroke="#D97706" stroke-width="1.5" opacity="0.3"/>
    <line x1="25" y1="54" x2="70" y2="54" stroke="#D97706" stroke-width="1.5" opacity="0.3"/>
    <!-- spine -->
    <rect x="15" y="15" width="8" height="55" rx="2" fill="#5B21B6"/>
    <!-- bookmark -->
    <rect x="60" y="8" width="6" height="15" fill="#F87171"/>
    <polygon points="63,23 60,20 66,20" fill="#F87171"/>
    <!-- cute face -->
    <circle cx="40" cy="40" r="3" fill="#5B21B6"/>
    <circle cx="55" cy="40" r="3" fill="#5B21B6"/>
    <path d="M40 50 Q47.5 55 55 50" stroke="#5B21B6" stroke-width="2.5" fill="none" stroke-linecap="round"/>
  </svg>

  <!-- Colorful stars floating -->
  <svg class="absolute top-1/3 left-20 w-12 h-12 pointer-events-none opacity-40" viewBox="0 0 60 60">
    <path d="M30 5 L35 22 L52 22 L38 32 L43 49 L30 39 L17 49 L22 32 L8 22 L25 22 Z" fill="#22D3EE" stroke="#0891B2" stroke-width="2"/>
    <circle cx="30" cy="20" r="3" fill="#fff" opacity="0.6"/>
  </svg>

  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="bg-white rounded-3xl lg:rounded-4xl border border-purple-100 shadow-xl p-6 sm:p-10 lg:p-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

        <!-- Left: Info -->
        <div class="lg:col-span-5 space-y-4">
          <span class="text-xs font-extrabold text-kid-purple tracking-widest uppercase bg-purple-100 px-3 py-1 rounded-full">Formulir Pendaftaran</span>
          <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 leading-tight">
            Daftarkan Buah Hati Hari Ini!
          </h2>
          <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
            Isi data singkat berikut. Tim admin kami akan segera menghubungi Ayah / Bunda untuk sesi konsultasi gratis dan penempatan kelas.
          </p>
          <div class="space-y-3 pt-3">
            <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-700 font-semibold">
              <i class="fa-solid fa-circle-check text-kid-green text-base"></i>
              <span>Laporan perkembangan per 3 bulan</span>
            </div>
            <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-700 font-semibold">
              <i class="fa-solid fa-circle-check text-kid-green text-base"></i>
              <span>Ekskul (Pembelajaran Tambahan)</span>
            </div>
            <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-700 font-semibold">
              <i class="fa-solid fa-circle-check text-kid-green text-base"></i>
              <span>Kuota Terbatas</span>
            </div>
          </div>
          <div class="pt-4 border-t border-slate-100">
            <p class="text-xs text-slate-500">
              Punya pertanyaan cepat? <br>
              <a href="https://wa.me/<?= $nomor_whatsapp_sekolah ?>?text=<?= $pesan_whatsapp ?>" target="_blank" class="text-kid-purple font-bold hover:underline">
                Chat via WhatsApp Admin: <?= $nomor_whatsapp_tampil ?>
              </a>
            </p>
          </div>
        </div>

        <!-- Right: Form -->
        <div class="lg:col-span-7 bg-slate-50 rounded-2xl p-6 border border-slate-200/80">
          <?php if ($sukses): ?>
          <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <div>
              <div class="font-heading font-black text-sm">Pendaftaran Berhasil Dikirim!</div>
              <div class="text-xs font-medium opacity-80 mt-0.5">Tim kami akan menghubungi Anda dalam 1×24 jam.</div>
            </div>
          </div>
          <?php endif; ?>
          <?php if ($gagal): ?>
          <div class="mb-5 p-4 rounded-2xl bg-red-50 border-l-4 border-red-500 text-red-800 font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
            <div class="text-sm">Mohon lengkapi semua data yang diperlukan.</div>
          </div>
          <?php endif; ?>

          <form action="ppdb_submit.php" method="post" class="space-y-5">

            <!-- Nama Anak -->
            <div>
              <label for="nama_anak" class="block text-sm font-extrabold text-slate-700 mb-2 font-heading">
                <i class="fa-solid fa-child text-kid-purple mr-1"></i> Nama Lengkap Anak
              </label>
              <input type="text" id="nama_anak" name="nama_anak"
                     placeholder="Contoh: Kenzo Alfarizi" required
                     class="w-full text-base font-semibold rounded-2xl border-2 border-slate-200 focus:border-kid-purple focus:ring-2 focus:ring-purple-200 bg-white px-4 py-3.5 placeholder:text-slate-400 placeholder:font-normal transition-all">
            </div>

            <!-- Usia + Orang Tua -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="usia_anak" class="block text-sm font-extrabold text-slate-700 mb-2 font-heading">
                  <i class="fa-solid fa-cake-candles text-kid-orange mr-1"></i> Usia Anak (tahun)
                </label>
                <input type="number" id="usia_anak" name="usia_anak"
                       min="2" max="7" placeholder="4" required
                       class="w-full text-base font-semibold rounded-2xl border-2 border-slate-200 focus:border-kid-orange focus:ring-2 focus:ring-orange-200 bg-white px-4 py-3.5 placeholder:text-slate-400 placeholder:font-normal transition-all">
              </div>
              <div>
                <label for="nama_ortu" class="block text-sm font-extrabold text-slate-700 mb-2 font-heading">
                  <i class="fa-solid fa-users text-kid-green mr-1"></i> Nama Orang Tua
                </label>
                <input type="text" id="nama_ortu" name="nama_ortu"
                       placeholder="Bunda / Ayah Rina" required
                       class="w-full text-base font-semibold rounded-2xl border-2 border-slate-200 focus:border-kid-green focus:ring-2 focus:ring-green-200 bg-white px-4 py-3.5 placeholder:text-slate-400 placeholder:font-normal transition-all">
              </div>
            </div>

            <!-- Alamat Saat Ini -->
            <div>
              <label for="alamat" class="block text-sm font-extrabold text-slate-700 mb-2 font-heading">
                <i class="fa-solid fa-location-dot text-kid-orange mr-1"></i> Alamat Saat Ini
              </label>
              <textarea id="alamat" name="alamat" rows="2" maxlength="500" required
                        placeholder="Masukkan alamat tempat tinggal saat ini"
                        class="w-full text-base font-semibold rounded-2xl border-2 border-slate-200 focus:border-kid-green focus:ring-2 focus:ring-green-200 bg-white px-4 py-3.5 placeholder:text-slate-400 placeholder:font-normal transition-all"></textarea>
            </div>

            <!-- WhatsApp -->
            <div>
              <label for="whatsapp" class="block text-sm font-extrabold text-slate-700 mb-2 font-heading">
                <i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i> Nomor WhatsApp Aktif
              </label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm select-none">+62</span>
                <input type="tel" id="whatsapp" name="whatsapp"
                       placeholder="8xx-xxxx-xxxx" required
                       class="w-full text-base font-semibold rounded-2xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 bg-white pl-14 pr-4 py-3.5 placeholder:text-slate-400 placeholder:font-normal transition-all">
              </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full py-4 px-6 rounded-2xl bg-kid-purple hover:bg-kid-purple-dark text-white font-heading font-black text-base tracking-wide shadow-lg shadow-purple-300/60 transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
              <i class="fa-solid fa-paper-plane text-lg"></i>
              Kirim Pendaftaran Sekarang
              <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>

            <p class="text-center text-xs text-slate-400 font-medium">
              <i class="fa-solid fa-lock mr-1"></i> Data Anda aman dan tidak akan dibagikan kepada pihak lain.
            </p>
          </form>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- About modal -->
<div id="aboutModal" class="fixed inset-0 z-[9998] hidden items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="aboutModalTitle" onclick="if(event.target === this) closeAboutModal()">
  <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl">
    <div class="flex items-center justify-between bg-kid-green px-5 py-4 text-white sm:px-7">
      <div>
        <p class="text-xs font-bold uppercase tracking-widest text-emerald-100">Tentang Kami</p>
        <h2 id="aboutModalTitle" class="font-heading text-xl font-black sm:text-2xl">Education House Preschool</h2>
      </div>
      <button type="button" onclick="closeAboutModal()" aria-label="Tutup informasi tentang kami" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-xl transition-colors hover:bg-white/30">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="max-h-[75vh] overflow-y-auto px-5 py-6 text-sm leading-relaxed text-slate-600 sm:px-7">
      <p class="mb-4 text-base font-semibold leading-7 text-slate-700">
        Sekolah kami menyediakan pendidikan komprehensif yang akan membantu anak Anda mencapai tujuan.
      </p>

      <ul class="mb-5 space-y-4 text-sm leading-6 text-slate-600">
        <li class="flex items-start gap-3">
          <span class="mt-0.5 text-lg leading-none">🌟</span>
          <div>
            <strong class="block font-heading text-base font-black text-slate-800">Welcome to Education House Preschool!</strong>
            <span>Sekolah anak usia dini untuk usia 2–7 tahun.</span>
          </div>
        </li>
        <li class="flex items-start gap-3">
          <span class="mt-0.5 text-lg leading-none">✨</span>
          <div>
            <strong class="block font-heading text-base font-black text-slate-800">Fun Learning with Montessori Approach</strong>
            <span>Anak belajar sambil bermain melalui metode Montessori yang menyenangkan dan interaktif, didampingi guru-guru yang ramah dan penuh kasih.</span>
          </div>
        </li>
        <li class="flex items-start gap-3">
          <span class="mt-0.5 text-lg leading-none">🌱</span>
          <div>
            <strong class="block font-heading text-base font-black text-slate-800">Kami Cinta Lingkungan</strong>
            <span>Anak dikenalkan pada kepedulian terhadap lingkungan melalui kegiatan indoor dan outdoor yang seru serta edukatif.</span>
          </div>
        </li>
        <li class="flex items-start gap-3">
          <span class="mt-0.5 text-lg leading-none">🗣️</span>
          <div>
            <strong class="block font-heading text-base font-black text-slate-800">English Everyday</strong>
            <span>Penggunaan bahasa Inggris dalam keseharian membantu membangun kemampuan komunikasi anak sejak usia dini.</span>
          </div>
        </li>
        <li class="flex items-start gap-3">
          <span class="mt-0.5 text-lg leading-none">💖</span>
          <div>
            <strong class="block font-heading text-base font-black text-slate-800">Pendidikan Karakter</strong>
            <span>Nilai tanggung jawab, empati, dan kemandirian ditanamkan untuk membentuk anak yang cerdas dan berkarakter.</span>
          </div>
        </li>
        <li class="flex items-start gap-3">
          <span class="mt-0.5 text-lg leading-none">🎉</span>
          <div>
            <strong class="block font-heading text-base font-black text-slate-800">Aktivitas Seru Setiap Hari</strong>
            <span>Anak dapat mengikuti kegiatan berkebun, memasak, seni, bermain air, hingga field trip dengan cara belajar yang menyenangkan.</span>
          </div>
        </li>
      </ul>

      <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl bg-purple-50 p-4">
          <h3 class="mb-2 font-heading font-black text-kid-purple">Visi Kami</h3>
          <p>Menjadi sekolah toddler dan preschool unggulan yang menciptakan generasi anak usia dini yang kreatif, berkarakter, dan memiliki pondasi kuat untuk belajar sepanjang hayat.</p>
        </div>
        <div class="rounded-2xl bg-amber-50 p-4">
          <h3 class="mb-2 font-heading font-black text-amber-600">Pendekatan Belajar</h3>
          <p>Kami memadukan pembelajaran akademik dasar dengan kegiatan bermain, eksplorasi, dan penanaman budi pekerti agar anak tumbuh percaya diri serta mandiri.</p>
        </div>
      </div>

      <h3 class="mt-6 mb-3 font-heading text-lg font-black text-slate-800">Yang Anak Dapatkan</h3>
      <ul class="space-y-3">
        <li class="flex items-start gap-3"><i class="fa-solid fa-circle-check mt-1 text-kid-green"></i><span>Pembelajaran yang sesuai tahap tumbuh kembang anak.</span></li>
        <li class="flex items-start gap-3"><i class="fa-solid fa-circle-check mt-1 text-kid-green"></i><span>Pengembangan kreativitas, karakter, sosial, motorik, dan kemandirian.</span></li>
        <li class="flex items-start gap-3"><i class="fa-solid fa-circle-check mt-1 text-kid-green"></i><span>Laporan perkembangan setiap 3 bulan dan komunikasi dengan orang tua.</span></li>
        <li class="flex items-start gap-3"><i class="fa-solid fa-circle-check mt-1 text-kid-green"></i><span>Kegiatan tambahan dan ekskul untuk memperluas pengalaman belajar.</span></li>
      </ul>

      <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
        <p class="font-bold text-emerald-800">Ingin mengetahui kelas yang paling sesuai?</p>
        <p class="mt-1">Hubungi admin kami melalui WhatsApp untuk konsultasi dan informasi pendaftaran.</p>
      </div>
    </div>
  </div>
</div>

<script>
function openAboutModal() {
  var modal = document.getElementById('aboutModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  document.body.style.overflow = 'hidden';
}

function closeAboutModal() {
  var modal = document.getElementById('aboutModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape' && !document.getElementById('aboutModal').classList.contains('hidden')) {
    closeAboutModal();
  }
});
</script>

<?php require 'includes/footer.php'; ?>
