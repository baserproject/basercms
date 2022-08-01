<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * セッション設定
 *
 * `/app/Config/setting.php` より先に読み込まれるため
 * `Configure` の値を変更するには、`/app/Config/session.php` で設定する
 */

if (empty($_SESSION)) {

	/**
	 * モバイル設定
	 */
	$agentAgents = Configure::read('BcAgent.mobile.agents');
	$agentAgents = implode('||', $agentAgents);
	$agentAgents = preg_quote($agentAgents, '/');
	$regex = '/' . str_replace('\|\|', '|', $agentAgents) . '/i';
	if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
		$useCookies = 0;
		if (Configure::read('BcAgent.mobile.sessionId')) {
			$useTransSid = 1;
		} else {
			$useTransSid = 0;
		}
	} else {
		$useCookies = 1;
		$useTransSid = 1;
		ini_set('url_rewriter.tags', '');
	}

	/**
	 * Cookie Secure
	 *
	 * 全てHTTPSでサイトが利用されている場合に有効とする
	 * URL設定がどちらとも同じドメインでかつ、 HTTPS で設定されている
	 * 異なるサブドメインにてcookie共有を許容しているサイトは対象外
	 */
	$cookieSecure = 0;
	if (strpos(strtolower(Configure::read('BcEnv.siteUrl')), 'https') === 0) {
		$siteUrlDomainMatches = null;
		$sslUrlDomainMatches = null;
		preg_match("/^https:\/\/([a-zA-Z0-9\.-]+)(:\d+|)\//", Configure::read('BcEnv.siteUrl'), $siteUrlDomainMatches);
		preg_match("/^https:\/\/([a-zA-Z0-9\.-]+)(:\d+|)\//", Configure::read('BcEnv.sslUrl'), $sslUrlDomainMatches);
		if (isset($siteUrlDomainMatches[0]) && isset($sslUrlDomainMatches[0]) &&
			$siteUrlDomainMatches[0] == $sslUrlDomainMatches[0]) {
			Configure::write('Session.ini.session.cookie_secure', 1);
			$cookieSecure = 1;
		}
	}


	Configure::write('Session', array_merge(Configure::read('Session'), [
		'defaults' => 'cake',
		'cookie' => 'BASERCMS',
		'timeout' => 60 * 24 * 2,
		'ini' => [
			'session.serialize_handler' => 'php',
			'session.save_path' => TMP . 'sessions',
			'session.use_cookies' => $useCookies,
			'session.use_trans_sid' => $useTransSid,
			'session.gc_divisor' => 100,
			'session.gc_probability' => 1,
			'session.cookie_secure' => $cookieSecure
		]
	]));

}
