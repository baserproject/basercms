service "iptables" do
  supports :status => true, :restart => true, :reload => true
  action [:enable, :start]
end

template "/etc/sysconfig/iptables" do
  source "iptables.erb"
  owner "root"
  group "root"
  mode 0600
  notifies :restart, 'service[iptables]'
end
