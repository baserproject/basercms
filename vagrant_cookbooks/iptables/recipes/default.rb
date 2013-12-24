service "iptables" do
  supports :status => true, :restart => true, :reload => true
  action [:disable, :stop]
end
