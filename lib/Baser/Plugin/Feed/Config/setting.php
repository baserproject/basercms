<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * システムナビ
 */
$config['BcApp.adminNavi.feed'] = [
	'name' => __d('baser', 'フィードプラグイン'),
	'contents' => [
		['name' => __d('baser', 'フィード設定一覧'), 'url' => ['admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'index']],
		['name' => __d('baser', 'フィード設定登録'), 'url' => ['admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'add']],
		['name' => __d('baser', 'フィードキャッシュ削除'), 'url' => ['admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'delete_cache'], 'options' => ['confirm' => __d('baser', 'フィードキャッシュを削除します。いいですか？')]]
	]
];
