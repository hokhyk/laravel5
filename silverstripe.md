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
