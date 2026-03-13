<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Représente une écurie de Formule 1.
 */
class Equipe
{
    private ?int $id = null;
    private string $nom;
    private string $ville;
    private int $id_championnat;
    private ?string $blason;

    public function __construct(string $nom = '', string $ville = '', int $id_championnat = 0, ?string $blason = null)
    {
        $this->nom = $nom;
        $this->ville = $ville;
        $this->id_championnat = $id_championnat;
        $this->blason = $blason;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getVille(): string { return $this->ville; }
    public function setVille(string $ville): void { $this->ville = $ville; }

    public function getIdChampionnat(): int { return $this->id_championnat; }
    public function setIdChampionnat(int $id): void { $this->id_championnat = $id; }

    public function getBlason(): ?string { return $this->blason; }
    public function setBlason(?string $blason): void { $this->blason = $blason; }
}

?>
