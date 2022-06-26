#
# init.sh
# コンテナの初期化
# /var/www/shared と /var/www/html の同期、マイグレーション、書き込み権限の変更などを行う
# マイグレーションを実行する際、DBの起動より先に実行すると失敗してしまうため sleep で待つようにしている
#

# host tmp and logs
chmod -R 777 /var/www/shared/tmp
chmod -R 777 /var/www/shared/logs

# rcync
rsync -a /var/www/shared/ /var/www/html --exclude='node_modules' --exclude='.git' --exclude='.idea' --exclude='.DS_Store' --exclude='docker' --exclude='config/jwt.key' --exclude='config/jwt.pem'

if [ ! -e '/var/www/shared/docker/inited' ]; then

    # composer
    composer install --no-plugins

    # .env
    cp /var/www/html/config/.env.example /var/www/html/config/.env

    # jwt
    rm /var/www/shared/config/jwt.key # host に存在している場合は、host -> guest へ所有権を同期してしまうため一旦削除
    rm /var/www/html/config/jwt.key
    openssl genrsa -out /var/www/html/config/jwt.key 1024
    openssl rsa -in /var/www/html/config/jwt.key -outform PEM -pubout -out /var/www/html/config/jwt.pem
    chown www-data.www-data /var/www/html/config/jwt.key

    # lsyncd
    mkdir /etc/lsyncd
    cp /var/www/shared/docker/lsyncd/lsyncd.conf.lua /etc/lsyncd/

    # migrations
    TIMES=0
    LIMIT_TIMES=50
    CONNECTED=1
    while [ "$(mysqladmin ping -h bc5-db -uroot -proot)" != "mysqld is alive" ]
    do
        echo "try connect $TIMES times"
        sleep 1
        TIMES=`expr $TIMES + 1`
        if [ $TIMES -eq $LIMIT_TIMES ]; then
            CONNECTED=0
            echo "MySQL timeout."
            break
        fi
    done
    if [ $CONNECTED -eq 1 ]; then
        /var/www/html/bin/cake migrations migrate --plugin BaserCore
        /var/www/html/bin/cake migrations seed --plugin BaserCore
        /var/www/html/bin/cake plugin assets symlink
    else
        echo "Migration failed."
	fi

	# touch installed
    touch /var/www/shared/docker/inited
fi

# lsyncd
service lsyncd start

# guest tmp and logs
chmod -R 777 /var/www/html/tmp
chmod -R 777 /var/www/html/logs

echo "Container setup is complete."
