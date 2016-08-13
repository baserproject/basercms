<?php

/**
 * [PUBLISH] RSS
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>
<?php
if($posts){
	echo $this->Rss->items($posts,'transformRSS');
}

function transformRSS($data) {
	$blogHelper = new BlogHelper($this);
	return array(
		'title' => $data['BlogPost']['name'],
		'link' => '/' . $this->request->params['Content']['name'] . '/archives/' . $data['BlogPost']['no'],
		'guid' => '/' . $this->request->params['Content']['name'] . '/archives/' . $data['BlogPost']['no'],
		'category' => $data['BlogCategory']['title'],
		'description' => $blogHelper->removeCtrlChars($data['BlogPost']['content'] . $data['BlogPost']['detail']),
		'pubDate' => $data['BlogPost']['posts_date']
	);
}
?>
