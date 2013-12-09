<?php

/* SVN FILE: $Id$ */
/**
 * セッション設定
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
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
	$timeout = 60 * 24; // １日
/**
 * ブラウザを閉じた後のセッションの有効期限
 * デフォルト：７日
 */
	$cookieTimeout = $timeout * 60 * 7;
/**
 * 設定
 */
	Configure::write('Session', array_merge(Configure::read('Session'), array(
		'cookie' => 'BASERCMS',
		'timeout' => $timeout,
		'cookieTimeout' => $cookieTimeout,
		'ini' => array(
			'session.serialize_handler' => 'php',
			'session.save_path' => TMP . 'sessions',
			'session.use_cookies' => $useCookies,
			'session.use_trans_sid' => $useTransSid,
			'session.gc_maxlifetime' => $timeout * 60
		)
	)));
}
