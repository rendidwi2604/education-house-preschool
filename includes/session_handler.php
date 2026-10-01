<?php
/**
 * Database Session Handler untuk Vercel + Supabase
 *
 * PHP default menyimpan session di file /tmp — di Vercel setiap
 * function invocation punya /tmp sendiri sehingga session hilang.
 * Handler ini menyimpan session ke tabel php_sessions di Supabase
 * agar persistent antar request.
 *
 * Cara pakai: include file ini SEBELUM session_start()
 */

function db_session_start(PDO $pdo): void
{
    // Konfigurasi session
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', '7200');  // 2 jam
    ini_set('session.cookie_lifetime', '0');     // sampai browser ditutup

    $handler = new class($pdo) implements SessionHandlerInterface {

        private PDO $pdo;

        public function __construct(PDO $pdo) {
            $this->pdo = $pdo;
        }

        public function open(string $path, string $name): bool {
            return true;
        }

        public function close(): bool {
            return true;
        }

        public function read(string $id): string|false {
            try {
                $stmt = $this->pdo->prepare(
                    "SELECT data FROM php_sessions WHERE id = ? AND updated_at > NOW() - INTERVAL '2 hours'"
                );
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? (string) $row['data'] : '';
            } catch (Exception $e) {
                error_log('[SESSION READ] ' . $e->getMessage());
                return '';
            }
        }

        public function write(string $id, string $data): bool {
            try {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO php_sessions (id, data, updated_at)
                     VALUES (?, ?, NOW())
                     ON CONFLICT (id) DO UPDATE
                     SET data = EXCLUDED.data, updated_at = NOW()"
                );
                $stmt->execute([$id, $data]);
                return true;
            } catch (Exception $e) {
                error_log('[SESSION WRITE] ' . $e->getMessage());
                return false;
            }
        }

        public function destroy(string $id): bool {
            try {
                $this->pdo->prepare("DELETE FROM php_sessions WHERE id = ?")
                          ->execute([$id]);
                return true;
            } catch (Exception $e) {
                error_log('[SESSION DESTROY] ' . $e->getMessage());
                return false;
            }
        }

        public function gc(int $max_lifetime): int|false {
            try {
                $stmt = $this->pdo->prepare(
                    "DELETE FROM php_sessions WHERE updated_at < NOW() - INTERVAL '2 hours'"
                );
                $stmt->execute();
                return $stmt->rowCount();
            } catch (Exception $e) {
                error_log('[SESSION GC] ' . $e->getMessage());
                return false;
            }
        }
    };

    session_set_save_handler($handler, true);
    session_start();
}
