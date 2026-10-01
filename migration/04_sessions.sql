-- ============================================================
-- MIGRATION 04: Tabel untuk PHP Session Handler
-- Jalankan di Supabase SQL Editor
-- ============================================================

CREATE TABLE IF NOT EXISTS php_sessions (
  id         VARCHAR(128) PRIMARY KEY,
  data       TEXT         NOT NULL DEFAULT '',
  updated_at TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_php_sessions_updated ON php_sessions (updated_at);

-- Nonaktifkan RLS
ALTER TABLE php_sessions DISABLE ROW LEVEL SECURITY;
