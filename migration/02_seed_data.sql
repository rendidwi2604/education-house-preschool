-- ============================================================
-- MIGRATION 02: Seed Data (Data awal dari database MySQL)
-- Target: Supabase (PostgreSQL 15+)
--
-- PENTING: Jalankan SETELAH 01_schema.sql
-- ============================================================

-- ── admin ───────────────────────────────────────────────────
INSERT INTO admin (id, username, password, nama, foto, created_at) VALUES
(8, 'admin',  '$2y$10$zjWEVFmHSeOco3SnDz.U3.iYqjuXJaI8j7j3bK7erSPD6rqMZxztW', 'admin', NULL, '2026-09-29 12:02:36+00'),
(9, 'murni',  '$2y$10$ju61P2456TED5BRNOpVDeuTemjmS/1/Jua5qi0.D95dJ5/UjnTA2S', 'murni', NULL, '2026-09-30 02:27:32+00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('admin_id_seq', (SELECT MAX(id) FROM admin));

-- ── berita ──────────────────────────────────────────────────
INSERT INTO berita (id, judul, isi, kategori, gambar, status, created_at) VALUES
(5,  'Selamat dan sukses untuk ananda Nabil sebagai siswa berprestasi 2025-2026',
     'Selamat kepada Ananda Nabil yang menjadi salah satu Siswa Berprestasi Tahun Ajaran 2025-2026.',
     'Prestasi', 'berita_1790610874_640.png', 'terbit', '2026-09-28 15:51:52+00'),
(6,  'Selamat dan sukses untuk ananda Nabil atas naiknya ke level Calistung 5',
     'Selamat atas pencapaiannya yang telah naik ke Level Calistung 5.',
     'Prestasi', 'berita_1790610976_939.png', 'terbit', '2026-09-28 15:56:16+00'),
(7,  'Selamat dan sukses untuk adinda Razeta atas naiknya ke level Calistung 4',
     'Selamat atas pencapaiannya yang telah naik ke Level Calistung 4.',
     'Prestasi', 'berita_1790611049_710.png', 'terbit', '2026-09-28 15:57:29+00'),
(8,  'Selamat kepada ananda nizam adinata akbar atas siswa berprestasi',
     'Selamat atas pencapaiannya sebagai Siswa Berprestasi.',
     'Prestasi', 'berita_1790739837_635.jpeg', 'terbit', '2026-09-30 03:43:57+00'),
(9,  'Selamat sukses kepada ananda rafasya',
     'Selamat kepada Ananda Rafasya yang telah diterima di SDN Cicadas 06.',
     'Prestasi', 'berita_1790739936_796.jpeg', 'terbit', '2026-09-30 03:45:36+00'),
(10, 'Santri Teladan Ramadhan 2026',
     'Selamat kepada Ananda Razeta yang telah menjadi Santri Teladan Ramadhan 2026.',
     'Prestasi', 'berita_1790740830_378.jpeg', 'terbit', '2026-09-30 04:00:30+00'),
(11, 'Kegiatan Pada Hari Anak Nasional',
     'Kelas Bermain tampil percaya diri dalam kegiatan menari dan menyanyi pada Hari Anak Nasional 2026.',
     'Kegiatan', 'berita_1790741196_223.jpeg', 'terbit', '2026-09-30 04:06:36+00'),
(12, 'Selamat Kepada Ananda Nadhira mendapatkan Piala juara 1 saat lomba 17 agustus 2026',
     'Selamat atas prestasinya meraih Juara 1 dalam lomba memperingati HUT Kemerdekaan RI ke-81.',
     'Pengumuman', 'berita_1790742048_641.jpeg', 'terbit', '2026-09-30 04:20:48+00'),
(13, 'Reward Terbanyak Di laporan Perkembangan Septermber 2026',
     'Selamat kepada ananda syafa mendapatkan Reward Terbanyak Di laporan Perkembangan September 2026.',
     'Prestasi', 'berita_1790830707_283.jpeg', 'terbit', '2026-10-01 04:58:27+00'),
(14, 'Siswa mendapatkan reward terbanyak di laporan perkembangan september 2026',
     'Selamat kepada ananda nata yang telah mendapatkan reward terbanyak di laporan perkembangan september 2026.',
     'Prestasi', 'berita_1790830767_325.jpeg', 'terbit', '2026-10-01 04:59:27+00'),
(15, 'Siswa mendapatkan reward terbanyak di laporan perkembangan september 2026',
     'Selamat kepada ananda Razeta telah mendapatkan reward terbanyak di laporan perkembangan september 2026.',
     'Prestasi', 'berita_1790830846_668.jpeg', 'terbit', '2026-10-01 05:00:46+00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('berita_id_seq', (SELECT MAX(id) FROM berita));

-- ── galeri ──────────────────────────────────────────────────
INSERT INTO galeri (id, keterangan, gambar, created_at) VALUES
(4, 'Keliling kebun Hidroponik & Belajar Menanam', 'galeri_1790611236_500.png', '2026-09-28 16:00:36+00'),
(5, 'Dokumentasi Belajar bahasa inggris',           'galeri_1790611260_560.png', '2026-09-28 16:01:00+00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('galeri_id_seq', (SELECT MAX(id) FROM galeri));

-- ── guru ────────────────────────────────────────────────────
INSERT INTO guru (id, nama, jabatan, foto, bio, bidang, pendidikan, pengalaman, created_at) VALUES
(1, 'Miss Halimah', 'Kepala Sekolah', 'guru_1790740230_580.jpeg', '', '', 'S2', '16 tahun', '2026-09-23 09:05:14+00'),
(2, 'Miss Nabila',  'Guru Kelas B',   'guru_1790740845_512.jpeg',
   'Miss Nabila merupakan guru Preschool yang memiliki ketertarikan dalam mendampingi tumbuh kembang anak usia dini.',
   'Preschool', 'SMA', '2 Tahun', '2026-09-23 09:05:14+00'),
(3, 'Miss Nurul',   'Guru Kelas A',   'guru_1790828553_185.jpeg',
   'Miss Nurul adalah guru yang senang mendampingi anak-anak dalam belajar, bermain, dan mengeksplorasi hal-hal baru.',
   'Kelas Toddler', 'SMA_MIPA', '5 Tahun', '2026-09-23 09:05:14+00'),
(5, 'Miss Fatimah', '',               'guru_1790740559_177.jpg',  '', 'IQRO/Tahsin & Tahfiz', '', '', '2026-09-30 03:55:59+00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('guru_id_seq', (SELECT MAX(id) FROM guru));

-- ── instagram_setting ───────────────────────────────────────
INSERT INTO instagram_setting (id, post_url, updated_at) VALUES
(1, 'https://www.instagram.com/reel/DaDcJ3hJTxy/', '2026-09-29 12:48:47+00')
ON CONFLICT (id) DO NOTHING;

-- ── instagram_posts ─────────────────────────────────────────
INSERT INTO instagram_posts (id, post_url, created_at) VALUES
(1,  'https://www.instagram.com/reel/DaDcJ3hJTxy/', '2026-09-29 12:48:47+00'),
(6,  'https://www.instagram.com/reel/DaFwwsnpT1v/', '2026-09-29 12:57:09+00'),
(7,  'https://www.instagram.com/reel/Da16J6jRkdz/', '2026-09-29 12:57:20+00'),
(8,  'https://www.instagram.com/reel/DdDtPLgxAvJ/', '2026-09-29 12:57:32+00'),
(9,  'https://www.instagram.com/reel/DdQ3iERJO6o/', '2026-09-29 13:04:30+00'),
(11, 'https://www.instagram.com/reel/DUFA-gpD_fg/', '2026-09-29 13:16:14+00'),
(12, 'https://www.instagram.com/reel/DUDdvWeEcgS/', '2026-09-29 13:16:28+00'),
(14, 'https://www.instagram.com/reel/DTxKvSykUHk/', '2026-09-29 13:21:24+00'),
(15, 'https://www.instagram.com/p/DSlhAvzkrkA/',    '2026-09-29 13:21:36+00'),
(16, 'https://www.instagram.com/reel/DO7wVmykSYf/', '2026-09-29 13:21:55+00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('instagram_posts_id_seq', (SELECT MAX(id) FROM instagram_posts));

-- ── kegiatan_islami ─────────────────────────────────────────
INSERT INTO kegiatan_islami (id, judul, keterangan, gambar, instagram_url, created_at) VALUES
(1, 'Kisah Nabi Sulaiman dan Semut Kecil', 'Cerita Nabi Sulaiman', NULL, 'https://www.instagram.com/p/DVgsXYxDxjx/', '2026-09-30 08:55:04+00'),
(2, 'Praktek sholat',       'ramadhan seru',       NULL, 'https://www.instagram.com/p/DVdC_K0ETgI/', '2026-09-30 08:57:17+00'),
(3, 'Cerita Nabi Yunus',    NULL,                  NULL, 'https://www.instagram.com/p/DVc1zCYEY8n/', '2026-09-30 08:59:46+00'),
(4, 'Halal Bihalal 1446 H', 'Marhaban ya Ramadhan',NULL, 'https://www.instagram.com/p/DU4sK3LkS9v/', '2026-09-30 09:08:03+00'),
(5, 'Kenangan Keluarga Besar Education House Preschool', NULL, NULL, 'https://www.instagram.com/p/DWiJQmckVFE/', '2026-09-30 09:11:56+00'),
(6, 'Kegiatan MAULID NABI', NULL,                  NULL, 'https://www.instagram.com/reel/DOIt04hkVAG/', '2026-09-30 09:15:14+00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('kegiatan_islami_id_seq', (SELECT MAX(id) FROM kegiatan_islami));

-- ── pendaftar ───────────────────────────────────────────────
INSERT INTO pendaftar (id, nama_anak, nama_ortu, alamat, whatsapp, usia_anak, status, created_at) VALUES
(7, 'murni', 'ijah', 'bogor', '083164905202', 3, 'Diterima', '2026-10-01 05:06:53+00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('pendaftar_id_seq', (SELECT MAX(id) FROM pendaftar));

-- ── slider ──────────────────────────────────────────────────
INSERT INTO slider (id, judul, subjudul, gambar, urutan, aktif, created_at) VALUES
(3, 'Membangun Prestasi dan pembelajaran', 'bangun prestasi & masa depan disini', 'slide_1790738060_951.jpeg', 3, TRUE, '2026-09-30 03:14:20+00'),
(4, 'jadwal kegiatan', NULL, 'slide_1790738183_116.jpeg', 1, TRUE, '2026-09-30 03:16:23+00'),
(5, 'Kegiatan belajar', NULL, 'slide_1790738281_920.jpeg', 2, TRUE, '2026-09-30 03:18:01+00'),
(6, 'VISI & MISI KAMI', NULL, 'slide_1790759844_811.jpeg', 4, TRUE, '2026-09-30 09:17:24+00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('slider_id_seq', (SELECT MAX(id) FROM slider));

-- ── testimoni_orangtua ──────────────────────────────────────
INSERT INTO testimoni_orangtua (id, keterangan, video, instagram_url, tiktok_url, created_at) VALUES
(2, 'ayah dan bunda vihan', NULL, NULL, 'https://www.tiktok.com/@educationhouse.preschool/video/7634409806496828679', '2026-10-01 04:47:08+00'),
(3, 'Testimoni',            NULL, NULL, 'https://www.tiktok.com/@educationhouse.preschool/video/7634409947165396231', '2026-10-01 04:50:54+00'),
(4, 'perkembangan anak',    NULL, NULL, 'https://www.tiktok.com/@educationhouse.preschool/video/7634410405636377864', '2026-10-01 04:53:32+00'),
(5, 'testimoni ortu',       NULL, NULL, 'https://www.tiktok.com/@educationhouse.preschool/video/7635851988860816648', '2026-10-01 05:03:39+00'),
(6, 'testimoni',            NULL, NULL, 'https://www.tiktok.com/@educationhouse.preschool/video/7635852104862600456', '2026-10-01 05:04:11+00'),
(7, 'testimoni',            NULL, NULL, 'https://www.tiktok.com/@educationhouse.preschool/video/7635852178405494023', '2026-10-01 05:04:42+00'),
(8, 'testimoni',            NULL, NULL, 'https://www.tiktok.com/@educationhouse.preschool/video/7635852264942406920', '2026-10-01 05:05:07+00'),
(9, 'testimoni',            NULL, NULL, 'https://www.tiktok.com/@educationhouse.preschool/video/7635852341320633607', '2026-10-01 05:05:26+00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('testimoni_orangtua_id_seq', (SELECT MAX(id) FROM testimoni_orangtua));
