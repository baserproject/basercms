execute "chmod-tmp" do
  command "chmod -Rf 0777 #{node['httpd']['root']}/app/tmp"
end

execute "change-php-session-dir-permission" do
  user "root"
  command "chmod 0777 /var/lib/php/session"
end


execute "basercms-mysql-create-db" do
  command "mysql -u root -p#{node['mysql']['password']} --execute \"create database if not exists #{node['basercms']['database']}\""
  action :nothing
end

execute "basercms-postgres-create-db" do
  user "postgres"
  command "createdb #{node['basercms']['database']} -E UTF8 --locale ja_JP.UTF8 -T template0"
  action :nothing
end
