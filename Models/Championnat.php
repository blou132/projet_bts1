<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class Championnat
 *
 * Represente un Grand Prix dans le domaine applicatif.
 */
class Championnat
{
    /**
     * @var int|null Identifiant du Grand Prix.
     */
    private ?int $id = null;

    /**
     * @var string Nom du Grand Prix.
     */
    private string $nom;

    /**
     * @var string Pays de la manche.
     */
    private string $pays;

    /**
     * @var string|null Chemin du visuel associe.
     */
    private ?string $blason;

    /**
     * Initialise un nouveau Grand Prix.
     *
     * @param string $nom Nom du Grand Prix.
     * @param string $pays Pays de la manche.
     * @param string|null $blason Chemin du visuel.
     */
    public function __construct(string $nom = '', string $pays = '', ?string $blason = null)
    {
        $this->nom = $nom;
        $this->pays = $pays;
        $this->blason = $blason;
    }

    /**
     * Retourne l'identifiant du Grand Prix.
     *
     * @return int|null
     */
    public function getId(): ?int { return $this->id; }

    /**
     * Definit l'identifiant du Grand Prix.
     *
     * @param int|null $id Identifiant technique.
     * @return void
     */
    public function setId(?int $id): void { $this->id = $id; }

    /**
     * Retourne le nom du Grand Prix.
     *
     * @return string
     */
    public function getNom(): string { return $this->nom; }

    /**
     * Definit le nom du Grand Prix.
     *
     * @param string $nom Nom du Grand Prix.
     * @return void
     */
    public function setNom(string $nom): void { $this->nom = $nom; }

    /**
     * Retourne le pays du Grand Prix.
     *
     * @return string
     */
    public function getPays(): string { return $this->pays; }

    /**
     * Definit le pays du Grand Prix.
     *
     * @param string $pays Pays de la manche.
     * @return void
     */
    public function setPays(string $pays): void { $this->pays = $pays; }

    /**
     * Retourne le chemin du visuel.
     *
     * @return string|null
     */
    public function getBlason(): ?string { return $this->blason; }

    /**
     * Definit le chemin du visuel.
     *
     * @param string|null $blason Chemin du visuel.
     * @return void
     */
    public function setBlason(?string $blason): void { $this->blason = $blason; }
}

?>
