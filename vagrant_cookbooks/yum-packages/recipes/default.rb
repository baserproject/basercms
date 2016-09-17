yum_repository 'epel' do
  description 'Extra Packages for Enterprise Linux'
  mirrorlist 'http://mirrors.fedoraproject.org/mirrorlist?repo=epel-6&arch=$basearch'
  gpgkey 'http://dl.fedoraproject.org/pub/epel/RPM-GPG-KEY-EPEL-6'
  action :create
end

yum_repository 'remi' do
  description 'Remis RPM repository for Enterprise Linux 6'
  baseurl 'http://rpms.famillecollet.com/enterprise/6/remi/x86_64/'
  gpgkey 'http://rpms.famillecollet.com/RPM-GPG-KEY-remi'
  fastestmirror_enabled true
  action :create
end

yum_repository 'remi-php56' do
  description 'Remis PHP5.6 RPM repository for Enterprise Linux 6'
  baseurl 'http://rpms.famillecollet.com/enterprise/6/php56/x86_64/'
  gpgkey 'http://rpms.famillecollet.com/RPM-GPG-KEY-remi'
  fastestmirror_enabled true
  action :create
end

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
