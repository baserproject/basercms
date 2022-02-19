if [ ! -e '/var/www/html/docker/check' ]; then
    touch /var/www/html/docker/check
    composer install --no-plugins
    cp /var/www/html/config/.env.example /var/www/html/config/.env
    sleep 10
	/var/www/html/bin/cake migrations migrate --plugin BaserCore
	/var/www/html/bin/cake migrations seed --plugin BaserCore
	/var/www/html/bin/cake migrations migrate --plugin BcPage
	/var/www/html/bin/cake migrations seed --plugin BcPage
	/var/www/html/bin/cake plugin assets symlink
    openssl genrsa -out /var/www/html/config/jwt.key 1024
    openssl rsa -in /var/www/html/config/jwt.key -outform PEM -pubout -out /var/www/html/config/jwt.pem
    echo "container setup is complete"
fi
