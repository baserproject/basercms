<?php
if (empty($_SESSION)) {
	if ($iniSet) {
		$agentAgents = Configure::read('BcAgent.mobile.agents');
		$agentAgents = implode('||', $agentAgents);
		$agentAgents = preg_quote($agentAgents, '/');
		$regex = '/'.str_replace('\|\|', '|', $agentAgents).'/i';
		if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
			ini_set('session.use_cookies', 0);
			if(Configure::read('BcAgent.mobile.sessionId')) {
				ini_set('session.use_trans_sid', 1);
			}
		} else {
			ini_set('session.use_trans_sid', 0);
		}
		ini_set('session.name', Configure::read('Session.cookie'));
		ini_set('session.cookie_lifetime', $this->cookieLifeTime);
		ini_set('session.cookie_path', $this->path);
	}
}
?>