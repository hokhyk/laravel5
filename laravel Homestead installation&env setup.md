# installing virtualbox

# installing vagrant

# installing composer
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer -V

# 安装homestead
vagrant box add laravel/homestead (或者vagrant box add laravel/homestead /path/to/virtualbox.box)
cd ~
## 克隆homestead配置文件。
git clone https://github.com/laravel/homestead.git Homestead

## 生成Homestead.yaml文件。
切换至Homestead目录cd Homestead，运行命令bash init.sh生成Homestead.yaml文件

## 修改Homestead.yaml配置文件
vi ~/.homestead/Homestead.yaml
 
## ssh-keygen -t rsa
 cp /home/vagrant/.ssh/id_rsa   /home/vagrant/share/todo/vagrant/laravel/Homestead/.vagrant/machines/homestead-7/virtualbox/private_key
 
## 修改homestead.rb文件。
 如果这时候你直接在Homestead目录下启动homestead虚拟机，肯定会得到反复叫你下载virtualbox的提示，猜测这是由于手动添加的virtualbox没有保存版本信息的缘故(可以使用命令vagrant box list来查看)。所以可以通过修改Homestead/scripts/homestead.rb来解决这一个问题，找到config.vm.box_version = settings["version"] ||= ">= 0.4.4"这一行，将其修改为config.vm.box_version = settings["version"] ||= ">= 0"即可
 
## 启动虚拟机。
进入Homestead目录，使用命令vagrant up命令启动虚拟机，可使用vagrant ssh登陆虚拟机。顺便一提，虚拟机数据库的root用户密码为secret

## 在homestead虚拟机上安装composer
$vagrant ssh
cd ~/Code

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

## 安装laravel框架：
### 1、全局安装
~vagrant@Homestead$vagrant ssh
cd ~/Code

export PATH="~/.config/composer/vendor/bin:$PATH" 确保 ~/.composer/vendor/bin 在系统路径中.
每次重新进入homestead，都要重新执行命令export PATH="~/.config/composer/vendor/bin:$PATH".

composer global require "laravel/installer"

### 2、按照项目安装：
composer create-project --prefer-dist laravel/laravel laraxxxxx


### 3、新建项目，配置web服务器等
#### 新建项目
laravel new laraxxxxx 或者 composer create-project --prefer-dist laravel/laravel laraxxxxx

#### 新建站点：
 1、vagrant ssh 192.168.0.198
 2、sudo /vagrant/scripts/serve-laravel.sh site-domain(laraxxxxx.app) site-root-folder(/home/vagrant/Code/laraxxxxx/public)
 在/etc/nginx/sites-available下激活该站点。
 3、 sudo cp /etc/nginx/ssl/homestead.app.crt /etc/nginx/ssl/laraxxxxx.app.crt
 4、 sudo cp /etc/nginx/ssl/homestead.app.key /etc/nginx/ssl/laraxxxxx.app.key
 5、修改本机hosts文件。 （winidows   ipconfig  /flushdns） 
 6、sudo nginx -t
 7、sudo nginx -s reload
#### 修改hosts文件
on laravel homestead box：

sudo vi /etc/hosts

##homestead mapping
192.168.0.198 homestead.app
192.168.0.198 laravel_cms.app
192.168.0.198 laraxxxxx.app

### 4、配置laravel项目参数
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
 
 
#### 5. 添加ide-helper
on homestead box, 
Install

Require this package with composer using the following command:

composer require --dev barryvdh/laravel-ide-helper
After updating composer, add the service provider to the providers array in config/app.php

Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
To install this package on only development systems, add the --dev flag to your composer command:

composer require --dev barryvdh/laravel-ide-helper
In Laravel, instead of adding the service provider in the config/app.php file, you can add the following code to your app/Providers/AppServiceProvider.php file, within the register() method:

public function register()
{
    if ($this->app->environment() !== 'production') {
        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
    }
    // ...
}
This will allow your application to load the Laravel IDE Helper on non-production enviroments.

Automatic phpDoc generation for Laravel Facades

You can now re-generate the docs yourself (for future updates)

php artisan ide-helper:generate
Note: bootstrap/compiled.php has to be cleared first, so run php artisan clear-compiled before generating (and php artisan optimize after).

You can configure your composer.json to do this after each commit:

"scripts":{
    "post-update-cmd": [
        "Illuminate\\Foundation\\ComposerScripts::postUpdate",
        "php artisan ide-helper:generate",
        "php artisan ide-helper:meta",
        "php artisan optimize"
    ]
},
You can also publish the config file to change implementations (ie. interface to specific class) or set defaults for --helpers or --sublime.

php artisan vendor:publish --provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config
The generator tries to identify the real class, but if it cannot be found, you can define it in the config file.

Some classes need a working database connection. If you do not have a default working connection, some facades will not be included. You can use an in-memory SQLite driver by adding the -M option.

You can choose to include helper files. This is not enabled by default, but you can override it with the --helpers (-H) option. The Illuminate/Support/helpers.php is already set up, but you can add/remove your own files in the config file.

Automatic phpDocs for models

You need to require doctrine/dbal: ~2.3 in your own composer.json to get database columns.
composer require doctrine/dbal
If you don't want to write your properties yourself, you can use the command php artisan ide-helper:models to generate phpDocs, based on table columns, relations and getters/setters. You can write the comments directly to your Model file, using the --write (-W) option. By default, you are asked to overwrite or write to a separate file (_ide_helper_models.php). You can force No with --nowrite (-N). Please make sure to backup your models, before writing the info. It should keep the existing comments and only append new properties/methods. The existing phpdoc is replaced, or added if not found. With the --reset (-R) option, the existing phpdocs are ignored, and only the newly found columns/relations are saved as phpdocs.

php artisan ide-helper:models Post
/**
 * An Eloquent Model: 'Post'
 *
 * @property integer $id
 * @property integer $author_id
 * @property string $title
 * @property string $text
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\Comment[] $comments
 */
By default, models in app/models are scanned. The optional argument tells what models to use (also outside app/models).

php artisan ide-helper:models Post User
You can also scan a different directory, using the --dir option (relative from the base path):

php artisan ide-helper:models --dir="path/to/models" --dir="app/src/Model"
You can publish the config file (php artisan vendor:publish) and set the default directories.

Models can be ignored using the --ignore (-I) option

php artisan ide-helper:models --ignore="Post,User"
Note: With namespaces, wrap your model name in double-quotes ("): php artisan ide-helper:models "API\User", or escape the slashes (Api\\User)

For properly recognition of Model methods (i.e. paginate, findOrFail) you should extend \Eloquent or add

/** @mixin \Eloquent */
for your model class.

Automatic phpDocs generation for Laravel Fluent methods

If you need phpDocs support for Fluent methods in migration, for example

$table->string("somestring")->nullable()->index();
After publishing vendor, simply change the include_fluent line your config/ide-helper.php file into:

'include_fluent' => true,
And then run php artisan ide-helper:generate , you will now see all of the Fluent methods are recognized by your IDE.

PhpStorm Meta for Container instances

It's possible to generate a PhpStorm meta file to add support for factory design pattern. For Laravel, this means we can make PhpStorm understand what kind of object we are resolving from the IoC Container. For example, events will return an Illuminate\Events\Dispatcher object, so with the meta file you can call app('events') and it will autocomplete the Dispatcher methods.

php artisan ide-helper:meta
app('events')->fire();
\App::make('events')->fire();

/** @var \Illuminate\Foundation\Application $app */
$app->make('events')->fire();

// When the key is not found, it uses the argument as class name
app('App\SomeClass');
Pre-generated example: https://gist.github.com/barryvdh/bb6ffc5d11e0a75dba67

Note: You might need to restart PhpStorm and make sure .phpstorm.meta.php is indexed. Note: When you receive a FatalException about a class that is not found, check your config (for example, remove S3 as cloud driver when you don't have S3 configured. Remove Redis ServiceProvider when you don't use it).

##### ide-helper support for laravel5.5 of Fluent class.
Updated version for Laravel 5.5

Create a file in your rood directory with name like _ide_helper_custom.php and copy next code into it:

<?php

namespace  {
    exit("This file should not be included, only analyzed by your IDE");
}

namespace Illuminate\Support {

    /**
     * @method Fluent first()
     * @method Fluent after($column)
     * @method Fluent change()
     * @method Fluent nullable()
     * @method Fluent unsigned()
     * @method Fluent unique()
     * @method Fluent index()
     * @method Fluent primary()
     * @method Fluent spatialIndex()
     * @method Fluent default($value)
     * @method Fluent onUpdate($value)
     * @method Fluent onDelete($value)
     * @method Fluent references($value)
     * @method Fluent on($value)
     * @method Fluent charset($value)
     * @method Fluent collation($value)
     * @method Fluent comment($value)
     * @method Fluent autoIncrement()
     * @method Fluent storedAs($value)
     * @method Fluent useCurrent()
     * @method Fluent virtualAs($value)
     */
    class Fluent {

    }

}

This will force PHP Storm to index the file and correctly suggest methods for chaining. Tested in PHP Storm 2017.3 but should work in all previous and hopefully future versions of IDE.




### 6、数据库phpmyadmin支持

#### adding phpmyadmin support for homestead box
##### method 1 
This will install PhpMyAdmin (not the latest version) from Ubuntu's repositories. Assuming that your projects live in /home/vagrant/Code :

    sudo apt-get install phpmyadmin Do not select apache2 nor lighttpd when prompted. Just hit tab and enter.

    sudo ln -s /usr/share/phpmyadmin/ /home/vagrant/Code/phpmyadmin

    cd ~/Code && serve phpmyadmin.app /home/vagrant/Code/phpmyadmin

Note: If you encounter issues creating the symbolic link on step 2, try the first option or see Lyndon Watkins' answer below.
Final steps:

    Open the /etc/hosts file on your main machine and add:

    127.0.0.1  phpmyadmin.app

    Go to http://phpmyadmin.app:8000

##### method 2
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


### 7、homestead mysql database connection
#### preferablly using HediSQL + Wine under linux environment.
• Connection Type: Standard (non-SSH)
• Host: 127.0.0.1
• Username: homestead
• Password: secret
• Port: 33060
For my case, the host is 192.168.0.198
  
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


#Laravel artisan command:
under the laravel project root dictory:
$php artisan list make
Laravel Framework 5.4.28

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
      --env[=ENV]       The environment the command should run under
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands for the "make" namespace:
  make:auth          Scaffold basic login and registration views and routes
  make:command       Create a new Artisan command
  make:controller    Create a new controller class
  make:event         Create a new event class
  make:job           Create a new job class
  make:listener      Create a new event listener class
  make:mail          Create a new email class
  make:middleware    Create a new middleware class
  make:migration     Create a new migration file
  make:model         Create a new Eloquent model class
  make:notification  Create a new notification class
  make:policy        Create a new policy class
  make:provider      Create a new service provider class
  make:request       Create a new form request class
  make:seeder        Create a new seeder class
  make:test          Create a new test class
