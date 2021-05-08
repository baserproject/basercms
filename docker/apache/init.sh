if [ ! -e '/check' ]; then
    touch /check
    composer install --no-plugins
    cp /var/www/html/config/.env.example /var/www/html/config/.env
    sleep 10
	bin/cake migrations migrate --plugin BaserCore
	bin/cake migrations seed --plugin BaserCore
	bin/cake plugin assets symlink
    echo "container setup is complete"
fi
