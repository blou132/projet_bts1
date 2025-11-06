<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Représente un Grand Prix dans le domaine applicatif.
 */
class Championnat
{
    private ?int $id = null;
    private string $nom;
    private string $pays;
    private ?string $blason;

    public function __construct(string $nom = '', string $pays = '', ?string $blason = null)
    {
        $this->nom = $nom;
        $this->pays = $pays;
        $this->blason = $blason;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getPays(): string { return $this->pays; }
    public function setPays(string $pays): void { $this->pays = $pays; }

    public function getBlason(): ?string { return $this->blason; }
    public function setBlason(?string $blason): void { $this->blason = $blason; }
}

?>
