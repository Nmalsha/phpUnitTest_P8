# ToDoList

## Project 8 - Parcours développeur d'application PHP/Symfony

### Environnement de développement

    -Linux
    -Composer 2.3.7
    -PHP 7.4.3
    -Symfony 5.4
    -Apache2
    -MySQL
    -git

### Instalation

1.Clonez le repository Github

    git clone https://github.com/Nmalsha/phpUnitTest_P8.git

2. Configure BDD connect on .env file

3.Créer la BDD

    composer install

4.Créer la BDD

    php bin/console doctrine:database:create

5.Créer les tables

    php bin/console doctrine:schema:create

6.Installer la Fixture (démo de données fictives)

        php bin/console doctrine:fixture:load

7.start server

        php -S localhost:8000 -t public

8.Test

        test datafixture - php bin/console doctrine:fixtures:load --env=test
        vendor/bin/phpunit --filter=nome de fonction test
        or
        bin/phpunit --coverage-html chémas de votre repo local/raport

        Tester les requêtes avec un compte User

    login: admin12@gmail.com

    password: admin
