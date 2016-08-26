<?php

/**
 * [MOBILE] RSS
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
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
	$blogHelper = new BlogHelper(new View());
	return array(
		'title' => $data['BlogPost']['name'],
		'link' => Router::url('/' . Configure::read('BcRequest.agentAlias') . '/' . $data['BlogContent']['name'] . '/archives/' . $data['BlogPost']['no']),
		'guid' => Router::url('/' . Configure::read('BcRequest.agentAlias') . '/' . $data['BlogContent']['name'] . '/archives/' . $data['BlogPost']['no']),
		'category' => $data['BlogCategory']['title'],
		'description' => $blogHelper->removeCtrlChars($data['BlogPost']['content']),
		'pubDate' => $data['BlogPost']['posts_date']
	);
}
?>
