# The MuzFront Project

==

A Symfony project created on May 28, 2016, 4:56 pm.

## QUICK LUNCH

1) get code from the repository
$ git clone git@github.com:Ju1jan/MuzFront.git
NB! assumption: you've got ssh key for your GitHub account

2) Edit the config file
<EDIT CONFIG>
vim app/config/parameters.yml

Example:

    database_name: muz_front
    database_user: root
    database_password: r00t

3) Run DB migrations

    $ php app/console doctrine:migrations:migrate

4) Run DB seed to fill demo data

    $ php app/console doctrine:fixtures:load
    $ doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/ --append

That's all according back-end. One more step to build front-end files.

5) Install npm packages, bower components and build static files (css and JS) using gulp
    
    $ npm install 
    $ bower install 
    $ gulp

NB! You should have npm, bower and gulp installed in your OS. Build's files are not stored in git repository.

6) Profit!
That's all. Enjoy your day:)
