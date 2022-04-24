#
# init.sh
# コンテナの初期化
#
rsync -av /var/www/shared/ /var/www/html --exclude='node_modules' --exclude='app/tmp' --exclude='app/webroot/files'  --exclude='app/View/Pages' --exclude='.git' --exclude='.idea' --exclude='.DS_Store' --exclude='.vagrant' --exclude='docker'
if [ ! -d '/var/www/html/app/tmp' ]; then
    mkdir /var/www/html/app/tmp
fi
if [ ! -d '/var/www/html/app/View/Pages' ]; then
    mkdir /var/www/html/app/View/Pages
fi
if [ ! -d '/var/www/html/app/webroot/files' ]; then
    mkdir /var/www/html/app/webroot/files
fi
if [ ! -d '/var/www/html/docker' ]; then
    mkdir /var/www/html/docker
fi

if [ ! -e '/var/www/shared/docker/check' ]; then
    chmod -R 777 /var/www/html/app/Config
    chmod -R 777 /var/www/html/app/tmp
    chmod -R 777 /var/www/html/app/View/Pages
    chmod -R 777 /var/www/html/app/webroot/files
    chmod -R 777 /var/www/html/app/webroot/img
    chmod -R 777 /var/www/html/app/webroot/js
    chmod -R 777 /var/www/html/app/webroot/css
    chmod -R 777 /var/www/html/app/webroot/theme
    mkdir /etc/lsyncd
    cp /var/www/shared/docker/lsyncd/lsyncd.conf.lua /etc/lsyncd/
    touch /var/www/shared/docker/check
fi

service lsyncd start
echo "Container setup is complete."
