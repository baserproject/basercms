rsync -av /var/www/shared/ /var/www/html --exclude='node_modules' --exclude='app/tmp' --exclude='app/tmp/files'  --exclude='app/View/Pages' --exclude='.git' --exclude='.idea' --exclude='.DS_Store' --exclude='.vagrant' --exclude='docker'
if [ ! -d '/var/www/html/app/tmp' ]; then
    mkdir /var/www/html/app/tmp
fi
if [ ! -d '/var/www/html/app/View/Pages' ]; then
    mkdir /var/www/html/app/View/Pages
fi
if [ ! -d '/var/www/html/app/webroot/files' ]; then
    mkdir /var/www/html/app/webroot/files
fi
chmod -R 777 /var/www/html/app/tmp
chmod -R 777 /var/www/html/app/View/Pages
chmod -R 777 /var/www/html/app/webroot/files
if [ ! -e '/var/www/html/docker/check' ]; then
    touch /var/www/shared/docker/check
    echo "container setup is complete"
fi
service lsyncd start
