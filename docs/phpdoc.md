# Generer la documentation PHP (phpDocumentor)

Ce projet suit une approche PHPDoc (commentaires sur classes/methodes).

## Installation (au choix)

Option 1 (composer, si disponible):

```bash
composer require --dev phpdocumentor/phpdocumentor
```

Option 2 (phar):

```bash
wget https://phpdoc.org/phpDocumentor.phar
chmod +x phpDocumentor.phar
sudo mv phpDocumentor.phar /usr/local/bin/phpdoc
```

## Generation de la documentation

Depuis la racine du projet:

```bash
phpdoc -d Controllers,Models,Security,Database,Routes -t docs/api --ignore "Public/*,Views/*,tests/*,docs/api/*"
```

Sortie attendue:
- dossier `docs/api` cree
- index HTML de la documentation genere
