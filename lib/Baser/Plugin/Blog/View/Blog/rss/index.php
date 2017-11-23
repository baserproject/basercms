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
 * [PUBLISH] RSS
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
		'link' => '/' . $view->request->params['Content']['name'] . '/archives/' . $data['BlogPost']['no'],
		'guid' => '/' . $view->request->params['Content']['name'] . '/archives/' . $data['BlogPost']['no'],
		'category' => $data['BlogCategory']['title'],
		'description' => $blogHelper->removeCtrlChars($data['BlogPost']['content'] . $data['BlogPost']['detail']),
		'pubDate' => $data['BlogPost']['posts_date']
	];
}
?>
