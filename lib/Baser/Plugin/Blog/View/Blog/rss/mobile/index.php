<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [MOBILE] RSS
 */
?>
<?php
if($posts){
	echo $this->Rss->items($posts,'transformRSS');
}

function transformRSS($data) {
	$view = new View();
	$blogHelper = new BlogHelper($view);
	return [
		'title' => $data['BlogPost']['name'],
		'link' => Router::url($data['BlogContent']['Content']['url'] . 'archives/' . $data['BlogPost']['no']),
		'guid' => Router::url($data['BlogContent']['Content']['url'] . 'archives/' . $data['BlogPost']['no']),
		'category' => $data['BlogCategory']['title'],
		'description' => $blogHelper->removeCtrlChars($data['BlogPost']['content']),
		'pubDate' => $data['BlogPost']['posts_date']
	];
}
?>
