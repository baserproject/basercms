#
# init.sh
# コンテナの初期化
# /var/www/shared と /var/www/html の同期、マイグレーション、書き込み権限の変更などを行う
# マイグレーションを実行する際、DBの起動より先に実行すると失敗してしまうため sleep で待つようにしている
#
rsync -a /var/www/shared/ /var/www/html --exclude='node_modules' --exclude='tmp' --exclude='logs' --exclude='.git' --exclude='.idea' --exclude='.DS_Store' --exclude='docker'

if [ ! -d '/var/www/html/tmp' ]; then
    mkdir /var/www/html/tmp
    mkdir /var/www/html/tmp/cache
    mkdir /var/www/html/tmp/cache/models
    mkdir /var/www/html/tmp/cache/persistent
    mkdir /var/www/html/tmp/cache/views
    mkdir /var/www/html/tmp/sessions
    mkdir /var/www/html/tmp/tests
fi
if [ ! -d '/var/www/html/logs' ]; then
    mkdir /var/www/html/logs
fi
chmod 777 -R /var/www/html/tmp
chmod 777 -R /var/www/html/logs

if [ ! -e '/var/www/shared/docker/check' ]; then
    if [ -e '/var/www/shared/config/jwt.key' ]; then
	    rm /var/www/shared/config/jwt.key
	fi
	if [ -e '/var/www/shared/config/jwt.pem' ]; then
	    rm /var/www/shared/config/jwt.pem
	fi
    composer install --no-plugins
    cp /var/www/html/config/.env.example /var/www/html/config/.env
    sleep 20
	/var/www/html/bin/cake migrations migrate --plugin BaserCore
	/var/www/html/bin/cake migrations seed --plugin BaserCore
	/var/www/html/bin/cake plugin assets symlink
	chmod 777 -R /var/www/html/tmp
	chmod 777 -R /var/www/html/logs
    openssl genrsa -out /var/www/html/config/jwt.key 1024
    openssl rsa -in /var/www/html/config/jwt.key -outform PEM -pubout -out /var/www/html/config/jwt.pem
    chown www-data.www-data /var/www/html/config/jwt.key
    touch /var/www/shared/docker/check
    echo "container setup is complete"
fi

service lsyncd start
