<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Représente un pilote de Formule 1.
 */
class Joueur
{
    private ?int $id = null;
    private string $nom;
    private string $prenom;
    private string $poste;
    private int $id_equipe;
    private ?string $photo;

    public function __construct(string $nom = '', string $prenom = '', string $poste = '', int $id_equipe = 0, ?string $photo = null)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->poste = $poste;
        $this->id_equipe = $id_equipe;
        $this->photo = $photo;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }

    public function getPoste(): string { return $this->poste; }
    public function setPoste(string $poste): void { $this->poste = $poste; }

    public function getIdEquipe(): int { return $this->id_equipe; }
    public function setIdEquipe(int $id): void { $this->id_equipe = $id; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): void { $this->photo = $photo; }
}

?>
