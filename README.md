# Pixel Perfect Back-End

Pixel Perfect Back-End is an API for an e-commerce site called Pixel Perfect.

Requirements:
- PHP 8.1.0 or higher
- Symfony 6.3 or higher
- MySql server

## Installation instructions

```bash
git clone https://github.com/INCUBATEUR-M2i-AFPA/pixel-perfect-backend.git
cd pixel-perfect-backend/
composer install --ignore-platform-req=ext-sodium
```

After that you'll need to add a new file called `.env.local`, it's a copy of `.env` but just for you.

Don't forget to add your login and database name to `.env.local`.

Then you need to create a private and public key for jwt :

```bash
cd config/
mkdir jwt
cd ..
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
# enter a passphrase
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
# enter same passphrase
```

Use the same passphrase for the private and public keys and add them to .env.local in this section:

```
### lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=YOURPASSPHRASE  
###< lexik/jwt-authentication-bundle ###
```

## Database installation

```bash
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Add data in Database

Specific application was made to generate product on database. You need download folder `Generateur de produit` it is on folder `Incubateur>Sprint 0`.

De-zip this folder where you want. Rename file `.example.config.json` to `.config.json`.And complete json file :

```json
{
    "server": "serverName", # your server name generaly localhost
    "database": "databaseName", # name of database for pixelPerfect
    "user": "User", # username of Mysql generaly root
    "password": "Password", # password of MySql user generaly it's empty
    "port": 3306, # port of MySql server generaly 3306
    "timeout": "30" # timeout if connexion database is long
}
```

After this don't forget to save and run :
- `scriptPixelPerfect-windows.exe` => Windows 
- `scriptPixelPerfect-darwin` => MacOs
- `scriptPixelPerfect-linux` => all linux Os

If you need help for installation contact Eric Sergueev

## How to use

Before running the project, you need to open your MySql server, launch an application like XAMPP or WAMP.

```bash
symfony server:start
```