<?php

/**
 * フィードルーティング定義
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
// Ajax 経由で、/feed/index/x を呼び出す際、cacheを false に設定すると
// /feed/index/x?_=xxxxxxx といった形式に対しリクエストされる事なり、
// CakePHPにおけるプラグインのデフォルトコントローラー機能が正常動作しない為、
// 明示的に定義を記述
$prefix = Configure::read('BcAgent.smartphone.alias');
Router::connect('/feed/index/*', array('plugin' => 'feed', 'controller' => 'feed'));
Router::connect('/feed/ajax/*', array('plugin' => 'feed', 'controller' => 'feed', 'action' => 'ajax'));
Router::connect('/' . $prefix . '/feed/index/*', array('prefix' => 'smartphone', 'plugin' => 'feed', 'controller' => 'feed'));
Router::connect('/' . $prefix . '/feed/ajax/*', array('prefix' => 'smartphone', 'plugin' => 'feed', 'controller' => 'feed', 'action' => 'ajax'));
