if [ ! -e '/var/www/html/docker/check' ]; then
    touch /var/www/html/docker/check
    composer install --no-plugins
    cp /var/www/html/config/.env.example /var/www/html/config/.env
    sleep 10
	bin/cake migrations migrate --plugin BaserCore
	bin/cake migrations seed --plugin BaserCore
	bin/cake migrations migrate --plugin BcPage
	bin/cake migrations seed --plugin BcPage
	bin/cake plugin assets symlink
    openssl genrsa -out config/jwt.key 1024
    openssl rsa -in config/jwt.key -outform PEM -pubout -out config/jwt.pem
    echo "container setup is complete"
fi
