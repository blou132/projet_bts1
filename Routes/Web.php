<?php
declare(strict_types=1);

namespace App\Routes;

use App\Controllers\HtmlController;

/**
 * Routeur minimal pour exposer des fragments HTML.
 */
class Web
{
    public function menu(): void
    {
        $html = (new HtmlController())->menu();
        echo $html;
    }
}

?>
