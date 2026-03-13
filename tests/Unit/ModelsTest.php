<?php
declare(strict_types=1);

use App\Models\Championnat;
use App\Models\Equipe;
use App\Models\Joueur;

test('Model Equipe: getters/setters fonctionnels', function (): void {
    $equipe = new Equipe('Ferrari', 'Maranello', 1, 'Public/assets/logos/ferrari.svg');
    $equipe->setId(10);
    $equipe->setNom('Alpine');
    $equipe->setVille('Enstone');
    $equipe->setIdChampionnat(2);
    $equipe->setBlason('Public/assets/logos/alpine.svg');

    expectSame(10, $equipe->getId(), 'ID equipe incorrect.');
    expectSame('Alpine', $equipe->getNom(), 'Nom equipe incorrect.');
    expectSame('Enstone', $equipe->getVille(), 'Ville equipe incorrecte.');
    expectSame(2, $equipe->getIdChampionnat(), 'Championnat equipe incorrect.');
    expectSame('Public/assets/logos/alpine.svg', $equipe->getBlason(), 'Blason equipe incorrect.');
});

test('Model Joueur: getters/setters fonctionnels', function (): void {
    $joueur = new Joueur('Hamilton', 'Lewis', 'Pilote titulaire', 1, 'Public/assets/pilotes/hamilton.svg');
    $joueur->setId(44);
    $joueur->setNom('Leclerc');
    $joueur->setPrenom('Charles');
    $joueur->setPoste('Pilote titulaire');
    $joueur->setIdEquipe(9);
    $joueur->setPhoto('Public/assets/pilotes/leclerc.svg');

    expectSame(44, $joueur->getId(), 'ID pilote incorrect.');
    expectSame('Leclerc', $joueur->getNom(), 'Nom pilote incorrect.');
    expectSame('Charles', $joueur->getPrenom(), 'Prenom pilote incorrect.');
    expectSame('Pilote titulaire', $joueur->getPoste(), 'Poste pilote incorrect.');
    expectSame(9, $joueur->getIdEquipe(), 'ID equipe pilote incorrect.');
    expectSame('Public/assets/pilotes/leclerc.svg', $joueur->getPhoto(), 'Photo pilote incorrecte.');
});

test('Model Championnat: getters/setters fonctionnels', function (): void {
    $championnat = new Championnat('Grand Prix de Monaco', 'Monaco', 'Public/assets/grands-prix/monaco.svg');
    $championnat->setId(3);
    $championnat->setNom("Grand Prix d'Australie");
    $championnat->setPays('Australie');
    $championnat->setBlason('Public/assets/grands-prix/australie.svg');

    expectSame(3, $championnat->getId(), 'ID championnat incorrect.');
    expectSame("Grand Prix d'Australie", $championnat->getNom(), 'Nom championnat incorrect.');
    expectSame('Australie', $championnat->getPays(), 'Pays championnat incorrect.');
    expectSame('Public/assets/grands-prix/australie.svg', $championnat->getBlason(), 'Blason championnat incorrect.');
});
