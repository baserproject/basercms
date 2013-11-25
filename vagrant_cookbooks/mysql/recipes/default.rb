package "mysql-server" do
  action :install
  notifies :run, 'execute[mysql-set-password]'
  notifies :run, 'execute[basercms-mysql-create-db]'
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
  command "mysql -u root -e \"SET PASSWORD FOR root@localhost=PASSWORD('#{node['mysql']['password']}');\""
  action :nothing
end
