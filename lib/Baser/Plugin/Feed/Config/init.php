<?php

/**
 * フィードインストーラー
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
 /**
 * データベース初期化
 */
	$this->Plugin->initDb('plugin', 'Feed', array('dbDataPattern'	=> $dbDataPattern));

/**
 * フィードURLを更新
 */
	$FeedDetail = ClassRegistry::init('Feed.FeedDetail');
	$datas = $FeedDetail->find('all', array('recursive' => -1));
	if($datas) {
		foreach($datas as $data) {
			if($data['FeedDetail']['url'] == 'http://basercms.net/news/index.rss') {
				$data['FeedDetail']['url'] .= '?site=' . siteUrl();
			}
			$FeedDetail->set($data);
			$FeedDetail->save($data);
			break;
		}
	}