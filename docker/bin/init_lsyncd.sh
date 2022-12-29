#
# init_lsyncd.sh
# コンテナの初期化
# /var/www/shared と /var/www/html の同期を行う
# マイグレーションを実行する際、DBの起動より先に実行すると失敗してしまうため sleep で待つようにしている
#

echo "[$(date +"%Y/%m/%d %H:%M:%S")] rsync and lsyncd start."

# rcync
rsync -a /var/www/shared/ /var/www/html --exclude='node_modules' --exclude='.git' --exclude='.idea' --exclude='.DS_Store' --exclude='docker' --exclude='config/jwt.key' --exclude='config/jwt.pem'

if [ ! -e '/var/www/html/docker_inited' ]; then

    # lsyncd
    mkdir /etc/lsyncd
    cp /var/www/shared/docker/lsyncd/lsyncd.conf.lua /etc/lsyncd/

fi

# lsyncd
service lsyncd start

echo "[$(date +"%Y/%m/%d %H:%M:%S")] lsyncd started."
