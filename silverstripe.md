# silverstripe
  1. git clone silverstripe/installer  & cd silverstripe
  2. composer install 
  3. database:
    mysql -uroot
 CREATE USER 'boilerplate'@'%' IDENTIFIED BY 'boilerplate';
 CREATE DATABASE IF NOT EXISTS `boilerplate` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
 GRANT ALL PRIVILEGES ON `boilerplate`.* TO 'boilerplate'@'%';

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
