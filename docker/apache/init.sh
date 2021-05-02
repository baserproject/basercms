if [ ! -e '/check' ]; then
    touch /check
    composer install --no-plugins
    cp /var/www/html/config/.env.example /var/www/html/config/.env
	bin/cake migrations migrate --plugin baser-core
    echo "container setup is complete"
fi
