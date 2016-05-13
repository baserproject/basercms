<?php

/**
 * ブログインストーラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * データベース初期化
 */
	$this->Plugin->initDb('plugin', 'Blog', array('dbDataPattern'	=> $dbDataPattern));

/**
 * ブログ記事の投稿日を更新
 */
	$BlogPost = ClassRegistry::init('Blog.BlogPost');
	$BlogPost->contentSaving = false;
	$datas = $BlogPost->find('all', array('recursive' => -1));
	if ($datas) {
		$ret = true;
		foreach ($datas as $data) {
			$data['BlogPost']['posts_date'] = date('Y-m-d H:i:s');
			unset($data['BlogPost']['eye_catch']);
			$BlogPost->set($data);
			if (!$BlogPost->save($data)) {
				$ret = false;
			}
		}
	}