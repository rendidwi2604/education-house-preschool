-- ============================================================
-- MIGRATION 03: Nonaktifkan Row Level Security (RLS)
-- Target: Supabase (PostgreSQL 15+)
--
-- PENTING: Jalankan SETELAH 01_schema.sql
--
-- Karena project ini PHP native yang koneksi langsung ke DB
-- via password (bukan Supabase JS SDK / anon key), RLS perlu
-- dinonaktifkan agar query dari PHP bisa berjalan normal.
-- ============================================================

ALTER TABLE admin               DISABLE ROW LEVEL SECURITY;
ALTER TABLE berita              DISABLE ROW LEVEL SECURITY;
ALTER TABLE galeri              DISABLE ROW LEVEL SECURITY;
ALTER TABLE guru                DISABLE ROW LEVEL SECURITY;
ALTER TABLE instagram_posts     DISABLE ROW LEVEL SECURITY;
ALTER TABLE instagram_setting   DISABLE ROW LEVEL SECURITY;
ALTER TABLE kegiatan_islami     DISABLE ROW LEVEL SECURITY;
ALTER TABLE pendaftar           DISABLE ROW LEVEL SECURITY;
ALTER TABLE slider              DISABLE ROW LEVEL SECURITY;
ALTER TABLE testimoni_orangtua  DISABLE ROW LEVEL SECURITY;
