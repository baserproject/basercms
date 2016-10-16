packages = %w{postfix dovecot}
packages.each do |packagename|
  package packagename do
    action :install
  end
end

service "postfix" do
  supports :status => true, :restart => true, :reload => true
  action [:enable, :start]
end

service "dovecot" do
  supports :status => true, :restart => true, :reload => true
  action [:enable, :start]
end

template "/etc/postfix/main.cf" do
  source "main.cf.erb"
  owner "root"
  group "root"
  mode 0644
  notifies :restart, 'service[postfix]'
end

cookbook_file "/etc/dovecot/dovecot.conf" do
  source "dovecot.conf"
  owner "root"
  group "root"
  mode 0644
  notifies :reload, 'service[dovecot]'
end

cookbook_file "/etc/dovecot/conf.d/10-mail.conf" do
  source "10-mail.conf"
  owner "root"
  group "root"
  mode 0644
  notifies :reload, 'service[dovecot]'
end

cookbook_file "/etc/dovecot/conf.d/10-auth.conf" do
  source "10-auth.conf"
  owner "root"
  group "root"
  mode 0644
  notifies :reload, 'service[dovecot]'
end

cookbook_file "/etc/dovecot/conf.d/10-ssl.conf" do
  source "10-ssl.conf"
  owner "root"
  group "root"
  mode 0644
  notifies :reload, 'service[dovecot]'
end
