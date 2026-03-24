<?php
declare(strict_types=1);

namespace App\Security;

/**
 * Journalise les evenements securite utilises par fail2ban.
 */
class SecurityLogger
{
    private const LOG_FILE = __DIR__ . '/../storage/logs/security.log';

    public static function authFail(string $email = '', string $reason = 'invalid_credentials'): void
    {
        self::write('AUTH_FAIL', [
            'ip' => self::clientIp(),
            'email' => $email,
            'reason' => $reason,
            'uri' => (string)($_SERVER['REQUEST_URI'] ?? '-'),
        ]);
    }

    /**
     * @param array<string, string> $fields
     */
    private static function write(string $event, array $fields): void
    {
        $parts = [date('c'), $event];

        foreach ($fields as $key => $value) {
            $parts[] = $key . '=' . self::sanitizeValue($value);
        }

        $line = implode(' ', $parts) . PHP_EOL;
        $dir = dirname(self::LOG_FILE);

        try {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            @file_put_contents(self::LOG_FILE, $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            // Ne jamais casser l'authentification a cause du logging.
        }
    }

    private static function clientIp(): string
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

    private static function sanitizeValue(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '-';
        }

        $value = preg_replace('/\s+/u', '_', $value);
        $value = preg_replace('/[^A-Za-z0-9@._:\/-]/', '', (string)$value);

        if (!is_string($value) || $value === '') {
            return '-';
        }

        return substr($value, 0, 200);
    }
}

?>
