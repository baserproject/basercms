execute "yum" do
  command "yum -y update"
end

packages = %w{ntpdate vim-enhanced git curl gd unzip}
packages.each do |packagename|
  package packagename do
    action :install
  end
end

execute "ntpdate" do
  command "ntpdate -s ntp.nc.u-tokyo.ac.jp"
end
