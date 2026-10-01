-- ============================================================
-- MIGRATION 06: Hapus duplikat di berita_gambar
-- Jalankan di Supabase SQL Editor
-- ============================================================

-- Hapus baris duplikat, pertahankan hanya 1 per berita_id + url
DELETE FROM berita_gambar
WHERE id NOT IN (
    SELECT MIN(id)
    FROM berita_gambar
    GROUP BY berita_id, url
);

-- Verifikasi: semua berita sekarang tidak ada duplikat
SELECT berita_id, COUNT(*) as total, array_agg(url) as urls
FROM berita_gambar
GROUP BY berita_id
ORDER BY berita_id;
