# The MuzFront Project

==

A Symfony project created on May 28, 2016, 4:56 pm.

## QUICK LUNCH

1) get code from the repository and install PHP vendors library using composer 
    
    $ git clone git@github.com:Ju1jan/MuzFront.git
    
NB! assumption: you've got ssh key for your GitHub account

To get vendors using composer please run

    $ composer install --no-scripts
    
(if composer has been installed globally in your OS) 

2) Edit the config file
At first copy config file using default

    $ copy parameters.yml.dist parameters.yml

<EDIT CONFIG>

    $ vim app/config/parameters.yml

Example:

    database_port: 3306
    database_name: muz_front
    database_user: root
    database_password: r00t

3) Run DB migrations

    $ php app/console doctrine:migrations:migrate

4) Run DB seed to fill demo data

    $ php app/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/ --append

That's all according back-end. One more step to build front-end files.

5) Install npm packages, bower components and build static files (css and JS) using gulp
    
    $ npm install 
    $ bower install 
    $ gulp

NB! You should have npm, bower and gulp installed in your OS. Build's files are not stored in git repository.

If you dont't have it, please follow the documentation to install npm https://github.com/nodesource/distributions
and them run these:

    $ npm install -g bower
    $ npm install -g gulp

6) Profit!
That's all. Enjoy your day:)


---

## Once again. In short.

So. All commands for quick start are kind of this:

    $ npm install -g bower
    $ npm install -g gulp
    $ npm install 
    $ bower install 
    $ gulp
    $ git clone git@github.com:Ju1jan/MuzFront.git
    $ composer install --no-scripts
    $ copy parameters.yml.dist parameters.yml
    $ vim app/config/parameters.yml
    $ php app/console doctrine:migrations:migrate
    $ php app/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/ --append
    $ npm install 
    $ bower install 
    $ gulp
