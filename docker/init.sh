#
# init.sh
# コンテナの初期化
# マイグレーション、書き込み権限の変更などを行う
# マイグレーションを実行する際、DBの起動より先に実行すると失敗してしまうため sleep で待つようにしている
#

echo "[$(date +"%Y/%m/%d %H:%M:%S")] Init Container start."

# msmtprc
# ブラウザでインストールする場合に、ここでインストール処理を実行させないため、docker_inited を配置してからコンテナを起動する必要がある。
# その場合でも、msmtprc をコピーする必要があるので、ここにコピー処理を記述するが、
# baserCMSのコマンドインストールが完了したら、インストールを test.yml に移し
# docker_inited がない場合だけ実行するように変更する
cp /var/www/html/docker/msmtp/msmtprc /etc/msmtprc

if [ ! -e '/var/www/html/docker_inited' ]; then

    # composer
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] composer start."
    composer install --no-plugins

    # .env
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] create .env."
    cp /var/www/html/config/.env.example /var/www/html/config/.env

    # bashrc
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] Add Path to Environment."
    echo "export PATH=$PATH:/var/www/html/bin:/var/www/html/vendor/bin" >> ~/.bashrc

    # jwt
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] Create JWT key."
    rm /var/www/html/config/jwt.key
    rm /var/www/html/config/jwt.pem
    openssl genrsa -out /var/www/html/config/jwt.key 1024
    openssl rsa -in /var/www/html/config/jwt.key -outform PEM -pubout -out /var/www/html/config/jwt.pem
    chown www-data.www-data /var/www/html/config/jwt.key

    # Guest tmp and logs
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] Change Mode tmp and logs"
    chmod -R 777 /var/www/html/tmp
    chmod -R 777 /var/www/html/logs
    chmod 777 /var/www/html/plugins

    # Setup install setting
    cp /var/www/html/config/test_install.php /var/www/html/config/install.php

    # Migrations
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] Migration start."
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
        mysql -h bc5-db -uroot -proot basercms -N -e 'show tables' | while read table; do mysql -h bc5-db -uroot -proot -e "drop table $table" basercms; done
        /var/www/html/bin/cake migrations migrate --plugin BaserCore
        /var/www/html/bin/cake migrations seed --plugin BaserCore
        /var/www/html/bin/cake migrations migrate --plugin BcBlog
        /var/www/html/bin/cake migrations seed --plugin BcBlog
        /var/www/html/bin/cake migrations migrate --plugin BcSearchIndex
        /var/www/html/bin/cake migrations seed --plugin BcSearchIndex
        /var/www/html/bin/cake migrations migrate --plugin BcContentLink
        /var/www/html/bin/cake migrations seed --plugin BcContentLink
        /var/www/html/bin/cake migrations migrate --plugin BcMail
        /var/www/html/bin/cake migrations seed --plugin BcMail
        /var/www/html/bin/cake migrations migrate --plugin BcWidgetArea
        /var/www/html/bin/cake migrations seed --plugin BcWidgetArea
        /var/www/html/bin/cake plugin assets symlink
    else
        echo "[$(date +"%Y/%m/%d %H:%M:%S")] Migration failed."
	fi

    # Clear cache
    /var/www/html/bin/cake cache clear_all

	# Touch installed
    touch /var/www/html/docker_inited

fi

echo "[$(date +"%Y/%m/%d %H:%M:%S")] Container setup is complete."
