<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * ブログインストーラー
 *
 * @package            Blog.Config
 */

/**
 * データベース初期化
 */
$this->Plugin->initDb('Blog');

/**
 * ブログ記事の投稿日を更新
 */
$BlogPost = ClassRegistry::init('Blog.BlogPost');
$BlogPost->contentSaving = false;
$datas = $BlogPost->find('all', ['recursive' => -1]);
if ($datas) {
	$ret = true;
	foreach($datas as $data) {
		$data['BlogPost']['posts_date'] = date('Y-m-d H:i:s');
		unset($data['BlogPost']['eye_catch']);
		$BlogPost->set($data);
		if (!$BlogPost->save($data)) {
			$ret = false;
		}
	}
}
