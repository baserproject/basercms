<?php
Configure::write('Security.salt', 'Vgf9TRDQmIA38NMDSJj5CBmPiJbZSS5DYbDISUc5');
Configure::write('Security.cipherSeed', '13708245764096601687371074541');
Configure::write('Cache.disable', false);
Configure::write('Cache.check', true);
Configure::write('Session.save', 'session');
Configure::write('BcEnv.siteUrl', 'http://basercms-contents.localhost:8888/');
Configure::write('BcEnv.sslUrl', '');
Configure::write('BcApp.adminSsl', false);
Configure::write('BcApp.mobile', false);
Configure::write('BcApp.smartphone', false);
Cache::config('default', array('engine' => 'File'));
Configure::write('debug', 0);