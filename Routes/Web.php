<?php
declare(strict_types=1);

namespace App\Routes;

use App\Controllers\HtmlController;

/**
 * Class Web
 *
 * Routeur minimal expose pour afficher des fragments HTML.
 */
class Web
{
    /**
     * Affiche le menu principal du site.
     *
     * @return void
     */
    public function menu(): void
    {
        $html = (new HtmlController())->menu();
        echo $html;
    }
}

?>
