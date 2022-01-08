# ----------------------------------------------------------------------------------
#
# This file describes step by step how a Playground Server can be setup.
#
# By default the current https://playground.fastsitephp.com/ is installed
# on the same server as other sites so it uses a differant setup than the
# original setup descibed in [Playground Server Setup.txt]. This version
# is recommend for testing as its the fastest way to setup the server to
# match the current production server. The original setup described in
# [Playground Server Setup.txt] used Apache only and would be recommended
# on a server that does not have other sites. nginx is used with Apache
# for this setup due to the unique needs to the main server.
#
# These instructions apply specifically to Ubuntu 20.04 LTS on AWS Lightsail;
# if using the commands for another build of Linux you will likely need to make
# changes to the commands for the custom build of PHP.
#
# This file is a shell script however the [*.sh] extension is so the commands
# can show up in code editor using syntax highlighting.
#
# When `nano` commands specify saving in this script use the following:
#    {control+s} -> {control+x}
#
# If you are testing this and need help or have an issue or have any questions
# then feel free to open an issue on GitHub and myself (Conrad, author of FastSitePHP)
# will likely be able to provide support and help.
#
# ----------------------------------------------------------------------------------

echo 'This script contains commands for running manually'
exit

# Login to server once created
# This assumes Mac or Linux but Windows with Windows Subsystem for Linux (WSL)
# should also work. When ussing `ssh` the location will vary on your computer once setup.
# Also the IP address will change based on setup from cloud host.
#
# ssh -i /Users/conrad/Documents/Code/keys/LightsailDefaultPrivateKey-us-west-2.pem ubuntu@0.0.0.0

# Install nginx and PHP with FastSitePHP Starter Site
wget https://www.fastsitephp.com/downloads/create-fast-site.sh
sudo bash create-fast-site.sh -n

# Install FastSitePHP Playground
# This script overwrites the Starter Site and moves the public folder from
# [/var/www/html] to [/var/www/fastsitephp-playground/public]. On the main server
# multiple sites so the same path is used on this setup. Commands below do not
# take effect until the full server setup is handled because the main [html]
# directory will be deleted so this would cause errors for nginx.

sudo mkdir /var/www/fastsitephp-playground
cd /var/www
sudo mv -t fastsitephp-playground app app_data scripts vendor
cd ~
sudo chown ubuntu:www-data -R /var/www
sudo chmod 0775 -R /var/www

wget https://raw.githubusercontent.com/fastsitephp/playground/master/scripts/sync-server-from-github.sh
bash sync-server-from-github.sh

php /var/www/fastsitephp-playground/scripts/install.php
cp /var/www/fastsitephp-playground/scripts/fast_autoloader.php /var/www/fastsitephp-playground/vendor/autoload.php
mkdir /var/www/fastsitephp-playground/public/sites

# Setup custom build of PHP with Apache for FastSitePHP Playground
which php
# This is the active version of PHP prior to these commands
# Output: /usr/bin/php
sudo apt install -y apache2 apache2-dev libxml2-dev
wget https://www.php.net/distributions/php-7.4.27.tar.bz2
tar xjf php-7.4.27.tar.bz2
wget https://fastsitephp.s3-us-west-1.amazonaws.com/playground/php-7.4.27/file.h
wget https://fastsitephp.s3-us-west-1.amazonaws.com/playground/php-7.4.27/file.c
mv file.h ~/php-7.4.27/ext/standard/file.h
mv file.c ~/php-7.4.27/ext/standard/file.c
cd php-7.4.27
./configure --with-apxs2=/usr/bin/apxs --disable-all --enable-json --enable-filter --enable-ctype --enable-opcache
# run `make` - this is expected to take several minutes once `wait` is called on most servers
make > make.log 2>&1 &
wait
cat make.log
# At the end of the file look for:
#    Build complete.
#    Don't forget to run 'make test'.
#
sudo make install
# After PHP is built and installed confirm the path is changed
which php
# Output: /usr/local/bin/php
# Configure PHP and Apache
cd ..
wget https://fastsitephp.s3-us-west-1.amazonaws.com/playground/php-8.1.1/php.ini-production
sudo mv php.ini-production /usr/local/lib/php.ini
# Change Apache to use prefork (required after PHP is enabled otherwise Apache won't start)
sudo a2dismod mpm_event
sudo a2enmod mpm_prefork
printf "<FilesMatch \\.php$>\n    SetHandler application/x-httpd-php\n</FilesMatch>\n" > php.conf
sudo mv php.conf /etc/apache2/mods-enabled/php.conf
# cat /etc/apache2/mods-enabled/php.conf
# cat /etc/apache2/mods-available/deflate.conf
sudo sed -i '/<IfModule mod_filter.c>/a \\t\tAddOutputFilterByType DEFLATE application\/json' /etc/apache2/mods-available/deflate.conf
# cat /etc/apache2/mods-available/deflate.conf
# Edit Apache Config
sudo nano /etc/apache2/apache2.conf
#
# Under:
#     <Directory /var/www/>
# Add:
#     FallbackResource /index.php
# And Change:
#     AllowOverride None
# To:
#     AllowOverride All
#
sudo nano /etc/apache2/ports.conf
# Change:
#   Listen 80
# To:
#   Listen 8080
sudo nano /etc/apache2/sites-available/000-default.conf
# Change:
#   <VirtualHost *:80>
#       DocumentRoot /var/www/html
# To:
#   <VirtualHost 127.0.0.1:8080>
#       DocumentRoot /var/www/fastsitephp-playground/public
#
# Delete downloaded make files
rm php-7.4.27.tar.bz2
sudo rm -r php-7.4.27
# Restart Apache
sudo service apache2 restart

# Setup a Cron Job using sudo to check for and delete expired playground sites.
# Runs once per minute, if not using [sudo] then sites will end up not being deleted.
sudo crontab -e
# Enter [1] for nano, and add the following after header comments:
* * * * * /usr/bin/php /var/www/scripts/delete-expired-sites.php > /var/www/app_data/last-cron-job.txt 2>&1

# Point ngnix to the Apache site running at 127.0.0.1:8080
nano nginx-config.txt
# Copy and Paste file contents from [nginx-config.txt] then save
sudo mv nginx-config.txt /etc/nginx/sites-enabled/fastsitephp

# Cleanup to remove un-used folders and files
sudo rm -r /var/www/html
ls /var/www
# Only [fastsitephp-playground] should show
pwd
# Should show: /home/ubuntu
rm create-fast-site.sh sync-server-from-github.sh
ls
# No files should exist

# Reset permissions after all files and folders are created
sudo chown ubuntu:www-data -R /var/www
sudo chmod 0775 -R /var/www

# Reload nginx (or skip and reboot below)
sudo systemctl reload nginx

# Restart the server and make sure everything works after a reboot
sudo reboot

# ----------------------------------------
# Testing and Usage
# ----------------------------------------
# - View Site by the IP: http://{ip}/
# - It will redirect to https://www.fastsitephp.com/en/playground
# - If the main page shows the site should be ready for real-world usage.
#   Bascially it will allow users to create custom PHP code on the server,
#   but any user code should not take the server down. This is not guaranteed
#   rather on the limited audience for a site it should not cause problems for
#   for small sites.
# - Due to issues with HTTP and HTTPS the brower will not allow a HTTP site by IP only
#   so the playground UI must run local. Alternatively HTTPS can be setup with a custom
#   host/URL on the playround server and it will work to use DevTools to modify the source.
# - With FastSitePHP copied locally edit [~/website/public/js/playground.js]
#   Line 27 `urlRoot: 'https://playground.fastsitephp.com/',` with the new URL
# - Run local build of FastSitePHP and you can test the new playground server.
#   See the readme and docs on how to run FastSitePHP. The setup is quick and simple.
# - Try the site and verify it works and shows the installed version of PHP.
# - Copy and paste contents from the following PHP server side scripts to verify errors:
#   scripts/app-error-testing.php
#   scripts/app-error-testing-2.php
