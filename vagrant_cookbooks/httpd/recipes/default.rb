packages = %w{httpd httpd-devel mod_ssl php php-cli php-pear php-pdo php-mysql php-pgsql php-sqlite php-curl php-gd php-mbstring php-xml php-xmlrpc php-dom php-intl php-mcrypt php-pecl-xdebug phpMyAdmin phpPgAdmin}
packages.each do |packagename|
  package packagename do
    action :install
    options "--enablerepo=remi-php56,remi"
  end
end

service "httpd" do
  supports :status => true, :restart => true, :reload => true
  action [:enable, :start]
end

template "/etc/httpd/conf/httpd.conf" do
  source "httpd.conf.erb"
  owner "root"
  group "root"
  mode 0644
  notifies :reload, 'service[httpd]', :immediately
end

template "/etc/php.ini" do
  source "php.ini.erb"
  owner "root"
  group "root"
  mode 0644
  notifies :reload, 'service[httpd]'
end

template "/etc/httpd/conf.d/phpMyAdmin.conf" do
  source "phpMyAdmin.conf.erb"
  owner "root"
  group "root"
  mode 0644
  notifies :reload, 'service[httpd]'
end

template "/etc/httpd/conf.d/phpPgAdmin.conf" do
  source "phpPgAdmin.conf.erb"
  owner "vagrant"
  group "vagrant"
  mode 0644
  notifies :reload, 'service[httpd]'
end

template "/etc/phpMyAdmin/config.inc.php" do
  source "phpMyAdmin-config.inc.php.erb"
  owner "vagrant"
  group "vagrant"
  mode 0644
end

directory '/etc/phpMyAdmin' do
    owner 'vagrant'
    group 'vagrant'
    mode 0755
    recursive true
end

template "/etc/phpPgAdmin/config.inc.php" do
  source "phpPgAdmin-config.inc.php.erb"
  owner "vagrant"
  group "vagrant"
  mode 0644
end

directory '/etc/phpPgAdmin' do
    owner 'vagrant'
    group 'vagrant'
    mode 0755
    recursive true
end

template "/etc/php.d/xdebug.ini" do
  source "xdebug.ini.erb"
  owner "root"
  group "root"
  mode 0644
  notifies :reload, 'service[httpd]'
end

directory "/root/cron" do
  owner "root"
  group "root"
  mode 0755
  action :create
end

cookbook_file "/root/cron/cron_httpd.sh" do
  source "cron_httpd.sh"
  owner "root"
  group "root"
  mode 0700
end

cron "cron_httpd" do
  user "root"
  command "/root/cron/cron_httpd.sh"
  action :create
end
