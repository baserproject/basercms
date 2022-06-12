#
# init.sh
# コンテナの初期化
#
rsync -av /var/www/shared/ /var/www/html --exclude='node_modules' --exclude='.git' --exclude='.idea' --exclude='.DS_Store' --exclude='.vagrant' --exclude='docker'

if [ ! -e '/var/www/shared/docker/check' ]; then
    chmod -R 777 /var/www/shared/app/tmp/*
    mkdir /var/www/html/app/tmp
    mkdir /var/www/html/app/View/Pages
    mkdir /var/www/html/app/webroot/files
    mkdir /var/www/html/docker
    chmod -R 777 /var/www/html/app/Config
    chmod -R 777 /var/www/html/app/tmp
    chmod -R 777 /var/www/html/app/View/Pages
    chmod -R 777 /var/www/html/app/webroot/files
    chmod -R 777 /var/www/html/app/webroot/img
    chmod -R 777 /var/www/html/app/webroot/js
    chmod -R 777 /var/www/html/app/webroot/css
    chmod -R 777 /var/www/html/app/webroot/theme
    touch /var/www/shared/docker/check
    mkdir /etc/lsyncd
    cp /var/www/shared/docker/lsyncd/lsyncd.conf.lua /etc/lsyncd/
fi

service lsyncd start
echo "Container setup is complete."
