<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class Joueur
 *
 * Represente un pilote de Formule 1.
 */
class Joueur
{
    /**
     * @var int|null Identifiant technique du pilote.
     */
    private ?int $id = null;

    /**
     * @var string Nom du pilote.
     */
    private string $nom;

    /**
     * @var string Prenom du pilote.
     */
    private string $prenom;

    /**
     * @var string Role du pilote dans l'ecurie.
     */
    private string $poste;

    /**
     * @var int Identifiant de l'ecurie associee.
     */
    private int $id_equipe;

    /**
     * @var string|null Chemin du portrait du pilote.
     */
    private ?string $photo;

    /**
     * Initialise un nouveau pilote.
     *
     * @param string $nom Nom du pilote.
     * @param string $prenom Prenom du pilote.
     * @param string $poste Poste du pilote.
     * @param int $id_equipe Identifiant de l'ecurie.
     * @param string|null $photo Chemin du portrait.
     */
    public function __construct(string $nom = '', string $prenom = '', string $poste = '', int $id_equipe = 0, ?string $photo = null)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->poste = $poste;
        $this->id_equipe = $id_equipe;
        $this->photo = $photo;
    }

    /**
     * Retourne l'identifiant du pilote.
     *
     * @return int|null
     */
    public function getId(): ?int { return $this->id; }

    /**
     * Definit l'identifiant du pilote.
     *
     * @param int|null $id Identifiant technique.
     * @return void
     */
    public function setId(?int $id): void { $this->id = $id; }

    /**
     * Retourne le nom du pilote.
     *
     * @return string
     */
    public function getNom(): string { return $this->nom; }

    /**
     * Definit le nom du pilote.
     *
     * @param string $nom Nom du pilote.
     * @return void
     */
    public function setNom(string $nom): void { $this->nom = $nom; }

    /**
     * Retourne le prenom du pilote.
     *
     * @return string
     */
    public function getPrenom(): string { return $this->prenom; }

    /**
     * Definit le prenom du pilote.
     *
     * @param string $prenom Prenom du pilote.
     * @return void
     */
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }

    /**
     * Retourne le poste du pilote.
     *
     * @return string
     */
    public function getPoste(): string { return $this->poste; }

    /**
     * Definit le poste du pilote.
     *
     * @param string $poste Poste du pilote.
     * @return void
     */
    public function setPoste(string $poste): void { $this->poste = $poste; }

    /**
     * Retourne l'identifiant de l'ecurie.
     *
     * @return int
     */
    public function getIdEquipe(): int { return $this->id_equipe; }

    /**
     * Definit l'identifiant de l'ecurie.
     *
     * @param int $id Identifiant de l'ecurie.
     * @return void
     */
    public function setIdEquipe(int $id): void { $this->id_equipe = $id; }

    /**
     * Retourne le chemin du portrait.
     *
     * @return string|null
     */
    public function getPhoto(): ?string { return $this->photo; }

    /**
     * Definit le chemin du portrait.
     *
     * @param string|null $photo Chemin du portrait.
     * @return void
     */
    public function setPhoto(?string $photo): void { $this->photo = $photo; }
}

?>
