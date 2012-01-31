<?php
if (empty($_SESSION)) {
	if ($iniSet) {
		if(Configure::read('AgentPrefix.currentAgent') == 'mobile') {
			ini_set('session.use_cookies', 0);
			ini_set('session.use_trans_sid', 1);
		} else {
			ini_set('session.use_trans_sid', 0);
		}
		ini_set('session.name', Configure::read('Session.cookie'));
		ini_set('session.cookie_lifetime', $this->cookieLifeTime);
		ini_set('session.cookie_path', $this->path);
	}
}
?>