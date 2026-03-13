<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require dirname(__DIR__) . '/Autoload.php';

$GLOBALS['__tests'] = [];

/**
 * Enregistre un test a executer.
 */
function test(string $name, callable $fn): void
{
    $GLOBALS['__tests'][] = ['name' => $name, 'fn' => $fn];
}

/**
 * Assertion booleenne.
 */
function expectTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

/**
 * Assertion d'egalite stricte.
 */
function expectSame($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        $debug = sprintf(
            "%s\nAttendu: %s\nRecu: %s",
            $message,
            var_export($expected, true),
            var_export($actual, true)
        );
        throw new RuntimeException($debug);
    }
}

/**
 * Assertion de presence d'une sous-chaine.
 */
function expectContains(string $needle, string $haystack, string $message): void
{
    if (!str_contains($haystack, $needle)) {
        throw new RuntimeException($message);
    }
}

/**
 * Assertion d'absence d'une sous-chaine.
 */
function expectNotContains(string $needle, string $haystack, string $message): void
{
    if (str_contains($haystack, $needle)) {
        throw new RuntimeException($message);
    }
}

/**
 * Retourne la racine du projet.
 */
function projectRoot(): string
{
    return dirname(__DIR__);
}

/**
 * Retourne l'URL de base d'un serveur PHP lance pour les tests HTTP.
 *
 * @return string
 */
function testServerBaseUrl(): string
{
    static $server = null;

    if ($server !== null) {
        return $server['base_url'];
    }

    $host = '127.0.0.1';
    $tries = 0;

    while ($tries < 10) {
        $port = random_int(18080, 18999);
        $command = sprintf(
            '%s -S %s:%d router.php',
            escapeshellarg(PHP_BINARY),
            $host,
            $port
        );
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', '/tmp/projet_bts1_test_server.out', 'a'],
            2 => ['file', '/tmp/projet_bts1_test_server.err', 'a'],
        ];
        $process = proc_open($command, $descriptors, $pipes, projectRoot());
        if (!is_resource($process)) {
            throw new RuntimeException('Impossible de lancer le serveur HTTP de test.');
        }

        $started = false;
        for ($i = 0; $i < 30; $i++) {
            $status = proc_get_status($process);
            if (!$status['running']) {
                break;
            }

            $fp = @fsockopen($host, (int)$port, $errno, $errstr, 0.2);
            if (is_resource($fp)) {
                fclose($fp);
                $started = true;
                break;
            }
            usleep(100000);
        }

        if ($started) {
            $server = [
                'process' => $process,
                'base_url' => sprintf('http://%s:%d', $host, $port),
            ];

            register_shutdown_function(static function () use (&$server): void {
                if ($server === null || !is_resource($server['process'])) {
                    return;
                }
                proc_terminate($server['process']);
                proc_close($server['process']);
            });

            return $server['base_url'];
        }

        proc_terminate($process);
        proc_close($process);
        $tries++;
    }

    throw new RuntimeException('Le serveur HTTP de test n a pas pu demarrer.');
}

/**
 * Effectue une requete HTTP simple sur le serveur de test.
 *
 * @param string $method
 * @param string $path
 * @param array<string, string> $data
 * @return array{status:int, body:string, headers:array<int, string>}
 */
function httpRequest(string $method, string $path, array $data = []): array
{
    $url = testServerBaseUrl() . $path;
    $headers = [
        'Content-Type: application/x-www-form-urlencoded',
    ];
    $options = [
        'http' => [
            'method' => strtoupper($method),
            'header' => implode("\r\n", $headers),
            'ignore_errors' => true,
            'follow_location' => 1,
            'max_redirects' => 5,
            'timeout' => 10,
        ],
    ];

    if ($data !== []) {
        $options['http']['content'] = http_build_query($data);
    }

    $context = stream_context_create($options);
    $body = file_get_contents($url, false, $context);
    $responseHeaders = $http_response_header ?? [];
    $status = 0;

    foreach ($responseHeaders as $headerLine) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $headerLine, $matches) === 1) {
            $status = (int)$matches[1];
        }
    }

    return [
        'status' => $status,
        'body' => is_string($body) ? $body : '',
        'headers' => $responseHeaders,
    ];
}
