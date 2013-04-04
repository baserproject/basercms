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
	if ($iniSet) {
/**
 * 基本設定 
 */
		ini_set('session.serialize_handler', 'php');
		ini_set('session.name', Configure::read('Session.cookie'));
		ini_set('session.cookie_path', $this->path);
		ini_set('session.auto_start', 0);
		ini_set('session.save_path', TMP . 'sessions');

/**
 * モバイル設定 
 */
		$agentAgents = Configure::read('BcAgent.mobile.agents');
		$agentAgents = implode('||', $agentAgents);
		$agentAgents = preg_quote($agentAgents, '/');
		$regex = '/'.str_replace('\|\|', '|', $agentAgents).'/i';
		if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
			ini_set('session.use_cookies', 0);
			if(Configure::read('BcAgent.mobile.sessionId')) {
				ini_set('session.use_trans_sid', 1);
			} else {
				ini_set('session.use_trans_sid', 0);
			}
		} else {
			ini_set('session.use_cookies', 1);
			ini_set('session.use_trans_sid', 0);
			ini_set('url_rewriter.tags', '');
		}
/**
 * ブラウザを閉じた後のセッションの有効期限
 * デフォルト：７日
 */
		ini_set('session.cookie_lifetime', $this->cookieLifeTime);
/**
 * ブラウザを開いた状態においてセッションが切れる条件
 * session.gc_maxlifetime で設定された秒数ごとに一定の確率ごとにセッションが切れる
 * 確率は、session.gc_probability で設定する。確実にタイムアウトさせたい場合は、100を設定する。
 * デフォルト：１日１回、100分の１の確率でセッションが切れる
 */
		$sessionTimeout = Security::inactiveMins() * Configure::read('Session.timeout');
		ini_set('session.gc_maxlifetime', $sessionTimeout);
		//ini_set('session.gc_probability', 1);
	}
}
