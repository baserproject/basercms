<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] RSS
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php
if($posts){
	echo $rss->items($posts,'transformRSS');
}
function transformRSS($data) {
	return array(
		'title' => $data['BlogPost']['name'],
		'link' => '/'.$data['BlogContent']['name'].'/archives/'.$data['BlogPost']['no'],
		'guid' => '/'.$data['BlogContent']['name'].'/archives/'.$data['BlogPost']['no'],
		'category' => $data['BlogCategory']['title'],
		'description' => $data['BlogPost']['content'].$data['BlogPost']['detail'],
		'pubDate' => $data['BlogPost']['posts_date']
	);
}
?>