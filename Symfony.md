# symfony 4.x installation:
- composer create-project symfony/skeleton mava 
- cd mava

## Run your application:
    1. Change to the project directory
    2. Create your code repository with the git init command
    3. Execute the php -S 127.0.0.1:8000 -t public command
    4. Browse to the http://localhost:8000/ URL.

       Quit the server with CTRL-C.
       Run composer require server --dev for a better web server.

  * Read the documentation at https://symfony.com/doc
  
# Symphony installation (version 3.4 and before)
- sudo curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony
- sudo chmod a+x /usr/local/bin/symfony
- symfony new mava 3.4
- symfony new mava lts

## using composer to start a symfony project:
- composer create-project symfony/framework-standard-edition my_project_name "3.4"

## Run your application:
- Installing the Web Server Bundle(for 2.8)
  - cd mava
  - composer require --dev symfony/web-server-bundle
- Starting the server(for 3.4 only this step is needed)
  -  php bin/console server:start
  -  php bin/console server:start 192.168.0.198:8000
  -  php bin/console server:start *:8080
  -  php bin/console server:run 192.168.0.198:8000
- testing the server
  - visit  http://192.168.0.198:8000/config.php

## show routes(3.4):
- bin/console debug:router

## generate a new bundle:
- bin/console generate:bundle

## setting up the database:
- mysql -uroot
- CREATE USER 'mava'@'%' IDENTIFIED BY 'mava';

- modify app/config/parameters.yml:
  parameters:
    database_driver: pdo_mysql
    database_host: 127.0.0.1
    database_port: 3306
    database_name: mava
    database_user: mava
    database_password: mava

- bin/console doctrine:database:create

##  generating an entity:
To generate an entity named User, run the following command:
$ bin/console doctrine:generate:entity

results:
  Entity generation  
  created ./src/HokBundle/Entity/
  created ./src/HokBundle/Entity/User.php
  created ./src/HokBundle/Resources/config/doctrine/
  created ./src/HokBundle/Resources/config/doctrine/User.orm.php
    > Generating entity class src/HokBundle/Entity/User.php: OK!
    > Generating repository class src/HokBundle/Repository/UserRepository.php: OK!
    > Generating mapping file src/HokBundle/Resources/config/doctrine/User.orm.php: OK!

## generate the related table:
    Now that we have our entity defined, it is time to generate the related table in our database:
    $ bin/console doctrine:schema:update --force

## installing bundles created by others:

