# Fail2ban (protection brute force login)

Ce projet ecrit les echecs de connexion dans :

- `storage/logs/security.log`

Format de log (exemple) :

```text
2026-03-24T18:20:10+01:00 AUTH_FAIL ip=203.0.113.10 email=test@example.com reason=invalid_credentials uri=/auth/authenticate
```

## Fichiers fournis dans le projet

- filtre : `ops/fail2ban/filter.d/tpformula1-auth.conf`
- jail : `ops/fail2ban/jail.d/tpformula1-auth.local`

## Installation serveur (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install -y fail2ban
```

Copier la configuration :

```bash
sudo cp ops/fail2ban/filter.d/tpformula1-auth.conf /etc/fail2ban/filter.d/
sudo cp ops/fail2ban/jail.d/tpformula1-auth.local /etc/fail2ban/jail.d/
```

Adapter `logpath` si ton projet n'est pas dans `/home/lin/Bureau/projet_bts1`.

Pour test local (`127.0.0.1`), laisser ces options dans la jail :

```ini
ignoreip =
ignoreself = false
```

Sinon fail2ban peut ignorer tes tentatives locales.

Redemarrer fail2ban :

```bash
sudo systemctl restart fail2ban
sudo systemctl enable fail2ban
```

Verifier :

```bash
sudo fail2ban-client status
sudo fail2ban-client status tpformula1-auth
```

Test rapide (attendu : ban apres 5 erreurs) :

```bash
# 1) redemarrer/recharger
sudo systemctl restart fail2ban
sleep 2
sudo fail2ban-client status tpformula1-auth

# 2) tenter 5 mauvais logins sur /auth/authenticate

# 3) verifier les IP bannies
sudo fail2ban-client status tpformula1-auth
```

## Regles actives

- `maxretry = 5` tentatives ratees
- `findtime = 10m`
- `bantime = 1h`

Tu peux durcir facilement en baissant `maxretry` et augmentant `bantime`.
