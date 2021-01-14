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

/**
 * システムナビ
 */
$config['BcApp.adminNavigation'] = [
	'Systems' => [
		'Feed' => [
			'title' => __d('baser', 'フィード管理'),
			'type' => 'system',
			'icon' => 'bca-icon--feed',
			'menus' => [
				'FeedConfigs' => [
					'title' => __d('baser', 'フィード設定'),
					'url' => ['admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'index'],
					'currentRegex' => '/(\/feed\/feed_configs\/|\/feed\/feed_details\/)/s'
				],
				'FeedDeleteCache' => [
					'title' => __d('baser', 'フィードキャッシュ削除'),
					'url' => ['admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'delete_cache']
				]
			]]]];
// @deprecated 5.0.0 since 4.2.0 BcApp.adminNavigation の形式に変更
$config['BcApp.adminNavi.feed'] = [
	'name' => __d('baser', 'フィードプラグイン'),
	'contents' => [
		['name' => __d('baser', 'フィード設定一覧'), 'url' => ['admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'index']],
		['name' => __d('baser', 'フィード設定登録'), 'url' => ['admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'add']],
		['name' => __d('baser', 'フィードキャッシュ削除'), 'url' => ['admin' => true, 'plugin' => 'feed', 'controller' => 'feed_configs', 'action' => 'delete_cache'], 'options' => ['confirm' => __d('baser', 'フィードキャッシュを削除します。いいですか？')]]
	]];
