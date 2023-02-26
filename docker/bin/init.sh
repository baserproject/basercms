#
# init.sh
# コンテナの初期化
# DBに接続する際、DBの起動より先に実行すると失敗してしまうため sleep で待つようにしている
#

echo "[$(date +"%Y/%m/%d %H:%M:%S")] Init Container start."

if [ ! -e '/var/www/html/docker_inited' ]; then

    # .env
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] create .env."
    cp /var/www/html/config/.env.example /var/www/html/config/.env

    # init baserCMS
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
    else
        echo "[$(date +"%Y/%m/%d %H:%M:%S")] Migration failed."
	fi

    # Clear cache
    /var/www/html/bin/cake cache clear_all

	# Touch installed
    touch /var/www/html/docker_inited

fi

echo "[$(date +"%Y/%m/%d %H:%M:%S")] Container setup is complete."
