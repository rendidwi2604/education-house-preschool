-- ============================================================
-- MIGRATION 05: Tabel berita_gambar (multi-gambar per berita)
-- Jalankan di Supabase SQL Editor
-- ============================================================

CREATE TABLE IF NOT EXISTS berita_gambar (
  id         SERIAL PRIMARY KEY,
  berita_id  INTEGER      NOT NULL REFERENCES berita(id) ON DELETE CASCADE,
  url        VARCHAR(500) NOT NULL,   -- URL Supabase Storage atau path lokal
  urutan     INTEGER      DEFAULT 0,
  created_at TIMESTAMPTZ  DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_berita_gambar_berita_id ON berita_gambar (berita_id, urutan ASC);

ALTER TABLE berita_gambar DISABLE ROW LEVEL SECURITY;

-- Migrasi data lama: pindahkan gambar lama dari kolom berita.gambar ke tabel baru
INSERT INTO berita_gambar (berita_id, url, urutan, created_at)
SELECT id,
       CASE
         WHEN gambar LIKE 'https://%' THEN gambar
         ELSE 'assets/uploads/galeri/' || gambar
       END,
       0,
       created_at
FROM berita
WHERE gambar IS NOT NULL AND gambar <> ''
ON CONFLICT DO NOTHING;
