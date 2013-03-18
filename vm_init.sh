#!/usr/bin/env bash

################################################################################
# What does this script do for you?
#
# 1. Installs Apache, enables required apache modules
# 3. Installs PHP with required modules
# 3. Sets default virtual host for /vagrant/ directory (where project files are)
# 4. Installs MySQL, create 'root' user with 'root' as a password
# 4. Creates db with name 'govnokod'
# 5. Creates fake sendmail, so you can test email ssending
#
# (Note that this script is executed with root privileges.)
#
# What you have to do?
#
# 1. Know how to use vagrant - http://vagrantup.com
# 2. Follow instructions from README.md
# 3. Use correct db user and password (user 'root', password 'root')
################################################################################

cat << 'FAKESENDMAIL' > /usr/sbin/sendmail
#!/bin/sh

MAILS_FILE='/var/mails.txt'

while read x; do
    echo $x >> $MAILS_FILE
done

echo "----------------------" >> $MAILS_FILE
echo >> $MAILS_FILE

exit 0
FAKESENDMAIL

touch /var/mails.txt
chmod 0777 /var/mails.txt
chmod +x /usr/sbin/sendmail

apt-get update

apt-get install -y mc vim

debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password_again password root'
apt-get -y install mysql-server

apt-get install -y mysql-client

apt-get install -y php5 php5-cli php5-curl php5-intl php5-gd php5-json \
php5-mcrypt php5-mysql php5-xdebug

service apache2 stop

cat << 'CONFIGURATION' > /etc/apache2/sites-available/default
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /vagrant/www

    LogLevel warn
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    ErrorLog ${APACHE_LOG_DIR}/error.log

    <Directory /vagrant/www/>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    <FilesMatch "\.ph(p[3-5]?|tml)$">
        SetHandler None
    </FilesMatch>
    AddType application/x-httpd-php .php

    php_admin_flag engine on
    php_admin_value session.save_path /tmp
    php_admin_value upload_tmp_dir /tmp
    php_admin_value sendmail_path "/usr/sbin/sendmail -t -i -fwebmaster@localhost"
</VirtualHost>
CONFIGURATION

a2enmod rewrite
service apache2 start

mysqladmin -uroot -proot create govnokod
