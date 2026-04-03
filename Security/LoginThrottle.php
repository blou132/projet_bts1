<?php
declare(strict_types=1);

namespace App\Security;

/**
 * Blocage simple anti-bruteforce cote application (par IP).
 */
class LoginThrottle
{
    private const STORE_FILE = __DIR__ . '/../storage/cache/login_throttle.json';
    private const MAX_ATTEMPTS = 5;
    private const FINDTIME_SECONDS = 600; // 10 minutes
    private const BAN_SECONDS = 3600; // 1 heure

    /**
     * @return array{blocked:bool, remaining:int}
     */
    public static function status(string $ip): array
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return ['blocked' => false, 'remaining' => 0];
        }

        $now = time();

        return self::withStore(static function (array &$store) use ($ip, $now): array {
            $entry = self::normalizeEntry($store[$ip] ?? [], $now);
            if ($entry === null) {
                unset($store[$ip]);
                return ['blocked' => false, 'remaining' => 0];
            }

            $store[$ip] = $entry;
            $banUntil = (int)($entry['ban_until'] ?? 0);

            if ($banUntil > $now) {
                return ['blocked' => true, 'remaining' => $banUntil - $now];
            }

            return ['blocked' => false, 'remaining' => 0];
        });
    }

    public static function recordFailure(string $ip): void
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return;
        }

        $now = time();

        self::withStore(static function (array &$store) use ($ip, $now): array {
            $entry = self::normalizeEntry($store[$ip] ?? [], $now) ?? ['fails' => [], 'ban_until' => 0];
            $banUntil = (int)($entry['ban_until'] ?? 0);
            if ($banUntil > $now) {
                $store[$ip] = $entry;
                return [];
            }

            $fails = is_array($entry['fails'] ?? null) ? $entry['fails'] : [];
            $fails[] = $now;

            if (count($fails) >= self::MAX_ATTEMPTS) {
                $store[$ip] = [
                    'fails' => [],
                    'ban_until' => $now + self::BAN_SECONDS,
                ];
                return [];
            }

            $store[$ip] = [
                'fails' => $fails,
                'ban_until' => 0,
            ];
            return [];
        });
    }

    public static function clear(string $ip): void
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return;
        }

        self::withStore(static function (array &$store) use ($ip): array {
            unset($store[$ip]);
            return [];
        });
    }

    public static function clientIp(): string
    {
        $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        if (is_string($forwarded) && $forwarded !== '') {
            $first = trim((string)explode(',', $forwarded)[0]);
            if ($first !== '' && filter_var($first, FILTER_VALIDATE_IP)) {
                return $first;
            }
        }

        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '-';
    }

    /**
     * @param array<string, mixed> $entry
     * @return array{fails:array<int, int>, ban_until:int}|null
     */
    private static function normalizeEntry(array $entry, int $now): ?array
    {
        $fails = [];
        foreach (($entry['fails'] ?? []) as $timestamp) {
            $value = (int)$timestamp;
            if ($value > ($now - self::FINDTIME_SECONDS)) {
                $fails[] = $value;
            }
        }

        $banUntil = (int)($entry['ban_until'] ?? 0);
        if ($banUntil <= $now) {
            $banUntil = 0;
        }

        if ($banUntil === 0 && $fails === []) {
            return null;
        }

        return [
            'fails' => $fails,
            'ban_until' => $banUntil,
        ];
    }

    /**
     * @param callable(array<string, mixed>&):array<string, mixed> $mutator
     * @return array<string, mixed>
     */
    private static function withStore(callable $mutator): array
    {
        $path = self::STORE_FILE;
        $dir = dirname($path);

        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return ['blocked' => false, 'remaining' => 0];
        }

        $handle = @fopen($path, 'c+');
        if (!is_resource($handle)) {
            return ['blocked' => false, 'remaining' => 0];
        }

        @flock($handle, LOCK_EX);
        rewind($handle);
        $raw = stream_get_contents($handle);
        $store = [];
        if (is_string($raw) && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $store = $decoded;
            }
        }

        $result = $mutator($store);

        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
        fflush($handle);
        @flock($handle, LOCK_UN);
        fclose($handle);

        return is_array($result) ? $result : ['blocked' => false, 'remaining' => 0];
    }
}

?>
