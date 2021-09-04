[![Codacy Badge](https://app.codacy.com/project/badge/Grade/bbdb028b57f5462b858d4dd6c8b8d09f)](https://www.codacy.com/gh/donjmi/Jimmy/dashboard?utm_source=github.com&utm_medium=referral&utm_content=donjmi/Jimmy&utm_campaign=Badge_Grade)
[![Test Coverage](https://api.codeclimate.com/v1/badges/d8890e6d4bf1040a5428/test_coverage)](https://codeclimate.com/github/donjmi/Jimmy/test_coverage)

#**SnowTricks**

Projet 6 du parcours PHP / Symfony sur OpenClassrooms - Développez de A à Z le site communautaire SnowTricks

**Configuration:**

- Symfony 5.3
- Composer 2.0.8
- Bootstrap 4.5.0
- jQuery 3.5.1
- WampServer 3.2.3 (php 7.4.9)

**Installation**

1. Clonez ou telechargez le repository.
   https://github.com/donjmi/Jimmy.git

2. Modifiez le fichier .env (ou créer un env.local) avec vos parametres de BDD et d'email.

3. Composer install -> pour installer toutes les dependances.

4. Lancer la commande pour installer/configurer/navihuer sur le projet : composer prepare
   php bin/console doctrine:database:drop --if-exists --force
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force
   php bin/console doctrine:fixtures:load -n

Connectez vous sur le site:
admin: email+1@snowtrick.com pass: 000000
user: email+2@snowtrick.com pass: 000000
