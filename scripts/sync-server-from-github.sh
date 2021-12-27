#!/usr/bin/env bash

# -----------------------------------------------------------------------------
#
#  This is a Bash Script that runs on the production server
#  [playground.fastsitephp.com] and is used to sync the latest changes
#  from GitHub. It runs manually from the author once published changes
#  are confirmed.
#
#  To run:
#      bash /var/www/fastsitephp-playground/scripts/sync-server-from-github.sh
#
#  For testing with [rsync] use [-n = --dry-run]
#  Example:
#      rsync -nrcv --delete ~/playground-master/app/ /var/www/app
#
# -----------------------------------------------------------------------------

wget https://github.com/fastsitephp/playground/archive/master.zip -O ~/master.zip
unzip -q ~/master.zip
rm ~/master.zip
wget https://github.com/fastsitephp/fastsitephp/archive/master.zip -O ~/master.zip
unzip -q ~/master.zip
rm ~/master.zip
rsync -rcv --delete ~/playground-master/app/ /var/www/fastsitephp-playground/app
rsync -rcv --delete ~/playground-master/app_data/template/ /var/www/fastsitephp-playground/app_data/template
rsync -rcv --delete ~/playground-master/scripts/ /var/www/fastsitephp-playground/scripts
rsync -rcv --delete --exclude sites ~/playground-master/public/ /var/www/fastsitephp-playground/public
rsync -rcv --delete ~/fastsitephp-master/src/ /var/www/vendor/fastsitephp/src
rm -r ~/playground-master
rm -r ~/fastsitephp-master
