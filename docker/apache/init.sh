if [ ! -e '/var/www/html/docker/check' ]; then
    rm -rf /var/www/html/tmp
    rm -rf /var/www/html/logs
	rm /var/www/html/config/jwt.key
	rm /var/www/html/config/jwt.pem
    composer install --no-plugins
    cp /var/www/html/config/.env.example /var/www/html/config/.env
    sleep 10
	/var/www/html/bin/cake migrations migrate --plugin BaserCore
	/var/www/html/bin/cake migrations seed --plugin BaserCore
	/var/www/html/bin/cake plugin assets symlink
    openssl genrsa -out /var/www/html/config/jwt.key 1024
    openssl rsa -in /var/www/html/config/jwt.key -outform PEM -pubout -out /var/www/html/config/jwt.pem
    chown www-data.www-data /var/www/html/config/jwt.key
    touch /var/www/html/docker/check
    chmod 777 -R /var/www/html/tmp
    chmod 777 -R /var/www/html/logs
    echo "container setup is complete"
fi
