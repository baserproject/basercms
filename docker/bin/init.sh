#
# init.sh
# コンテナの初期化
# DBに接続する際、DBの起動より先に実行すると失敗してしまうため sleep で待つようにしている
#

echo "[$(date +"%Y/%m/%d %H:%M:%S")] Init Container start."

# OPcache
# 設定値は docker/php/opcache.ini（conf.d/99-opcache.ini にマウント）で指定している。
# 拡張自体はイメージによって組み込み済み（php8.5 等）と .so 提供（php8.1〜8.4 等）があるため、
# 未ロードのときだけ docker-php-ext-enable で有効化する（毎回の起動時に確認する）。
if ! php -m | grep -q "Zend OPcache"; then
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] Enable OPcache."
    docker-php-ext-enable opcache
fi

if [ ! -e '/var/www/html/docker_inited' ]; then

    # init baserCMS
    # .env について、ライブラリのインストーラーでコピーする仕様になっているが、
    # GitHubActions のユニットテストの際には、composer コマンドでインストールするため、
    # ここでコピーする
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] init baserCMS"
    cp /var/www/html/config/.env.example /var/www/html/config/.env
    rm /var/www/html/config/install.php

    # msmtprc
    cp /var/www/html/docker/msmtp/msmtprc /etc/msmtprc

    # bashrc
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] Add Path to Environment."
    echo "export PATH=$PATH:/var/www/html/bin:/var/www/html/vendor/bin" >> ~/.bashrc

    # database
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] Migration start."
    TIMES=0
    LIMIT_TIMES=50
    CONNECTED=1
    while [ "$(mysqladmin ping -h bc-db -uroot -proot)" != "mysqld is alive" ]
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
        mysql -h bc-db -uroot -proot basercms -N -e 'show tables' | while read table; do mysql -h bc-db -uroot -proot -e "drop table $table" basercms; done
    else
        echo "[$(date +"%Y/%m/%d %H:%M:%S")] Migration failed."
	fi

    # Clear cache
    /var/www/html/bin/cake cache clear_all

	# Touch installed
    touch /var/www/html/docker_inited

fi

echo "[$(date +"%Y/%m/%d %H:%M:%S")] Container setup is complete."
