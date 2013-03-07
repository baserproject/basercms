<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] モバイル用 Google Analytics 画像タグ
 *
 * PHP5以上のみ対応
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<cake:nocache>
<?php
$baseUrl = "/mobile/ga";
if(!empty($_SERVER["HTTP_REFERER"])) {
	$referer = $_SERVER["HTTP_REFERER"];
} else {
	$referer = "-";
}
if (!empty($_SERVER["REQUEST_URI"])) {
	$path = $_SERVER["REQUEST_URI"];
} else {
	$path = '';
}

$url = $baseUrl . "?";
$url .= "&utmn=" . rand(0, 0x7fffffff);
$url .= "&utmr=" . urlencode($referer);
$url .= "&utmp=" . urlencode($path);
$url .= "&guid=ON";
echo '<img src="'.str_replace("&", "&amp;", $url).'" width="1" height="1" />';
?>
</cake:nocache>