# install gvm
$bash < <(curl -s -S -L https://raw.githubusercontent.com/moovweb/gvm/master/binscripts/gvm-installer)

# install go1.4
$gvm listall
$gvm list
$gvm install go1.4

# install go
$sudo zypper install go
$gvm use system
$go version
go version go1.9.4 linux/amd64

# install gitea
## installation from binary
 1. download gitea binary package. 
    wget -O gitea https://dl.gitea.io/gitea/1.4.2/gitea-1.4.2-linux-amd64
 2. chmod +x gitea
 3. ./gitea web      ! /data/todo/vagrant/golang/gitea/gitea web -c /data/todo/vagrant/golang/gitea/custom/conf/app.ini &
 4. create mysql database:
  mysql -uroot
 CREATE USER 'gitea'@'%' IDENTIFIED BY 'gitea';
 CREATE USER 'gitea'@'localhost' IDENTIFIED BY 'gitea';
 CREATE DATABASE IF NOT EXISTS `gitea` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
 GRANT ALL PRIVILEGES ON `gitea`.* TO 'gitea'@'%';

 5. visit: http://localhost:3000
 
 default admin: hok/hok hok@gitea.me


Recommended server configuration
Prepare environment

Check that git is installed on the server, if it is not install it first.

git --version

Create user to run gitea (ex. git)

adduser \
   --system \
   --shell /bin/bash \
   --gecos 'Git Version Control' \
   --group \
   --disabled-password \
   --home /home/git \
   git

Create required directory structure

mkdir -p /var/lib/gitea/{custom,data,indexers,public,log}
chown git:git /var/lib/gitea/{data,indexers,log}
chmod 750 /var/lib/gitea/{data,indexers,log}
mkdir /etc/gitea
chown root:git /etc/gitea
chmod 770 /etc/gitea

NOTE: /etc/gitea is temporary set with write rights for user git so that Web installer could write configuration file. After installation is done it is recommended to set rights to read-only using:

chmod 750 /etc/gitea
chmod 644 /etc/gitea/app.ini

Copy gitea binary to global location

cp gitea /usr/local/bin/gitea

Create service file to start gitea automatically

See how to create Linux service

# running gitea as a linux service
Run as service in Ubuntu 16.04 LTS
Using systemd

Run the below command in a terminal:

sudo vim /etc/systemd/system/gitea.service

Copy the sample gitea.service.

Uncomment any service that needs to be enabled on this host, such as MySQL.

Change the user, home directory, and other required startup values. Change the PORT or remove the -p flag if default port is used.

Enable and start gitea at boot:

sudo systemctl enable gitea
sudo systemctl start gitea

Using supervisor

Install supervisor by running below command in terminal:

sudo apt install supervisor

Create a log dir for the supervisor logs:

#assuming gitea is installed in /home/git/gitea/
mkdir /home/git/gitea/log/supervisor

Open supervisor config file in a file editor:

sudo vim /etc/supervisor/supervisord.conf

Append the configuration from the sample supervisord config.

Change the user(git) and home(/home/git) settings to match the deployment environment. Change the PORT or remove the -p flag if default port is used.

Lastly enable and start supervisor at boot:

sudo systemctl enable supervisor
sudo systemctl start supervisor

