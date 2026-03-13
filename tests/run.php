<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$files = glob(__DIR__ . '/Unit/*Test.php');
sort($files);

foreach ($files as $file) {
    require $file;
}

$total = 0;
$failed = 0;

foreach ($GLOBALS['__tests'] as $testCase) {
    $total++;
    $name = (string)$testCase['name'];
    $fn = $testCase['fn'];

    try {
        $fn();
        echo "[OK]   " . $name . PHP_EOL;
    } catch (Throwable $e) {
        $failed++;
        echo "[FAIL] " . $name . PHP_EOL;
        echo "       " . $e->getMessage() . PHP_EOL;
    }
}

$passed = $total - $failed;
echo PHP_EOL;
echo sprintf("Resultat: %d/%d tests OK", $passed, $total) . PHP_EOL;

exit($failed === 0 ? 0 : 1);
