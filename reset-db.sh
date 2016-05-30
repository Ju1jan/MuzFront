#!/usr/bin/env bash
php app/console doctrine:database:drop --force --if-exists
php app/console doctrine:database:create --if-not-exists
php app/console doctrine:migrations:migrate
php app/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/ --append