<?php

/* SVN FILE: $Id$ */
/**
 * フィードルーティング定義
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
// Ajax 経由で、/feed/index/x を呼び出す際、cacheを false に設定すると
// /feed/index/x?_=xxxxxxx といった形式に対しリクエストされる事なり、
// CakePHPにおけるプラグインのデフォルトコントローラー機能が正常動作しない為、
// 明示的に定義を記述
$prefix = Configure::read('BcAgent.smartphone.alias');
Router::connect('/feed/index/*', array('plugin' => 'feed', 'controller' => 'feed'));
Router::connect('/feed/ajax/*', array('plugin' => 'feed', 'controller' => 'feed', 'action' => 'ajax'));
Router::connect('/' . $prefix . '/feed/index/*', array('smartphone' => true, 'plugin' => 'feed', 'controller' => 'feed'));
Router::connect('/' . $prefix . '/feed/ajax/*', array('smartphone' => true, 'plugin' => 'feed', 'controller' => 'feed', 'action' => 'ajax'));
