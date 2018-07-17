<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
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
 * セッションタイムアウト
 * 《ブラウザを開いた状態においてセッションが切れる条件》
 * session.gc_maxlifetime で設定された秒数ごとに一定の確率ごとにセッションが切れる
 * 確率は、session.gc_probability で設定する。確実にタイムアウトさせたい場合は、100を設定する。
 * デフォルト：１日１回、100分の１の確率でセッションが切れる
 */
	$timeout = 60 * 24;
/**
 * ブラウザを閉じた後のセッションの有効期限
 * デフォルト：１日
 */
	$cookieTimeout = $timeout;
/**
 * 設定
 */
	Configure::write('Session', array_merge(Configure::read('Session'), array(
		'cookie' => 'BASERCMS',
		'timeout' => $timeout,
		'cookieTimeout' => $cookieTimeout,
		'ini' => array(
			'session.serialize_handler' => 'php',
			'session.save_path'			=> TMP . 'sessions',
			'session.use_cookies'		=> $useCookies,
			'session.use_trans_sid'		=> $useTransSid,
			'session.gc_maxlifetime'	=> $timeout * 60,
			'session.gc_divisor'		=> 100,
			'session.gc_probability'	=> 1,
			'session.cookie_secure' => 0
		)
	)));
/*
 * 全てHTTPSでサイトが利用されていたらsession.cookie_secureを1とする
 */
	if (strpos(strtolower(Configure::read('BcEnv.siteUrl')), 'https') === 0) {
		// URL設定がどちらとも同じドメインでかつ、HTTPSで設定されている
		// 異なるサブドメインにてcookie共有を許容しているサイトは対象外
		$siteUrlDomainMatches = null;
		$sslUrlDomainMatches  = null;
		preg_match("/^https:\/\/([a-zA-Z0-9\.-]+)(:\d+|)\//", Configure::read('BcEnv.siteUrl'), $siteUrlDomainMatches);
		preg_match("/^https:\/\/([a-zA-Z0-9\.-]+)(:\d+|)\//", Configure::read('BcEnv.sslUrl'), $sslUrlDomainMatches);
		if (isset($siteUrlDomainMatches[0]) && isset($sslUrlDomainMatches[0]) &&
			$siteUrlDomainMatches[0] == $sslUrlDomainMatches[0]) {
			Configure::write('Session.ini', array_merge(Configure::read('Session.ini'), array(
				'session.cookie_secure' => 1
			)));
		}
	}
}
