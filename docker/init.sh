#
# init.sh
# コンテナの初期化
# /var/www/shared と /var/www/html の同期、マイグレーション、書き込み権限の変更などを行う
# マイグレーションを実行する際、DBの起動より先に実行すると失敗してしまうため sleep で待つようにしている
#
rsync -a /var/www/shared/ /var/www/html --exclude='node_modules' --exclude='tmp' --exclude='logs' --exclude='.git' --exclude='.idea' --exclude='.DS_Store' --exclude='docker' --exclude='config/jwt.key' --exclude='config/jwt.pem'
if [ ! -d '/var/www/html/tmp' ]; then
    mkdir /var/www/html/tmp
fi
if [ ! -d '/var/www/html/logs' ]; then
    mkdir /var/www/html/logs
fi

if [ ! -e '/var/www/shared/docker/check' ]; then
    rm -rf /var/www/shared/vendor/*
    composer install --no-plugins
    cp /var/www/html/config/.env.example /var/www/html/config/.env
    openssl genrsa -out /var/www/html/config/jwt.key 1024
    openssl rsa -in /var/www/html/config/jwt.key -outform PEM -pubout -out /var/www/html/config/jwt.pem
    chown www-data.www-data /var/www/html/config/jwt.key
    mkdir /etc/lsyncd
    cp /var/www/shared/docker/lsyncd/lsyncd.conf.lua /etc/lsyncd/
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
        rm -rf /var/www/html/tmp/cache
    else
        echo "Migration failed."
	fi
    touch /var/www/shared/docker/check
fi

service lsyncd start
echo "Container setup is complete."
