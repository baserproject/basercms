execute "php-pear-phpunit" do
	command <<-EOH
		pear upgrade PEAR
		pear config-set auto_discover 1
		pear install pear.phpunit.de/PHPUnit-3.7.32
		pear install phpunit/PHP_Invoker
	EOH
end