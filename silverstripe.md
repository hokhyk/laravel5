# silverstripe
  1. git clone silverstripe/installer  & cd silverstripe
  2. composer install 
  3. database:
    mysql -uroot
 CREATE USER 'sf4'@'%' IDENTIFIED BY 'sf4';
 CREATE DATABASE IF NOT EXISTS `silverstripe4` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
 GRANT ALL PRIVILEGES ON `silverstripe4`.* TO 'sf4'@'%';

 4. 配置web服务器等
 sudo /vagrant/scripts/serve-laravel.sh silverstripe.app /home/vagrant/Code/silverstripe-installer4/public/
 sudo nginx -t
 sudo nginx -s reload
 修改hosts文件：laravel homestead box：
 sudo vi /etc/hosts
 192.168.0.198 bp.app

 5. visit silverstripe.app/install.php
    fill in the database information:   localhost   homestead/secret  silverstripe4   admin/admin
    and then click the "reinstall" button at the bottom.
 
 6. visit silverstripe.app and start building the CMS site.

 
# install silverstripe4 using composer
  composer create-project silverstripe/installer ./my/website/folder ^4 --prefer-source

# SS Core environment variables
SilverStripe core environment variables are listed here, though you're free to define any you need for your application.
Name 	Description
SS_DATABASE_CLASS 	The database class to use, MySQLPDODatabase, MySQLDatabase, MSSQLDatabase, etc. defaults to MySQLPDODatabase.
SS_DATABASE_SERVER 	The database server to use, defaulting to localhost.
SS_DATABASE_USERNAME 	The database username (mandatory).
SS_DATABASE_PASSWORD 	The database password (mandatory).
SS_DATABASE_PORT 	The database port.
SS_DATABASE_SUFFIX 	A suffix to add to the database name.
SS_DATABASE_PREFIX 	A prefix to add to the database name.
SS_DATABASE_TIMEZONE 	Set the database timezone to something other than the system timezone.
SS_DATABASE_NAME 	Set the database name. Assumes the $database global variable in your config is missing or empty.
SS_DATABASE_CHOOSE_NAME 	Boolean/Int. If defined, then the system will choose a default database name for you if one isn't give in the $database variable. The database name will be "SS_" followed by the name of the folder into which you have installed SilverStripe. If this is enabled, it means that the phpinstaller will work out of the box without the installer needing to alter any files. This helps prevent accidental changes to the environment. If SS_DATABASE_CHOOSE_NAME is an integer greater than one, then an ancestor folder will be used for the database name. This is handy for a site that's hosted from /sites/examplesite/www or /buildbot/allmodules-2.3/build. If it's 2, the parent folder will be chosen; if it's 3 the grandparent, and so on.
SS_DEPRECATION_ENABLED 	Enable deprecation notices for this environment.
SS_ENVIRONMENT_TYPE 	The environment type: dev, test or live.
SS_DEFAULT_ADMIN_USERNAME 	The username of the default admin. This is a user with administrative privileges.
SS_DEFAULT_ADMIN_PASSWORD 	The password of the default admin. This will not be stored in the database.
SS_USE_BASIC_AUTH 	Protect the site with basic auth (good for test sites).
When using CGI/FastCGI with Apache, you will have to add the RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}] rewrite rule to your .htaccess file
SS_SEND_ALL_EMAILS_TO 	If you define this constant, all emails will be redirected to this address.
SS_SEND_ALL_EMAILS_FROM 	If you define this constant, all emails will be sent from this address.
SS_ERROR_LOG 	Relative path to the log file.
SS_PROTECTED_ASSETS_PATH 	Path to secured assets - defaults to ASSET_PATH/.protected
SS_DATABASE_MEMORY 	Used for SQLite3 DBs
SS_TRUSTED_PROXY_IPS 	IP address or CIDR range to trust proxy headers from. If left blank no proxy headers are trusted. Can be set to 'none' (trust none) or '*' (trust all)
SS_ALLOWED_HOSTS 	A comma deliminated list of hostnames the site is allowed to respond to
SS_MANIFESTCACHE 	The manifest cache to use (defaults to file based caching). Must be a CacheInterface or CacheFactory class name
SS_IGNORE_DOT_ENV 	If set the .env file will be ignored. This is good for live to mitigate any performance implications of loading the .env file
SS_BASE_URL 	The url to use when it isn't determinable by other means (eg: for CLI commands)
SS_CONFIGSTATICMANIFEST 	Set to SS_ConfigStaticManifest_Reflection to use the Silverstripe 4 Reflection config manifest (speed improvement during dev/build and ?flush)
SS_DATABASE_SSL_KEY 	Absolute path to SSL key file
SS_DATABASE_SSL_CERT 	Absolute path to SSL certificate file
SS_DATABASE_SSL_CA 	Absolute path to SSL Certificate Authority bundle file
SS_DATABASE_SSL_CIPHER 	Optional setting for custom SSL cipher

# Directory structure
## core structure
  Directory 	Description
public/ 	Webserver public webroot
public/assets/ 	Images and other files uploaded via the SilverStripe CMS. You can also place your own content inside it, and link to it from within the content area of the CMS.
public/resources/ 	Exposed public files added from modules. Folders within this parent will match that of the source root location.
vendor/ 	SilverStripe modules and other supporting libraries (the framework is in vendor/silverstripe/framework)
themes/ 	Standard theme installation location

## custom code structure
  Directory 	Description
app/ 	This directory contains all of your code that defines your website.
app/_config 	YAML configuration specific to your application
app/src 	PHP code for model and controller (subdirectories are optional)
app/tests 	PHP Unit tests
app/templates 	HTML templates with *.ss-extension for the $default theme
app/css 	CSS files
app/images 	Images used in the HTML templates
app/javascript 	Javascript and other script files
app/client 	More complex projects can alternatively contain frontend assets in a common client folder
app/themes/<yourtheme> 	Custom nested themes (note: theme structure is described below)

## theme structure
Themes Structure
Directory 	Description
themes/simple/ 	Standard "simple" theme
themes/<yourtheme>/ 	Custom theme base directory
themes/<yourtheme>/templates 	Theme templates
themes/<yourtheme>/css 	Theme CSS files

## Module structure
Modules are commonly stored as composer packages in the vendor/ folder. They need to have a _config.php file or a _config/ directory present, and should follow the same conventions as posed in "Custom Site Structure".


# SS CMS 
## Explaining security groups and roles
Instead of assigning individual permissions to access, create, edit, or delete content per user, you can use Security Groups and Roles to organise which accounts have certain permissions.

Roles are collections of permissions. For example, you might create an editor role to give a group read/write access to all content, or a "contributor" role who has the right to add content to the CMS but not to publish it, or a "spectator" role which gives a person a right to view the backend of the CMS, but not any ability to edit it.

Security groups are collections of users, and whatever permissions they have apply to a subset of pages. So, for example, the marketing team could have access to the parts of the website dealing with marketing, and the customer support team could have access to the parts of the website dealing with customer queries.

One of the ways that the two can be used together is to assign similar roles to different groups. You only need to define an "editor" role once, but by applying the "editor" role to different groups with different access to different pages, so if you assigned the "editor" role to both the marketing team and development team security groups, the marketing team would be able to edit the marketing pages, and the development team would be able to edit the development pages, but they would not be able to edit each other's pages.

Groups represent a group of members, and you can assign a Group with a set of roles which are descriptors for various permissions in the system e.g. a group which has the "Administrator" role, allows access to the CMS.

# installing blog, subsites, widgets
composer search silverstripe
composer require silverstripe/blog
composer require silverstripe/subsites
composer require silverstripe/widgets

modify configuration file ./mysite/_config/mysite.yml, adding the fllowing codes:
SilverStripe\Blog\Model\Blog:
  extensions:
    - SilverStripe\Widgets\Extensions\WidgetPageExtension
    
visit the route http://silverstripe.app/dev/build/  to migrate the database tables.
visit the route http://silverstripe.app/?flush  to clear the cache.

# Common subsite uses
Subsites can be used for various different reasons here are some of the common ones:
    Setting up a subsite for a small campaign so for example a clothing company may set up a summer or winter subsite to market just that season of clothing.
    Locking down a particular subsite you may create a particular department like recruitment who would have access to create and edit pages for their particular subsite but they would not be able to modify the main website.
    Running sub-domains on a single SilverStripe instance, with subsites if a sub-domain is pointing to the same instance and has been setup correctly you can manage this via a single CMS instance.
    Subsites can not be used to run multiple websites on a single instance. Subsites does not allow you to run multiple domains/vhosts on a single instance.

# CMS workflow
composer require symbiote/silverstripe-advancedworkflow
https://packagist.org/packages/symbiote/silverstripe-advancedworkflow

# Silverstripe ORM
SilverStripe uses an object-relational model to represent its information.
    Each database table maps to a PHP class.
    Each database row maps to a PHP object.
    Each database column maps to a property on a PHP object.

All data tables in SilverStripe are defined as subclasses of DataObject. The DataObject object represents a single row in a database table, following the "Active Record" design pattern. Database Columns are defined as Data Types in the static $db variable along with any relationships defined as $has_one, $has_many, $many_many properties on the class.

app/code/Player.php

use SilverStripe\ORM\DataObject;

class Player extends DataObject 
{
    private static $db = [
        'PlayerNumber' => 'Int',
        'FirstName' => 'Varchar(255)',
        'LastName' => 'Text',
        'Birthday' => 'Date'
    ];
}

