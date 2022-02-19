if [ ! -e '/var/www/shared/docker/check' ]; then
    if [ -d '/var/www/shared/tmp' ]; then
        rm -rf /var/www/shared/tmp
    fi
    if [ -d '/var/www/shared/logs' ]; then
        rm -rf /var/www/shared/logs
    fi
    if [ -e '/var/www/shared/config/jwt.key' ]; then
	    rm /var/www/shared/config/jwt.key
	fi
	if [ -e '/var/www/shared/config/jwt.pem' ]; then
	    rm /var/www/shared/config/jwt.pem
	fi
    rsync -a /var/www/shared/ /var/www/html --exclude='node_modules' --exclude='tmp' --exclude='.git' --exclude='.idea' --exclude='.DS_Store' --exclude='docker'
    composer install --no-plugins
    cp /var/www/html/config/.env.example /var/www/html/config/.env
    sleep 5
	/var/www/html/bin/cake migrations migrate --plugin BaserCore
	/var/www/html/bin/cake migrations seed --plugin BaserCore
	/var/www/html/bin/cake plugin assets symlink
    openssl genrsa -out /var/www/html/config/jwt.key 1024
    openssl rsa -in /var/www/html/config/jwt.key -outform PEM -pubout -out /var/www/html/config/jwt.pem
    chown www-data.www-data /var/www/html/config/jwt.key
    chmod 777 -R /var/www/html/tmp
    chmod 777 -R /var/www/html/logs
    touch /var/www/shared/docker/check
    echo "container setup is complete"
fi
service lsyncd start
