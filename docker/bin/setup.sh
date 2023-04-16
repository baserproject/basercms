#
# setup.sh
# 主に書き込み権限の変更を実行
#

# Guest tmp and logs
chmod -R 777 /var/www/html/tmp
chmod -R 777 /var/www/html/logs
chmod 777 /var/www/html/plugins
chmod 777 /var/www/html/
chmod 777 /var/www/html/composer
chmod 777 /var/www/html/vendor
chmod 777 /var/www/html/config
chmod 777 /var/www/html/webroot/files
chmod 777 /var/www/html/db
