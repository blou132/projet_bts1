<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class Equipe
 *
 * Représente une ecurie de Formule 1 dans le domaine applicatif.
 */
class Equipe
{
    /**
     * @var int|null Identifiant technique de l'ecurie.
     */
    private ?int $id = null;

    /**
     * @var string Nom de l'ecurie.
     */
    private string $nom;

    /**
     * @var string Ville ou pays de rattachement.
     */
    private string $ville;

    /**
     * @var int Identifiant du championnat associe.
     */
    private int $id_championnat;

    /**
     * @var string|null Chemin du logo de l'ecurie.
     */
    private ?string $blason;

    /**
     * Initialise une nouvelle ecurie.
     *
     * @param string $nom Nom de l'ecurie.
     * @param string $ville Ville ou pays associe.
     * @param int $id_championnat Identifiant du championnat.
     * @param string|null $blason Chemin du logo.
     */
    public function __construct(string $nom = '', string $ville = '', int $id_championnat = 0, ?string $blason = null)
    {
        $this->nom = $nom;
        $this->ville = $ville;
        $this->id_championnat = $id_championnat;
        $this->blason = $blason;
    }

    /**
     * Retourne l'identifiant de l'ecurie.
     *
     * @return int|null
     */
    public function getId(): ?int { return $this->id; }

    /**
     * Definit l'identifiant de l'ecurie.
     *
     * @param int|null $id Identifiant technique.
     * @return void
     */
    public function setId(?int $id): void { $this->id = $id; }

    /**
     * Retourne le nom de l'ecurie.
     *
     * @return string
     */
    public function getNom(): string { return $this->nom; }

    /**
     * Definit le nom de l'ecurie.
     *
     * @param string $nom Nom de l'ecurie.
     * @return void
     */
    public function setNom(string $nom): void { $this->nom = $nom; }

    /**
     * Retourne la ville ou le pays associe.
     *
     * @return string
     */
    public function getVille(): string { return $this->ville; }

    /**
     * Definit la ville ou le pays associe.
     *
     * @param string $ville Ville ou pays.
     * @return void
     */
    public function setVille(string $ville): void { $this->ville = $ville; }

    /**
     * Retourne l'identifiant du championnat.
     *
     * @return int
     */
    public function getIdChampionnat(): int { return $this->id_championnat; }

    /**
     * Definit l'identifiant du championnat.
     *
     * @param int $id Identifiant du championnat.
     * @return void
     */
    public function setIdChampionnat(int $id): void { $this->id_championnat = $id; }

    /**
     * Retourne le chemin du logo.
     *
     * @return string|null
     */
    public function getBlason(): ?string { return $this->blason; }

    /**
     * Definit le chemin du logo.
     *
     * @param string|null $blason Chemin du logo.
     * @return void
     */
    public function setBlason(?string $blason): void { $this->blason = $blason; }
}

?>
