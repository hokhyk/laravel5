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
- create this directory and file structure:
    /src/HokBundle/DataFixtures/ORM/LoadUsers.php
- Adding the following content to our class:
  <?php
    // mava/src/AppBundle/DataFixtures/ORM/LoadUsers.php
    namespace AppBundle\DataFixtures\ORM;
    use Doctrine\Common\DataFixtures\FixtureInterface;
    use Doctrine\Common\Persistence\ObjectManager;
    use AppBundle\Entity\User;
    class LoadUsers implements FixtureInterface
    {
        public function load(ObjectManager $manager)
        {
            // todo: create and persist objects
            $user1 = new User();
            $user1->setName('John');
            $user1->setBio('He is a cool guy');
            $user1->setEmail('john@mava.info');
            $manager->persist($user1);
            
            $user2 = new User();
            $user2->setName('Jack');
            $user2->setBio('He is a cool guy too');
            $user2->setEmail('jack@mava.info');
            $manager->persist($user2);
            $manager->flush();
        }
    }

## loading data fixtures
  $ bin/console doctrine:fixtures:load

  
# installing Jenkins
- installing apache2
 sudo apt-get update
 sudo apt-get install apache2
 sudo a2enmod proxy
 sudo a2enmod proxy_http
 sudo service apache2 restart
 
- installing Mysql and php
  sudo apt-get install php7.0 libapache2-mod-php7.0
  sudo apt-get install mysql-server mysql-client 
  mysql: root/root
  
- checking php mysql support:
  root@ubuntu16:~# apt-cache search php7|grep mysql
  php7.0-mysql - MySQL module for PHP
  如果没有结果，则sudo apt-get install php7.0-mysql
  
- install phpmyadmin
  sudo apt-get install phpmyadmin
  cp -r /usr/share/phpmyadmin/ /var/www/html/phpmyadmin

- installing Jenkins:
   - sudo apt install openjdk-8-jdk
   - wget -q -O - http://pkg.jenkins-ci.org/debian/jenkins-ci.org.key|sudo apt-key add -
   - echo "deb http://pkg.jenkins-ci.org/debian binary/" | sudo tee -a /etc/apt/sources.list.d/jenkins.list
   - sudo apt-get update
   - sudo apt-get install jenkins
   
- configure jenkins:
   - sudo vi /etc/default/jenkins
     # port for HTTP connector (default 8080; disable with -1)
     HTTP_PORT=8069   
   - sudo service jenkins start
   
   - setting up an apache virtual host for Jenkins as a proxy:
        $ sudo a2enmod proxy
        $ sudo a2enmod proxy_http
        $ sudo service apache2 restart
        - configure a virtual host:
            $ sudo vi /etc/apache2/sites-available/jenkins.conf
            <VirtualHost *:80>
                ServerName 192.168.0.100
                ProxyRequests Off
                <Proxy *>
                    Order deny,allow
                    Allow from all
                </Proxy>
                ProxyPreserveHost on
                ProxyPass / http://192.168.0.100:8069/
            </VirtualHost>
            $ sudo a2ensite jenkins
            $ sudo apache2 reload
   
   - visit :192.168.0.100
       - input the administrator password:
       - sudo cat /var/lib/jenkins/secrets/initialAdminPassword
         442c9ca929354d4b9da977c202a4b584
       - change admin login to admin/admin
       
   - 
         

     
