<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] グールグルマップ
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
<?php
$_width = 600;
$_height = 400;
$_zoom = 16;
$_mapId = 'map';
$_address = $bcBaser->siteConfig['address'];
$_markerText = '<span class="sitename">'.$bcBaser->siteConfig['name'].'</span><br /><span class="address">'.$_address.'</span>';
if(isset($width)) $_width = $width;
if(isset($height)) $_height = $height;
if(isset($zoom)) $_zoom = $zoom;
if(isset($mapId)) $_mapId = $mapId;
if(isset($address)) $_address = $address;
if(isset($markerText)) $_markerText = $markerText;
if(isset($longitude)) {
	$bcGooglemaps->longitude = $longitude;
}
if(isset($latitude)) {
	$bcGooglemaps->latitude = $latitude;
}
$bcGooglemaps->mapId = $_mapId;
$bcGooglemaps->zoom = $_zoom;
$bcGooglemaps->title = $bcBaser->siteConfig['name'];
$bcGooglemaps->markerText = $_markerText;
if(!$bcGooglemaps->load($_address,$_width,$_height)){
	echo 'Google Maps を読み込めません。管理画面で正しい住所が設定されているか確認してください。';
}
?>