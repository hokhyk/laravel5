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
## installation from source
go get -d -u code.gitea.io/gitea

## installation from binary
wget -O gitea https://dl.gitea.io/gitea/1.4.2/gitea-1.4.2-linux-amd64
chmod +x gitea
### test running:
./gitea web
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
