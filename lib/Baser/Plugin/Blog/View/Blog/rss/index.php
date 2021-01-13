<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] RSS
 */
?>
<?php
if ($posts) {
	echo $this->Rss->items($posts, 'transformRSS');
}

function transformRSS($data)
{
	$view = new View();
	$blogHelper = new BlogHelper($view);
	$bcBaserhelper = new BcBaserHelper($view);
	$url = $bcBaserhelper->getContentsUrl(null, false, null, false) . 'archives/' . $data['BlogPost']['no'];
	$eyeCatch = [
		'url' => '',
		'type' => '',
		'length' => '',
	];
	if (!empty($data['BlogPost']['eye_catch'])) {
		$eyeCatch['url'] = Router::url($blogHelper->getEyeCatch($data, ['imgsize' => '', 'output' => 'url']), true);
	}
	return [
		'title' => $data['BlogPost']['name'],
		'link' => $url,
		'guid' => $url,
		'category' => $data['BlogCategory']['title'],
		'description' => $blogHelper->removeCtrlChars($data['BlogPost']['content'] . $data['BlogPost']['detail']),
		'pubDate' => $data['BlogPost']['posts_date'],
		'enclosure' => $eyeCatch,
	];
}

?>
