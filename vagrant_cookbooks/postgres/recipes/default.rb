package "postgresql-server" do
  action :install
end

if not File.exists? "/var/lib/pgsql/data/" then
  execute "postgresql-init" do
    command "service postgresql initdb --no-locale -E UTF8"
    notifies :run, 'execute[postgres-set-password]'
    notifies :run, 'execute[basercms-postgres-create-db]'
  end
end

template "/var/lib/pgsql/data/postgresql.conf" do
  source "postgresql.conf.erb"
  owner "postgres"
  group "postgres"
  mode 0600
  notifies :restart, 'service[postgresql]'
end

template "/var/lib/pgsql/data/pg_hba.conf" do
  source "pg_hba.conf.erb"
  owner "postgres"
  group "postgres"
  mode 0600
  notifies :restart, 'service[postgresql]'
end

service "postgresql" do
  supports :status => true, :restart => true, :reload => true
  action [:enable, :start]
end

execute 'postgres-set-password' do
  user "postgres"
  command "psql -c \"alter role postgres with password '#{node['postgres']['password']}';\""
  action :nothing
end
