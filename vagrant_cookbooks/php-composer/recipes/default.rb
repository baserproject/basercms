execute "composer-install" do
	command "curl -sS https://getcomposer.org/installer | php ;mv composer.phar /usr/local/bin/composer"
	not_if { ::File.exists?("/usr/local/bin/composer")}
end

execute "composer-execute" do
	command "composer install"
	cwd "/vagrant/"
	only_if { ::File.exists?("/usr/local/bin/composer")}
end
