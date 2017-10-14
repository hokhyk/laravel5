#安装homestead
vagrant box add laravel/homestead (或者vagrant box add laravel/homestead /path/to/virtualbox.box)
cd ~
git clone https://github.com/laravel/homestead.git Homestead
#安装php7.1 lnmp
sudo apt-get update 
sudo apt-get install -y language-pack-en-base
locale-gen en_US.UTF-8


sudo apt-get install software-properties-common 
sudo LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php
sudo apt-get update 


sudo apt-get -y install php7.1
sudo apt-get -y install php7.1-mysql
sudo apt-get install php7.1-fpm

apt-get install php7.1-curl php7.1-xml php7.1-mcrypt php7.1-json php7.1-gd php7.1-mbstring


sudo apt-get -y install nginx

sudo apt-get -y install mysql-server-5.6
第三节视频：

sudo vim /etc/php/7.1/fpm/php.ini  // 将cgi.fix_pathinfo=1这一行去掉注释，将1改为0

sudo vim /etc/php/7.1/fpm/pool.d/www.conf 

// 配置这个 listen = /var/run/php7.1-fpm.sock

sudo service php7.1-fpm restart


sudo vim /etc/nginx/sites-available/default
Nginx 基础配置如下：

        listen 80 default_server;
        listen [::]:80 default_server ipv6only=on;

        root /var/www/laravel-ubuntu/public;
        index index.php index.html index.htm;

        # Make site accessible from http://localhost/
        server_name localhost;

        location / {
                # First attempt to serve request as file, then
                # as directory, then fall back to displaying a 404.
                try_files $uri $uri/ /index.php?$query_string;
                # Uncomment to enable naxsi on this location
                # include /etc/nginx/naxsi.rules
        }
        location ~ \.php$ {
                try_files $uri /index.php =404;
                fastcgi_split_path_info ^(.+\.php)(/.+)$;
                fastcgi_pass unix:/var/run/php7.1-fpm.sock;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
        }
还有就是，注意 laravel-ubuntu 这个目录的所有者为: www-data:www-data

最后给，storage 文件夹权限，重启 Nginx



#安装composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"


#安装laravel框架：
 ##1、全局安装
composer global require "laravel/installer"
export PATH="~/.config/composer/vendor/bin:$PATH" 确保 ~/.composer/vendor/bin 在系统路径中
laravel new blog
每次重新进入homestead，都要重新执行命令export PATH="~/.config/composer/vendor/bin:$PATH"

Once installed, the laravel new command will create a fresh Laravel installation in the directory you specify. For instance, laravel new blog will create a directory named blog containing a fresh Laravel installation with all of Laravel's dependencies already installed:

laravel new blog

 ##2、按照项目安装：
composer create-project --prefer-dist laravel/laravel blog

 ##3、配置
 Configuration
 Public Directory
 
 After installing Laravel, you should configure your web server's document / web root to be the  public directory. The index.php in this directory serves as the front controller for all HTTP requests entering your application.
 
 Configuration Files
 
 All of the configuration files for the Laravel framework are stored in the config directory. Each option is documented, so feel free to look through the files and get familiar with the options available to you.
 
 Directory Permissions
 
 After installing Laravel, you may need to configure some permissions. Directories within the storage and the bootstrap/cache directories should be writable by your web server or Laravel will not run. If you are using the Homestead virtual machine, these permissions should already be set.
 
 Application Key
 
 The next thing you should do after installing Laravel is set your application key to a random string. If you installed Laravel via Composer or the Laravel installer, this key has already been set for you by the php artisan key:generate command.
 
 Typically, this string should be 32 characters long. The key can be set in the .env environment file. If you have not renamed the .env.example file to .env, you should do that now. If the application key is not set, your user sessions and other encrypted data will not be secure!
 
 Additional Configuration
 
 Laravel needs almost no other configuration out of the box. You are free to get started developing! However, you may wish to review the config/app.php file and its documentation. It contains several options such as timezone and locale that you may wish to change according to your application.
 
 You may also want to configure a few additional components of Laravel, such as:
 
 Cache
 Database
 Session
 
 Web Server Configuration
 
 Pretty URLs
 Apache
 
 Laravel includes a public/.htaccess file that is used to provide URLs without the index.php front controller in the path. Before serving Laravel with Apache, be sure to enable the mod_rewrite module so the .htaccess file will be honored by the server.
 
 If the .htaccess file that ships with Laravel does not work with your Apache installation, try this alternative:
 
 Options +FollowSymLinks
 RewriteEngine On
 
 RewriteCond %{REQUEST_FILENAME} !-d
 RewriteCond %{REQUEST_FILENAME} !-f
 RewriteRule ^ index.php [L]
 Nginx
 
 If you are using Nginx, the following directive in your site configuration will direct all requests to the  index.php front controller:
 
 location / {
     try_files $uri $uri/ /index.php?$query_string;
 }
 
 
 
 
#laravel homestead box：
##homestead mapping
192.168.0.198 homestead.app
192.168.0.198 laravel_cms.app
192.168.0.198 lara.app  
##新建站点：
 1、vagrant ssh 192.168.0.198
 2、sudo /vagrant/scripts/serve-laravel.sh site-domain(lara.app) site-root-folder(/home/vagrant/Code/lara/public)
 3、 sudo cp /etc/nginx/ssl/homestead.app.crt /etc/nginx/ssl/lara.app.crt
 4、 sudo cp /etc/nginx/ssl/homestead.app.key /etc/nginx/ssl/lara.app.key
 5、修改本机hosts文件。 （winidows   ipconfig  /flushdns） 

  
## x-debug phpstorm中的配置
1、编译安装xdebug-2.5.4.tgz。
  tar -zxvf xdebug-2.5.4.tgz
  cd x-debug-2.5.4
  sudo find / -name phpize
  phpize
  ./configure --enable-xdebug
  make
  cd modules/
  pwd
  /home/vagrant/Code/xdebug-2.5.4/modules/
  
2、which php,  修改/etc/php/7.1/cli/php.ini
增加
[xdebug]
zend_extension = /home/vagrant/Code/xdebug-2.5.4/modules/xdebug.so
xdebug.remote_enable = 1
xdebug.remote_handler = dbgp
xdebug.remode_mode = req
xdebug.remote_host = 192.168.0.123
xdebug.remote_connect_back = 1
xdebug.remote_port = 9000
xdebug.idekey = "PHPSTORM"

4、重启web server。
   service nginx restart
5、phpstorm配置：
settings：interpretor， remote， vagrant
path mapping
run->edit configuration  

# adding phpmyadmin support for homestead box
## method 1 
This will install PhpMyAdmin (not the latest version) from Ubuntu's repositories. Assuming that your projects live in /home/vagrant/Code :

    sudo apt-get install phpmyadmin Do not select apache2 nor lighttpd when prompted. Just hit tab and enter.

    sudo ln -s /usr/share/phpmyadmin/ /home/vagrant/Code/phpmyadmin

    cd ~/Code && serve phpmyadmin.app /home/vagrant/Code/phpmyadmin

Note: If you encounter issues creating the symbolic link on step 2, try the first option or see Lyndon Watkins' answer below.
Final steps:

    Open the /etc/hosts file on your main machine and add:

    127.0.0.1  phpmyadmin.app

    Go to http://phpmyadmin.app:8000

## method 2
Step 1:

Go to the phpMyAdmin website, download the latest version and unzip it into your code directory
Step 2:

Open up homestead.yaml file and add these lines

folders:
    - map: /Users/{yourName}/Code/phpMyAdmin
      to: /home/vagrant/Code/phpMyAdmin
sites:
    - map: phpmyadmin.app
      to: /home/vagrant/Code/phpMyAdmin

Step 3:

Open your hosts file and add this line:

127.0.0.1 phpmyadmin.app

Step 4:

You may need to run vagrant provision to load the new configuration if vagrant is already running.

