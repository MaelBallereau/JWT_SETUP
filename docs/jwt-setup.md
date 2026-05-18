# Installation OpenSSL & Génération des clés JWT

## 1. Ajouter OpenSSL au PATH (Windows)

OpenSSL est fourni avec Git for Windows. Il faut l'ajouter aux variables d'environnement.

### Via l'interface Windows

1. Ouvrir **Paramètres** → **Système** → **Informations système** → **Paramètres système avancés**
2. Cliquer sur **Variables d'environnement**
3. Dans la section **Variables système**, sélectionner `Path` puis **Modifier**
4. Cliquer sur **Nouveau** et ajouter :
   ```
   C:\Program Files\Git\mingw64\bin
   ```
5. Valider avec **OK** sur toutes les fenêtres

### Via PowerShell (administrateur)

```powershell
[System.Environment]::SetEnvironmentVariable(
    "Path",
    [System.Environment]::GetEnvironmentVariable("Path", "Machine") + ";C:\Program Files\Git\mingw64\bin",
    "Machine"
)
```

### Vérifier l'installation

Ouvrir un **nouveau** terminal et lancer :

```powershell
openssl version
```

Résultat attendu : `OpenSSL 3.x.x ...`

---

## 2. Générer les clés JWT

### Méthode 1 — Script PHP (recommandé)

Le projet inclut un script `generate-jwt-keys.php` à la racine.

```powershell
php generate-jwt-keys.php
```

Les clés sont créées dans `config/jwt/` :
- `config/jwt/private.pem`
- `config/jwt/public.pem`

### Méthode 2 — Commande OpenSSL manuelle

```powershell
# Créer le dossier si nécessaire
mkdir config\jwt -Force

# Générer la clé privée (passphrase = valeur de JWT_PASSPHRASE dans .env)
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096

# Extraire la clé publique
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

---

## 3. Configuration `.env`

Les variables suivantes doivent être présentes dans `.env` (déjà configurées) :

```dotenv
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=mael
```

> **Important :** `JWT_PASSPHRASE` doit correspondre à la passphrase utilisée lors de la génération de la clé privée.

---

## 4. Vérifier que tout fonctionne

```powershell
php bin/console lexik:jwt:generate-token test@example.com
```

Un token JWT doit s'afficher dans la console.
