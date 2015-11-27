package "mysql-server" do
  action :install
  notifies :run, 'execute[mysql-set-password]'
  notifies :run, 'execute[basercms-mysql-create-db]'
  notifies :run, 'execute[mysql-set-timezone]'
end

service "mysqld" do
  supports :status => true, :restart => true, :reload => false
  action [:enable, :start]
end

template "/etc/my.cnf" do
  source "my.cnf.erb"
  owner "root"
  group "root"
  mode 0644
  notifies :restart, 'service[mysqld]', :immediately
end

execute 'mysql-set-password' do
  command <<-EOH
    mysql -u root -e "GRANT ALL PRIVILEGES ON *.* to 'root'@'%' IDENTIFIED BY '#{node['mysql']['password']}' WITH GRANT OPTION;"
    mysql -u root -e "FLUSH PRIVILEGES;"
    mysql -u root -e "SET PASSWORD FOR 'root'@'localhost'=PASSWORD('#{node['mysql']['password']}');"
  EOH
  action :nothing
end

execute 'mysql-set-timezone' do
  command <<-EOH
    /usr/bin/mysql_tzinfo_to_sql /usr/share/zoneinfo | /usr/bin/mysql -u root mysql -p#{node['mysql']['password']}
  EOH
  action :nothing
end
