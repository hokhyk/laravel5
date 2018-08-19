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
  sudo apt-get install php7.0 libapache2-mod-php7.0 (需要升级为7.1)
  
  sudo add-apt-repository ppa:ondrej/php
  sudo apt-get update
  sudo apt-get install php7.1 php7.1-common
  sudo apt-get install libapache2-mod-php7.1 php7.1-mysql php7.1-zip php7.1-gd php7.1-mbstring mcrypt  php7.1-mcrypt  php7.1-xml  php7.1-curl
  sudo apt-get install mysql-server mysql-client 
  mysql: root/root
  
- checking php mysql support:
  root@ubuntu16:~# apt-cache search php7|grep mysql
  php7.1-mysql - MySQL module for PHP
  如果没有结果，则sudo apt-get install php7.1-mysql
  
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
                ServerName jenkins.app
                ProxyRequests Off
                <Proxy *>
                    Order deny,allow
                    Allow from all
                </Proxy>
                ProxyPreserveHost on
                ProxyPass / http://localhost:8069/
            </VirtualHost>
            $ sudo a2ensite jenkins
            $ sudo apache2 reload
            $ sudo vi /etc/hosts     # adding   192.168.0.100  jenkins.app   
   - visit http://jenkins.app
       - input the administrator password:
       - sudo cat /var/lib/jenkins/secrets/initialAdminPassword
         442c9ca929354d4b9da977c202a4b584
       - change admin login to admin/admin
       
   - isntall the following plugins:
    °°    GitHub (access to GitHub repositories)
    °°    Checkstyle (reading CodeSniffer logs in the Checkstyle format)
    °°    Clover PHP (processing PHPUnit's Clover log file)
    °°    Crap4J (processing PHPUnit's Crap4J XML log file)
    °°    DRY (processing phpcpd logs in the PMD-CPD format)
    °°    HTML Publisher (publishing documentation generated by phpDox, for instance)
    °°    JDepend (processing PHP_Depend logs in the JDepend format)
    °°    Plot (processing phploc CSV output)
    °°    PMD (processing PHPMD log files in the PMD format)
    °°    Violations (processing various log files)
    °°    xUnit (processing PHPUnit's JUnit XML log file)
   - check if you have git installed:
     sudo apt-get install git 
     
   - installing PHP tools:
    - using PEAR():
      - add the following PEAR channels to the CI server system:
        $ sudo pear channel-discover pear.pdepend.org
        $ sudo pear channel-discover pear.phpmd.org
        $ sudo pear channel-discover pear.phpunit.de
        $ sudo pear channel-discover pear.phpdoc.org
        $ sudo pear channel-discover pear.symfony-project.com
        
      - install PHP tools as follows:
        $ sudo pear install pdepend/PHP_Depend
        $ sudo pear install phpmd/PHP_PMD
        $ sudo pear install phpunit/phpcpd
        $ sudo pear install phpunit/phploc
        $ sudo pear install --alldeps phpunit/PHP_CodeBrowser
        $ sudo pear install phpdoc/phpDocumentor-alpha
    - using composer:
        $sudo apt-get install composer
        $ composer global require phpunit/phpunit
        $ composer global require phpunit/dbunit
        $ composer global require phing/phing
        $ composer global require sebastian/phpcpd
        $ composer global require phpmd/phpmd
        $ composer global require pdepend/pdepend
        $ composer global require phploc/phploc
        $ composer global require "squizlabs/php_codesniffer=*"
        $ composer global require phpdocumentor/phpdocumentor   (###problematic...... ,use phpdox instead.)

    - using phar for phpdox:
        ➜ wget http://phpdox.de/releases/phpdox.phar
        ➜ chmod +x phpdox.phar
        ➜ mv phpdox.phar /usr/local/bin/phpdox
        ➜ phpdox --version

    - config phpcs:
        $ vi ~/.bash_profile
           adding the following line:
           export PATH=$PATH:/home/vagrant/.config/composer/vendor/bin/
        $ source ~/.bash_profile
        $ git clone git://github.com/djoos/Symfony2-coding-standard.git
        $ phpcs --config-set symfony2_standard  /home/vagrant/Symfony2-coding-standard
        $ phpcs -i
        $ phpcs /path/to/code     

- using apache Ant
  - apache Ant's build.xml file.
    sudo apt install ant
    
- jenkins to integrate with github:
  - Sign in Jenkins,new item->source code management-> choose git-> type in your project's git repository
  - configure ssh keys:
      $sudo su - jenkins
      $ssh-kengen -t dsa
      $cat ~/.ssh/id_dsa.pub |xclip
  - Go to your github repository, choose settings tab, and select Deploy keys-> Add deploy key->paste the content of id_das.pub in the text area.
  - jenkins@192.168.0.100:~$ git ls-remote -h git@github.com:Soolan/mava.git HEAD

# Using BDD methodology in Symfony


