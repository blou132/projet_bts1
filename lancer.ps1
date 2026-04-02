param(
    [string]$MysqlRootUser = 'root',
    [string]$MysqlRootPassword = '',
    [switch]$RunTests,
    [switch]$NoServer
)

$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $PSScriptRoot

function Resolve-ToolPath {
    param(
        [Parameter(Mandatory = $true)][string]$CommandName,
        [Parameter(Mandatory = $true)][string]$FallbackPath,
        [Parameter(Mandatory = $true)][string]$Label
    )

    $command = Get-Command $CommandName -CommandType Application -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($null -ne $command) {
        return $command.Source
    }

    if (Test-Path -LiteralPath $FallbackPath) {
        return $FallbackPath
    }

    throw "Erreur: $Label introuvable. Installe XAMPP ou ajoute $CommandName au PATH."
}

function Get-EnvValue {
    param(
        [hashtable]$Map,
        [string]$Key,
        [string]$Default
    )

    if ($Map.ContainsKey($Key) -and $Map[$Key] -ne '') {
        return [string]$Map[$Key]
    }
    return $Default
}

function Escape-SqlLiteral {
    param([string]$Value)
    return $Value -replace "'", "''"
}

$phpExe = Resolve-ToolPath -CommandName 'php' -FallbackPath 'C:\xampp\php\php.exe' -Label 'PHP'
$mysqlExe = Resolve-ToolPath -CommandName 'mysql' -FallbackPath 'C:\xampp\mysql\bin\mysql.exe' -Label 'MySQL'

if (-not (Test-Path -LiteralPath '.env')) {
    if (-not (Test-Path -LiteralPath '.env.example')) {
        throw "Erreur: .env et .env.example introuvables."
    }
    Copy-Item -LiteralPath '.env.example' -Destination '.env'
    Write-Host ".env cree a partir de .env.example"
}

$envMap = @{}
Get-Content -LiteralPath '.env' | ForEach-Object {
    $line = $_.Trim()
    if ($line -eq '' -or $line.StartsWith('#') -or -not $line.Contains('=')) {
        return
    }

    $parts = $line -split '=', 2
    $key = $parts[0].Trim()
    $value = $parts[1].Trim().Trim('"').Trim("'")

    if ($key -ne '') {
        $envMap[$key] = $value
    }
}

$dbHost = Get-EnvValue -Map $envMap -Key 'DB_HOST' -Default '127.0.0.1'
$dbPort = Get-EnvValue -Map $envMap -Key 'DB_PORT' -Default '3306'
$dbName = Get-EnvValue -Map $envMap -Key 'DB_NAME' -Default 'tpformula1'
$dbUser = Get-EnvValue -Map $envMap -Key 'DB_USER' -Default 'tpformula1'
$dbPass = Get-EnvValue -Map $envMap -Key 'DB_PASS' -Default 'change-me'

if ($dbName -notmatch '^[A-Za-z0-9_]+$') {
    throw "Erreur: DB_NAME invalide ($dbName). Utilise seulement lettres/chiffres/_ pour le script Windows."
}

$dbUserEscaped = Escape-SqlLiteral -Value $dbUser
$dbPassEscaped = Escape-SqlLiteral -Value $dbPass

$sql = @"
CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$dbUserEscaped'@'localhost' IDENTIFIED BY '$dbPassEscaped';
CREATE USER IF NOT EXISTS '$dbUserEscaped'@'127.0.0.1' IDENTIFIED BY '$dbPassEscaped';
ALTER USER '$dbUserEscaped'@'localhost' IDENTIFIED BY '$dbPassEscaped';
ALTER USER '$dbUserEscaped'@'127.0.0.1' IDENTIFIED BY '$dbPassEscaped';
GRANT ALL PRIVILEGES ON $dbName.* TO '$dbUserEscaped'@'localhost';
GRANT ALL PRIVILEGES ON $dbName.* TO '$dbUserEscaped'@'127.0.0.1';
FLUSH PRIVILEGES;
"@

Write-Host "Configuration MySQL (base + utilisateur)..."
$mysqlArgs = @('-u', $MysqlRootUser)
if ($MysqlRootPassword -ne '') {
    $mysqlArgs += "-p$MysqlRootPassword"
}
$mysqlArgs += @('-e', $sql)
& $mysqlExe @mysqlArgs

Write-Host "Initialisation des donnees..."
& $phpExe '.\init_db.php'

if ($RunTests) {
    Write-Host "Execution des tests..."
    & $phpExe '.\tests\run.php'
} else {
    Write-Host "Tests ignores. Utilise -RunTests pour les lancer."
}

Write-Host ''
Write-Host "Projet pret."
Write-Host "- URL: http://localhost:8000/accueil"
Write-Host "- DB: $dbHost`:$dbPort / $dbName / $dbUser"
Write-Host ''

if ($NoServer) {
    return
}

Write-Host "Demarrage du serveur PHP..."
& $phpExe '-S' 'localhost:8000' 'router.php'
