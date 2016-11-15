cookbook_file "/etc/sysconfig/clock" do
    source "clock.conf"
    owner "root"
    group "root"
    mode 0644
    notifies :run, 'execute[centos-timezone-copy]'
end

execute 'centos-timezone-copy' do
    command <<-EOH
        source /etc/sysconfig/clock
        cp -pf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
    EOH
    action :nothing
end

execute 'resolv.conf' do
    command "echo 'options single-request-reopen' >> /etc/resolv.conf"
    not_if "cat /etc/resolv.conf | grep 'options single-request-reopen'"
end

user node['centos']['testuser_name'] do
  comment node['centos']['testuser_name']
  home "/home/" + node['centos']['testuser_name']
  shell "/bin/bash"
  password node['centos']['testuser_password']
  supports :manage_home => true
  action :create
end

