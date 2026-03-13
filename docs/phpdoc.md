# Documentation PHP et DocBlocks

Ce projet suit une approche PHPDoc :
- docblocks sur classes, proprietes et methodes ;
- generation possible d'une documentation HTML avec `phpDocumentor`.

## Structure attendue des DocBlocks

Les PDF de reference demandent de documenter au minimum :
- les classes ;
- les proprietes avec `@var` ;
- les methodes avec `@param` et `@return` ;
- les exceptions avec `@throws` quand necessaire.

Exemple simple :

```php
/**
 * Class Car
 *
 * This class represents a car.
 */
class Car
{
    /**
     * @var string The make of the car.
     */
    private string $make;

    /**
     * Set the make of the car.
     *
     * @param string $make The make of the car.
     * @return void
     */
    public function setMake(string $make): void
    {
        $this->make = $make;
    }
}
```

## Balises principales

- `@var` : type et role d'une propriete
- `@param` : description d'un parametre
- `@return` : type de retour
- `@throws` : exception possible
- `@package` : package logique si besoin

## Fichiers du projet deja documentes

Les docblocks ont ete poses en priorite sur :
- `Models/Equipe.php`
- `Models/Joueur.php`
- `Models/Championnat.php`
- `Security/Csrf.php`
- `Database/Database.php`

## Installation de phpDocumentor

Option 1 :

```bash
composer require --dev phpdocumentor/phpdocumentor
```

Option 2 :

```bash
wget https://phpdoc.org/phpDocumentor.phar
chmod +x phpDocumentor.phar
sudo mv phpDocumentor.phar /usr/local/bin/phpdoc
```

## Generation de la documentation HTML

Depuis la racine du projet :

```bash
phpdoc -d Controllers,Models,Security,Database,Routes -t docs/api --ignore "Public/*,Views/*,tests/*,docs/api/*"
```

Resultat attendu :
- un dossier `docs/api`
- un `index.html` genere par `phpDocumentor`
