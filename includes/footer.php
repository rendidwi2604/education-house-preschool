<!-- ══════════════════════════════════════
     FOOTER
══════════════════════════════════════ -->
<footer style="background:#328C39;color:#DCFCE7;font-family:'Nunito',sans-serif;">

  <!-- Main grid -->
  <div class="site-footer-main">

    <!-- Brand -->
    <div>
      <a href="index.php" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:14px;">
        <picture>
          <source srcset="assets/img/Logo_EduHouse.webp" type="image/webp">
          <img src="assets/img/Logo_EduHouse.png" alt="Logo Education House Preschool"
               width="44" height="44"
               style="height:44px;width:auto;object-fit:contain;filter:drop-shadow(0 2px 6px rgba(0,0,0,.35));"
               loading="lazy">
        </picture>
      </a>

      <p style="font-size:13px;line-height:1.7;color:#BBF7D0;margin:0 0 18px;max-width:220px;">
        Sekolah yang mendidik dengan hati, menginspirasi anak-anak untuk masa depan yang cerah.
      </p>

      <!-- Social -->
      <div style="display:flex;gap:8px;">
          <a href="https://www.tiktok.com/@educationhouse.preschool" aria-label="TikTok" title="Buka TikTok Education House" target="_blank" rel="noopener noreferrer"
            style="width:33px;height:33px;border-radius:8px;background:#166534;display:flex;align-items:center;justify-content:center;transition:background .18s;"
            onmouseenter="this.style.background='#F97316'" onmouseleave="this.style.background='#166534'">
           <i class="fa-brands fa-tiktok" aria-hidden="true" style="font-size:14px;color:#fff;"></i>
        </a>
        <a href="#" aria-label="Instagram"
           style="width:33px;height:33px;border-radius:8px;background:#166534;display:flex;align-items:center;justify-content:center;transition:background .18s;"
           onmouseenter="this.style.background='#EC4899'" onmouseleave="this.style.background='#166534'">
          <svg width="14" height="14" fill="#A5B4FC" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        </a>
          <a href="https://www.tiktok.com/@educationhouse.preschool" aria-label="Situs TikTok" title="Buka TikTok Education House" target="_blank" rel="noopener noreferrer"
           style="width:33px;height:33px;border-radius:8px;background:#166534;display:flex;align-items:center;justify-content:center;transition:background .18s;"
            onmouseenter="this.style.background='#F97316'" onmouseleave="this.style.background='#166534'">
           <i class="fa-solid fa-globe" aria-hidden="true" style="font-size:14px;color:#fff;"></i>
        </a>
      </div>
    </div>

    <!-- Navigasi -->
    <div>
      <h4 style="font-family:'Baloo 2',sans-serif;font-size:12px;font-weight:800;color:#E0E7FF;text-transform:uppercase;letter-spacing:.07em;margin:0 0 14px;">Navigasi</h4>
      <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:9px;">
        <?php
        $flinks = [
          ['index.php#beranda',  'Beranda'],
          ['index.php#tentang',  'Tentang Kami'],
          ['index.php#pengajar', 'Pengajar'],
          ['index.php#kegiatan', 'Kegiatan'],
          ['index.php#kegiatan-islami', 'Kegiatan Islami'],
          ['index.php#ppdb',     'PPDB'],
          ['index.php#berita',   'Berita'],
        ];
        foreach ($flinks as [$href, $lbl]):
        ?>
        <li>
          <a href="<?= $href ?>"
             style="font-size:14px;color:#E5F5E1;text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:color .15s;"
             onmouseenter="this.style.color='#FFFFFF'" onmouseleave="this.style.color='#E5F5E1'">
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <?= $lbl ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Program -->
    <div>
      <h4 style="font-family:'Baloo 2',sans-serif;font-size:12px;font-weight:800;color:#E0E7FF;text-transform:uppercase;letter-spacing:.07em;margin:0 0 14px;">Program</h4>
      <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:9px;">
        <?php foreach (['Toodler(1,5 - 3 tahun)','Playgroup(3–4 tahun)','Kindergarten A (4-5 tahun)','Kinderkarten B (5-6 tahun)'] as $p): ?>
        <li>
          <a href="index.php#ppdb"
             style="font-size:14px;color:#E5F5E1;text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:color .15s;"
             onmouseenter="this.style.color='#FFFFFF'" onmouseleave="this.style.color='#E5F5E1'">
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <?= $p ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Kontak -->
    <div>
      <h4 style="font-family:'Baloo 2',sans-serif;font-size:12px;font-weight:800;color:#E0E7FF;text-transform:uppercase;letter-spacing:.07em;margin:0 0 14px;">Kontak</h4>
      <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:12px;">

        <li style="display:flex;align-items:flex-start;gap:9px;">
          <div style="width:28px;height:28px;background:#2D2B70;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
            <svg width="13" height="13" fill="none" stroke="#A5B4FC" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
          </div>
          <div>
            <div style="font-size:11.5px;color:#E5F5E1;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin-bottom:1px;">Telepon</div>
            <a href="https://wa.me/6285863649047?text=Halo%20Admin%2C%20saya%20ingin%20menanyakan%20info%20tentang%20Education%20House%20Preschool." target="_blank" rel="noopener" style="font-size:14px;color:#FFFFFF;font-weight:700;text-decoration:none;">0858-6364-9047</a>
          </div>
        </li>

        <li style="display:flex;align-items:flex-start;gap:9px;">
          <div style="width:28px;height:28px;background:#2D2B70;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
            <svg width="13" height="13" fill="#A5B4FC" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
          </div>
          <div>
            <div style="font-size:11.5px;color:#E5F5E1;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin-bottom:1px;">Instagram</div>
            <a href="https://www.instagram.com/educationhouse.preschool?stkn=MTg0NTBwNnhzZHo4eQ==" target="_blank" rel="noopener" style="font-size:13.5px;color:#FFFFFF;font-weight:600;text-decoration:none;">@educationhouse.preschool</a>
          </div>
        </li>

        <li style="display:flex;align-items:flex-start;gap:9px;">
          <div style="width:28px;height:28px;background:#2D2B70;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
            <svg width="13" height="13" fill="none" stroke="#A5B4FC" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
          </div>
          <div>
            <div style="font-size:11.5px;color:#E5F5E1;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin-bottom:1px;">Alamat</div>
            <span style="font-size:13.5px;color:#FFFFFF;font-weight:600;line-height:1.5;">Jl pembangunan IV, Pabuaran, gunung putri Cicadas , Bogor Jawa Barat</span>
          </div>
        </li>

      </ul>
    </div>
  </div>

  <!-- Bottom bar -->
  <div style="border-top:1px solid #2D2B70;max-width:1152px;margin:0 auto;padding:16px 24px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;">
    <span style="font-size:13px;color:#E5F5E1;">
      © <?= date('Y') ?> Education House Preschool. Semua hak dilindungi.
    </span>
    <div style="display:flex;align-items:center;gap:14px;">
      <span style="font-size:13px;color:#E5F5E1;display:flex;align-items:center;gap:4px;">
        Dibuat dengan
        <svg width="12" height="12" fill="#EC4899" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
        untuk anak-anak
      </span>
      <span style="color:#2D2B70;">|</span>
      <a href="admin/login.php"
        style="font-size:13px;color:#E5F5E1;text-decoration:none;display:inline-flex;align-items:center;gap:4px;transition:color .15s;"
        onmouseenter="this.style.color='#FFFFFF'" onmouseleave="this.style.color='#E5F5E1'">
        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
        Admin
      </a>
    </div>
  </div>
</footer>
</body>
</html>
