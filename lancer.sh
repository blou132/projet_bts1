#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

if ! command -v php >/dev/null 2>&1; then
  echo "Erreur: PHP n'est pas installe."
  exit 1
fi

if ! command -v mysql >/dev/null 2>&1; then
  echo "Erreur: client MySQL non trouve (commande mysql)."
  exit 1
fi

if [ ! -f ".env" ]; then
  cp .env.example .env
  echo ".env cree a partir de .env.example"
fi

set -a
. ./.env
set +a

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_NAME="${DB_NAME:-tpformula1}"
DB_USER="${DB_USER:-tpformula1}"
DB_PASS="${DB_PASS:-change-me}"

sql_escape() {
  printf "%s" "$1" | sed "s/'/''/g"
}

db_name_escaped="$(printf "%s" "$DB_NAME" | sed 's/`/``/g')"
db_user_escaped="$(sql_escape "$DB_USER")"
db_pass_escaped="$(sql_escape "$DB_PASS")"

mysql_sql=$(cat <<SQL
CREATE DATABASE IF NOT EXISTS \`$db_name_escaped\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$db_user_escaped'@'localhost' IDENTIFIED BY '$db_pass_escaped';
CREATE USER IF NOT EXISTS '$db_user_escaped'@'127.0.0.1' IDENTIFIED BY '$db_pass_escaped';
ALTER USER '$db_user_escaped'@'localhost' IDENTIFIED BY '$db_pass_escaped';
ALTER USER '$db_user_escaped'@'127.0.0.1' IDENTIFIED BY '$db_pass_escaped';
GRANT ALL PRIVILEGES ON \`$db_name_escaped\`.* TO '$db_user_escaped'@'localhost';
GRANT ALL PRIVILEGES ON \`$db_name_escaped\`.* TO '$db_user_escaped'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL
)

echo "Configuration MySQL (base + utilisateur)..."
if command -v sudo >/dev/null 2>&1; then
  if ! sudo mysql -e "$mysql_sql"; then
    echo "Echec via sudo mysql. Tentative via mysql -u root -p..."
    mysql -u root -p -e "$mysql_sql"
  fi
else
  mysql -u root -p -e "$mysql_sql"
fi

echo "Initialisation des donnees..."
php init_db.php

echo "Execution des tests..."
php tests/run.php

echo
echo "Projet pret."
echo "- URL: http://localhost:8000/accueil"
echo "- DB: $DB_HOST:$DB_PORT / $DB_NAME / $DB_USER"
echo
echo "Demarrage du serveur PHP..."
php -S localhost:8000 router.php
