#
# setup.sh
# 主に書き込み権限の変更を実行
#

# マスト
chmod -R 777 /var/www/html/tmp
chmod -R 777 /var/www/html/logs
chmod 777 /var/www/html/webroot/files

# インストーラーを利用する場合に必要
chmod 777 /var/www/html/composer
chmod 777 /var/www/html/config

# アップデーターを利用する場合に必要
chmod 777 /var/www/html/vendor
chmod 666 /var/www/html/composer.json
chmod 666 /var/www/html/composer.lock

# テーマやプラグインを管理画面よりアップロードする場合に必要
chmod 777 /var/www/html/plugins

# Sqlite を利用する場合に必要
chmod 777 /var/www/html/db
