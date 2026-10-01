<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($page_title) ? h($page_title) . ' — ' : '' ?>Education House Preschool</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        heading: ['Quicksand', 'sans-serif'],
        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
      },
      colors: {
        kid: {
          purple: '#58A834',
          'purple-dark': '#468926',
          amber: '#F59E0B',
          orange: '#F97316',
          'orange-dark': '#EA580C',
          green: '#69BD45',
          'green-dark': '#4A9A32',
          teal: '#06B6D4',
          blue: '#2B8FE8',
          'blue-dark': '#1C74C5',
          pink: '#EC4899',
          yellow: '#FBBF24',
          cream: '#FEF9EE'
        }
      },
      borderRadius: {
        '4xl': '2rem',
        '5xl': '2.5rem',
      }
    }
  }
}
</script>
<link rel="stylesheet" href="assets/css/style.css?v=20260930">
<style>
  *, *::before, *::after { box-sizing: border-box; }
  body  { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(135deg, #E4F5DC 0%, #EFF8E9 72%, #FFF0DE 100%); }
  h1,h2,h3,h4 { font-family: 'Quicksand', sans-serif; }
  .program-card { transition: all 0.28s cubic-bezier(0.34, 1.56, 0.64, 1); }
  .program-card:hover { transform: translateY(-6px); }
  .sun-shine { animation: rotateSlow 28s linear infinite; }
  @keyframes rotateSlow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

  /* ── Mobile drawer ── */
  #mob-drawer {
    transform: translateX(100%);
    transition: transform .26s cubic-bezier(.4,0,.2,1);
  }
  #mob-drawer.open { transform: translateX(0); }
  #mob-backdrop { opacity:0; pointer-events:none; transition: opacity .26s; }
  #mob-backdrop.open { opacity:1; pointer-events:auto; }
</style>
</head>
<body class="text-gray-800">

<!-- ══════════════════════════════════════
     NAVBAR  — compact, full-color à la ref
══════════════════════════════════════ -->
<header style="background:#328C39;position:sticky;top:0;z-index:50;box-shadow:0 2px 12px rgba(50,140,57,.22);">
  <div style="max-width:1152px;margin:0 auto;padding:0 20px;height:56px;display:flex;align-items:center;gap:20px;">

    <!-- ── BRAND ── -->
    <a href="index.php" style="display:flex;align-items:center;gap:9px;text-decoration:none;flex-shrink:0;">
      <img src="assets/img/Logo_EduHouse.png" alt="Education House Logo"
           style="height:38px;width:auto;object-fit:contain;filter:drop-shadow(0 1px 4px rgba(0,0,0,.25));">
      <div style="line-height:1.15;">
        <div style="font-family:'Quicksand',sans-serif;font-size:15px;font-weight:800;color:#fff;white-space:nowrap;">Education House</div>
        <div style="font-size:10px;color:rgba(255,255,255,.65);font-weight:600;">Sekolah Toddler & Preschool
</div>
      </div>
    </a>

    <!-- ── DESKTOP NAV ── -->
    <nav style="display:flex;align-items:center;gap:5px;flex:1;justify-content:center;" class="hidden-mobile">

      <!-- Beranda -->
      <a href="index.php#beranda"
        style="display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:999px;background:#D98B55;color:#fff;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap;transition:filter .15s;"
        onmouseenter="this.style.filter='brightness(1.12)'" onmouseleave="this.style.filter='brightness(1)'">
        <!-- home icon -->
        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M11.47 3.841a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.061l-1.329-1.33V6.75A.75.75 0 0020.25 6h-2.25a.75.75 0 00-.75.75v1.638L12.53 3.841a.75.75 0 00-1.06 0l-8.69 8.69a.75.75 0 001.061 1.06l1.329-1.329V19.5A1.5 1.5 0 006.75 21h4.5a.75.75 0 00.75-.75V16.5a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75V20.25a.75.75 0 00.75.75h4.5a1.5 1.5 0 001.5-1.5v-7.258l1.33 1.33a.75.75 0 101.06-1.061l-8.69-8.69z"/></svg>
        Beranda
      </a>

      <!-- Tentang — orange -->
      <a href="index.php#tentang"
         style="display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:999px;background:#D98B55;color:#fff;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap;transition:filter .15s;"
         onmouseenter="this.style.filter='brightness(1.12)'" onmouseleave="this.style.filter='brightness(1)'">
        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5A.75.75 0 0012 9z" clip-rule="evenodd"/></svg>
        Tentang
      </a>

      <!-- Pengajar — green -->
      <a href="index.php#pengajar"
         style="display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:999px;background:#D98B55;color:#fff;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap;transition:filter .15s;"
         onmouseenter="this.style.filter='brightness(1.12)'" onmouseleave="this.style.filter='brightness(1)'">
        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z"/></svg>
        Pengajar
      </a>

      <!-- Kegiatan — yellow -->
      <a href="index.php#kegiatan"
         style="display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:999px;background:#D98B55;color:#fff;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap;transition:filter .15s;"
         onmouseenter="this.style.filter='brightness(1.08)'" onmouseleave="this.style.filter='brightness(1)'">
        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd"/></svg>
        Kegiatan
      </a>

      <!-- PPDB — pink/rose -->
      <a href="index.php#pendaftaran"
         style="display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:999px;background:#D98B55;color:#fff;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap;transition:filter .15s;"
         onmouseenter="this.style.filter='brightness(1.12)'" onmouseleave="this.style.filter='brightness(1)'">
        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75-6.75a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd"/><path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z"/></svg>
        PPDB
      </a>

      <!-- Berita — sky/blue -->
      <a href="index.php#berita"
         style="display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:999px;background:#D98B55;color:#fff;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap;transition:filter .15s;"
         onmouseenter="this.style.filter='brightness(1.12)'" onmouseleave="this.style.filter='brightness(1)'">
        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M4.125 3C3.089 3 2.25 3.84 2.25 4.875V18a3 3 0 003 3h15.75A1.5 1.5 0 0022.5 19.5v-4.875c0-.621-.504-1.125-1.125-1.125H18.75a3 3 0 01-3-3V4.875C15.75 3.839 14.911 3 13.875 3H4.125zM6 12a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5H6zm.75 4.5a.75.75 0 01.75-.75H9a.75.75 0 010 1.5H7.5a.75.75 0 01-.75-.75z" clip-rule="evenodd"/><path d="M16.5 6.75v6h4.875c.621 0 1.125-.504 1.125-1.125V6.75a1.5 1.5 0 00-1.5-1.5h-3a1.5 1.5 0 00-1.5 1.5z"/></svg>
        Berita
      </a>

    </nav>

    <!-- ── RIGHT: phone + CTA ── -->
    <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;margin-left:auto;" class="hidden-mobile">

      <!-- phone -->
      <div style="display:flex;align-items:center;gap:7px;">
        <div style="width:28px;height:28px;background:rgba(255,255,255,.15);border-radius:7px;display:flex;align-items:center;justify-content:center;">
          <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
          </svg>
        </div>
        <div>
          <div style="font-size:9.5px;color:rgba(255,255,255,.6);font-weight:600;font-family:'Nunito',sans-serif;line-height:1;">Hubungi Kami</div>
          <a href="https://wa.me/6285863649047?text=Halo%20Admin%2C%20saya%20ingin%20menanyakan%20info%20tentang%20Education%20House%20Preschool." target="_blank" rel="noopener" style="font-size:12.5px;color:#fff;font-weight:800;font-family:'Nunito',sans-serif;line-height:1.2;white-space:nowrap;text-decoration:none;">0858-6364-9047</a>
        </div>
      </div>

      <!-- CTA -->
      <a href="index.php#pendaftaran"
         style="display:inline-flex;align-items:center;gap:7px;background:#D98B55;color:#fff;font-family:'Baloo 2',sans-serif;font-weight:800;font-size:13px;padding:7px 16px;border-radius:999px;text-decoration:none;white-space:nowrap;box-shadow:0 2px 10px rgba(0,0,0,.2);transition:filter .15s;"
         onmouseenter="this.style.filter='brightness(1.12)'" onmouseleave="this.style.filter='brightness(1)'">
        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zm4.5-1.5a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0 3 3 0 00-6 0z" clip-rule="evenodd"/></svg>
        Daftar Sekarang
      </a>
    </div>

    <!-- ── HAMBURGER ── -->
    <button id="mob-open" aria-label="Buka menu"
            style="margin-left:auto;background:#D98B55;border:none;width:36px;height:36px;border-radius:9px;display:none;align-items:center;justify-content:center;cursor:pointer;"
            class="show-mobile">
      <svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2.3" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </button>

  </div>
</header>

<style>
  @media (max-width: 900px) {
    .hidden-mobile { display: none !important; }
    .show-mobile   { display: flex !important; }
  }
</style>

<!-- ══ BACKDROP ══ -->
<div id="mob-backdrop" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:60;" onclick="closeMob()"></div>

<!-- ══ MOBILE DRAWER ══ -->
<aside id="mob-drawer" style="position:fixed;top:0;right:0;height:100%;width:264px;background:#fff;z-index:70;display:flex;flex-direction:column;box-shadow:-6px 0 28px rgba(0,0,0,.16);">

  <!-- Drawer header -->
  <div style="background:#328C39;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;">
    <div style="display:flex;align-items:center;gap:8px;">
      <img src="assets/img/Logo_EduHouse.png" alt="Education House Logo"
           style="height:32px;width:auto;object-fit:contain;filter:drop-shadow(0 1px 4px rgba(0,0,0,.25));flex-shrink:0;">
      <div style="line-height:1.2;">
        <div style="font-family:'Quicksand',sans-serif;font-size:13px;font-weight:800;color:#fff;">Education House</div>
        <div style="font-size:9.5px;color:rgba(255,255,255,.6);font-weight:600;">Bright Minds, Bright Futures</div>
      </div>
    </div>
    <button id="mob-close" onclick="closeMob()" aria-label="Tutup"
            style="background:#D98B55;border:none;width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
      <svg width="15" height="15" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  <!-- Nav links -->
  <nav style="flex:1;overflow-y:auto;padding:14px 12px;display:flex;flex-direction:column;gap:4px;">
    <?php
    $mob = [
      ['index.php#beranda', 'Beranda',  '#D98B55', 'M11.47 3.841a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.061l-1.329-1.33V6.75A.75.75 0 0020.25 6h-2.25a.75.75 0 00-.75.75v1.638L12.53 3.841a.75.75 0 00-1.06 0l-8.69 8.69a.75.75 0 001.061 1.06l1.329-1.329V19.5A1.5 1.5 0 006.75 21h4.5a.75.75 0 00.75-.75V16.5a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75V20.25a.75.75 0 00.75.75h4.5a1.5 1.5 0 001.5-1.5v-7.258l1.33 1.33a.75.75 0 101.06-1.061l-8.69-8.69z'],
      ['index.php#tentang', 'Tentang',  '#D98B55', 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'],
      ['index.php#pengajar','Pengajar', '#D98B55', 'M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z'],
      ['index.php#kegiatan','Kegiatan', '#D98B55', 'M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z'],
      ['index.php#pendaftaran', 'PPDB',     '#D98B55', 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
      ['index.php#berita',  'Berita',   '#D98B55', 'M4.125 3C3.089 3 2.25 3.84 2.25 4.875V18a3 3 0 003 3h15.75A1.5 1.5 0 0022.5 19.5v-4.875c0-.621-.504-1.125-1.125-1.125H18.75a3 3 0 01-3-3V4.875C15.75 3.839 14.911 3 13.875 3H4.125zM6 12a.75.75 0 000 1.5h7.5a.75.75 0 010-1.5H6zm.75 4.5a.75.75 0 01.75-.75H9a.75.75 0 010 1.5H7.5a.75.75 0 01-.75-.75z'],
    ];
    foreach ($mob as [$href, $label, $color, $path]):
    ?>
    <a href="<?= $href ?>" onclick="closeMob()"
       style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:12px;text-decoration:none;color:#374151;font-family:'Nunito',sans-serif;font-weight:700;font-size:14px;transition:background .15s;"
      onmouseenter="this.style.background='#FFF7ED'" onmouseleave="this.style.background='transparent'">
      <div style="width:32px;height:32px;border-radius:9px;background:<?= $color ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="16" height="16" fill="#fff" viewBox="0 0 24 24"><path fill-rule="evenodd" d="<?= $path ?>" clip-rule="evenodd"/></svg>
      </div>
      <?= $label ?>
    </a>
    <?php endforeach; ?>
  </nav>

  <!-- Drawer footer -->
  <div style="padding:12px;border-top:1px solid #f3f4f6;">
    <a href="index.php#pendaftaran" onclick="closeMob()"
      style="display:flex;align-items:center;justify-content:center;gap:8px;background:#D98B55;color:#fff;font-family:'Baloo 2',sans-serif;font-weight:800;font-size:14px;padding:11px;border-radius:12px;text-decoration:none;">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zm4.5-1.5a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0 3 3 0 00-6 0z" clip-rule="evenodd"/></svg>
      Daftar Sekarang
    </a>
    <div style="text-align:center;margin-top:8px;font-size:12px;color:#9ca3af;font-family:'Nunito',sans-serif;">
      <a href="https://wa.me/6285863649047?text=Halo%20Admin%2C%20saya%20ingin%20menanyakan%20info%20tentang%20Education%20House%20Preschool." target="_blank" rel="noopener" style="font-weight:700;color:#6b7280;text-decoration:none;">0858-6364-9047</a>
    </div>
  </div>
</aside>

<script>
function openMob() {
  document.getElementById('mob-drawer').classList.add('open');
  document.getElementById('mob-backdrop').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeMob() {
  document.getElementById('mob-drawer').classList.remove('open');
  document.getElementById('mob-backdrop').classList.remove('open');
  document.body.style.overflow = '';
}
document.getElementById('mob-open').addEventListener('click', openMob);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMob(); });
</script>
