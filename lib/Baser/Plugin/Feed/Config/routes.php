<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

// Ajax 経由で、/feed/index/x を呼び出す際、cacheを false に設定すると
// /feed/index/x?_=xxxxxxx といった形式に対しリクエストされる事なり、
// CakePHPにおけるプラグインのデフォルトコントローラー機能が正常動作しない為、
// 明示的に定義を記述
Router::connect('/feed/index/*', ['plugin' => 'feed', 'controller' => 'feed']);
Router::connect('/feed/ajax/*', ['plugin' => 'feed', 'controller' => 'feed', 'action' => 'ajax']);
try {
	$Site = ClassRegistry::init('Site');
} catch (Exception $e) {
	return;
}
$prefix = $Site->getPrefix($Site->find('first', ['conditions' => ['name' => 'smartphone'], 'recursive' => -1]));
if ($prefix) {
	Router::connect('/' . $prefix . '/feed/index/*', ['prefix' => 'smartphone', 'plugin' => 'feed', 'controller' => 'feed']);
	Router::connect('/' . $prefix . '/feed/ajax/*', ['prefix' => 'smartphone', 'plugin' => 'feed', 'controller' => 'feed', 'action' => 'ajax']);
}
