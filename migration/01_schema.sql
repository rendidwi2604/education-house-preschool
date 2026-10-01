-- ============================================================
-- MIGRATION 01: Schema PostgreSQL untuk Education House Preschool
-- Target: Supabase (PostgreSQL 15+)
--
-- Cara pakai:
--   Supabase Dashboard → SQL Editor → New Query → paste → Run
--   Jalankan file ini PERTAMA sebelum 02 dan 03
-- ============================================================

-- ── 1. admin ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admin (
  id          SERIAL PRIMARY KEY,
  username    VARCHAR(50)  NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,
  nama        VARCHAR(100) NOT NULL,
  foto        VARCHAR(255) DEFAULT NULL,
  created_at  TIMESTAMPTZ  DEFAULT NOW()
);

-- ── 2. berita ───────────────────────────────────────────────
DO $$ BEGIN
  CREATE TYPE berita_status AS ENUM ('draft', 'terbit');
EXCEPTION WHEN duplicate_object THEN NULL;
END $$;

CREATE TABLE IF NOT EXISTS berita (
  id         SERIAL PRIMARY KEY,
  judul      VARCHAR(200) NOT NULL,
  isi        TEXT         NOT NULL,
  kategori   VARCHAR(50)  DEFAULT 'Pengumuman',
  gambar     VARCHAR(255) DEFAULT NULL,
  status     berita_status DEFAULT 'terbit',
  created_at TIMESTAMPTZ  DEFAULT NOW()
);

-- ── 3. galeri ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS galeri (
  id          SERIAL PRIMARY KEY,
  keterangan  VARCHAR(200) DEFAULT NULL,
  gambar      VARCHAR(255) NOT NULL,
  created_at  TIMESTAMPTZ  DEFAULT NOW()
);

-- ── 4. guru ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS guru (
  id          SERIAL PRIMARY KEY,
  nama        VARCHAR(100) NOT NULL,
  jabatan     VARCHAR(100) DEFAULT NULL,
  foto        VARCHAR(255) DEFAULT NULL,
  bio         TEXT         DEFAULT NULL,
  bidang      VARCHAR(150) DEFAULT NULL,
  pendidikan  VARCHAR(150) DEFAULT NULL,
  pengalaman  VARCHAR(150) DEFAULT NULL,
  created_at  TIMESTAMPTZ  DEFAULT NOW()
);

-- ── 5. instagram_posts ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS instagram_posts (
  id         SERIAL PRIMARY KEY,
  post_url   VARCHAR(500) NOT NULL UNIQUE,
  created_at TIMESTAMPTZ  DEFAULT NOW()
);

-- ── 6. instagram_setting ────────────────────────────────────
CREATE TABLE IF NOT EXISTS instagram_setting (
  id         SMALLINT PRIMARY KEY,
  post_url   VARCHAR(500) DEFAULT NULL,
  updated_at TIMESTAMPTZ  DEFAULT NOW()
);

-- Trigger: otomatis update updated_at saat UPDATE
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER LANGUAGE plpgsql AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_instagram_setting_updated_at ON instagram_setting;
CREATE TRIGGER trg_instagram_setting_updated_at
  BEFORE UPDATE ON instagram_setting
  FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- ── 7. kegiatan_islami ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS kegiatan_islami (
  id            SERIAL PRIMARY KEY,
  judul         VARCHAR(150) NOT NULL,
  keterangan    VARCHAR(500) DEFAULT NULL,
  gambar        VARCHAR(255) DEFAULT NULL,
  instagram_url VARCHAR(500) DEFAULT NULL,
  created_at    TIMESTAMPTZ  DEFAULT NOW()
);

-- ── 8. pendaftar ────────────────────────────────────────────
DO $$ BEGIN
  CREATE TYPE pendaftar_status AS ENUM ('Baru', 'Diterima', 'Ditolak');
EXCEPTION WHEN duplicate_object THEN NULL;
END $$;

CREATE TABLE IF NOT EXISTS pendaftar (
  id         SERIAL PRIMARY KEY,
  nama_anak  VARCHAR(100) NOT NULL,
  nama_ortu  VARCHAR(100) NOT NULL,
  alamat     TEXT         NOT NULL,
  whatsapp   VARCHAR(20)  NOT NULL,
  usia_anak  INTEGER      DEFAULT NULL,
  status     pendaftar_status DEFAULT 'Baru',
  created_at TIMESTAMPTZ  DEFAULT NOW()
);

-- ── 9. slider ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS slider (
  id         SERIAL PRIMARY KEY,
  judul      VARCHAR(200) DEFAULT NULL,
  subjudul   VARCHAR(300) DEFAULT NULL,
  gambar     VARCHAR(255) NOT NULL,
  urutan     INTEGER      DEFAULT 0,
  aktif      BOOLEAN      DEFAULT TRUE,
  created_at TIMESTAMPTZ  DEFAULT NOW()
);

-- ── 10. testimoni_orangtua ──────────────────────────────────
CREATE TABLE IF NOT EXISTS testimoni_orangtua (
  id            SERIAL PRIMARY KEY,
  keterangan    VARCHAR(500) DEFAULT NULL,
  video         VARCHAR(255) DEFAULT NULL,
  instagram_url VARCHAR(500) DEFAULT NULL,
  tiktok_url    VARCHAR(500) DEFAULT NULL,
  created_at    TIMESTAMPTZ  DEFAULT NOW()
);

-- ── Indexes untuk performa query ────────────────────────────
CREATE INDEX IF NOT EXISTS idx_berita_status_kategori ON berita (status, kategori);
CREATE INDEX IF NOT EXISTS idx_berita_created_at      ON berita (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_galeri_created_at      ON galeri (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_guru_created_at        ON guru   (created_at ASC);
CREATE INDEX IF NOT EXISTS idx_slider_aktif_urutan    ON slider (aktif, urutan ASC);
CREATE INDEX IF NOT EXISTS idx_instagram_posts_created ON instagram_posts (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_kegiatan_created_at    ON kegiatan_islami (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_testimoni_created_at   ON testimoni_orangtua (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_pendaftar_created_at   ON pendaftar (created_at DESC);
