# Documentation PHP (DocBlocks + Doxygen)

Ce projet utilise des DocBlocks PHP et une generation HTML avec `doxygen`.

## Regles DocBlock (attendues)

Les elements minimaux a documenter :
- classes
- proprietes avec `@var`
- methodes avec `@param` et `@return`
- exceptions avec `@throws` si necessaire

Exemple :

```php
/**
 * Classe de service exemple.
 */
class ExampleService
{
    /** @var string Nom courant */
    private string $name = '';

    /**
     * Met a jour le nom.
     *
     * @param string $name Nouveau nom
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```

## Fichiers documentes en priorite

- `Models/Equipe.php`
- `Models/Joueur.php`
- `Models/Championnat.php`
- `Security/Csrf.php`
- `Database/Database.php`

## Generation avec Doxygen

Configuration deja fournie a la racine : `Doxyfile`

Commande :

```bash
doxygen Doxyfile
```

Resultat :
- dossier `docs/doxygen/html`
- page d'entree `docs/doxygen/html/index.html`
- URL locale : `http://localhost:8000/docs/doxygen/html/index.html`

## Option alternative (phpDocumentor)

Si besoin, la generation phpDocumentor reste possible :

```bash
phpdoc -d Controllers,Models,Security,Database,Routes -t docs/api --ignore "Public/*,Views/*,tests/*,docs/api/*"
```
