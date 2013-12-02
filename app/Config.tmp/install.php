<?php
Configure::write('Security.salt', 'gY6YhRRC2GIcUd27hFAQ3G4hZUWg5L2bmZ0HHIWm');
Configure::write('Security.cipherSeed', '81352143579318595204784062407');
Configure::write('Cache.disable', false);
Configure::write('Cache.check', true);
Configure::write('Session.save', 'session');
Configure::write('BcEnv.siteUrl', 'http://basercamp.localhost:8888/');
Configure::write('BcEnv.sslUrl', '');
Configure::write('BcApp.adminSsl', false);
Configure::write('BcApp.mobile', true);
Configure::write('BcApp.smartphone', true);
Cache::config('default', array('engine' => 'File'));
Configure::write('debug', 0);