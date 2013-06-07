<?php
Configure::write('Security.salt', 'TVajR7LAO2lPEEAShkYI4NcdDb5ZSijB4jLMg9ZV');
Configure::write('Cache.disable', false);
Configure::write('Cache.check', true);
Configure::write('BcEnv.siteUrl', 'http://kumagaku.localhost:8888/');
Configure::write('BcEnv.sslUrl', '');
Configure::write('BcApp.adminSsl', false);
Configure::write('BcApp.mobile', true);
Configure::write('BcApp.smartphone', false);
Cache::config('default', array('engine' => 'File'));
Configure::write('debug', 2);
Configure::write('App.baseUrl', '');
?>