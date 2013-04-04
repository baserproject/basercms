<?php
/* SVN FILE: $Id$ */
/**
 * フィード設定
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
/**
 * システムナビ
 */
	$config['BcApp.adminNavi.feed'] = array(
			'name'		=> 'フィードプラグイン',
			'contents'	=> array(
				array('name' => 'フィード設定一覧',		'url' => array('admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'index')),
				array('name' => 'フィード設定登録',		'url' => array('admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'add')),
				array('name' => 'フィードキャッシュ削除',	'url' => array('admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'delete_cache'))
		)
	);

