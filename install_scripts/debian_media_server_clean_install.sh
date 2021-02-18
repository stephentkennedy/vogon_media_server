#!/bin/bash
echo -e "This is the installation script for Vogon Media Server. The software this will install on your device is not configured to be secure. Are you comfortable with other devices on your network potentially having root access to this device and its files? y/n"

read script_confirm

#Exit if they did not confirm
if [[ $script_confirm != 'y' && $script_confirm != 'Y' ]]
then
	echo -e "Exiting."
	exit 0
fi

echo -e "Upgrading Packages"
sudo apt-get update -y
sudo apt-get upgrade -y

echo -e "Installing Apache"
sudo apt-get install apache2 -y
sudo a2enmod rewrite


echo -e "Installing PHP"
sudo apt-get install php libapache2-mod-php php-mbstring php-xmlrpc php-soap php-gd php-xml php-cli php-zip -y

echo -e "Installing MySQL"
sudo apt-get install mariadb-server php-mysql -y
sudo mysql -e "CREATE DATABASE vogon /*\!40100 DEFAULT CHARACTER SET utf8 */;"
sudo mysql -e "CREATE USER vogon@localhost IDENTIFIED BY 'vogon';"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO vogon@localhost;"
sudo mysql -e "FLUSH PRIVILEGES;"

echo -e "Would you like to install phpmyadmin? y/n"

read pma_confirm

if [[ $pma_confirm == 'y'  || $pma_confirm == 'Y' ]]
then
	sudo apt-get install phpmyadmin -y
fi

echo -e "Restarted Apache"


echo -e "Installing curl"
sudo apt-get install curl -y
sudo apt-get install php-curl -y

echo -e "Installing unzip"
sudo apt-get install unzip -y

echo -e "Installing ffmpeg"
sudo apt-get install ffmpeg -y

echo -e "Installing Composer"
curl -sS https://getcomposer.org/installer | php

sudo mv composer.phar /usr/local/bin/composer

echo -e "Downloading most recent Vogon Media Server code"

curl -L https://github.com/stephentkennedy/vogon_media_server/archive/master.zip > media_server.zip

sudo mv media_server.zip /var/www/html/media_server.zip
sudo chown root /var/www/html/media_server.zip
sudo unzip /var/www/html/media_server.zip -d /var/www/html
sudo rm /var/www/html/index.html
sudo cp -rp /var/www/html/vogon_media_server-master/{.,}* /var/www/html
sudo rm -r /var/www/html/vogon_media_server-master
sudo rm /var/www/html/media_server.zip
sudo cp /var/www/html/example_configs/apache2.conf /etc/apache2/apache2.conf
sudo systemctl restart apache2

echo -e "Installing MiniDLNA Server"
sudo apt-get install minidlna -y
sudo cp /var/www/html/example_configs/minidlna.conf /etc/minidlna.conf
sudo service minidlna restart

echo -e "Installing dependencies via composer"
cd /var/www/html
sudo composer install --no-dev

sudo php index.php --app_name="Vogon Media Server" --database_host=localhost --database_name="vogon" --database_user="vogon" --database_password="vogon" --uri="" >/dev/null