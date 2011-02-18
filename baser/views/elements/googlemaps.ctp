<?php
/* SVN FILE: $Id$ */
/**
 * グールグルマップ
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
 * @package			baser.views
 * @since			Baser v 0.1.0
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
$_address = $baser->siteConfig['address'];
$_markerText = '<span class="sitename">'.$baser->siteConfig['name'].'</span><br /><span class="address">'.$_address.'</span>';
if(isset($width)) $_width = $width;
if(isset($height)) $_height = $height;
if(isset($zoom)) $_zoom = $zoom;
if(isset($mapId)) $_mapId = $mapId;
if(isset($address)) $_address = $address;
if(isset($markerText)) $_markerText = $markerText;
if(isset($longitude)) {
	$googlemaps->longitude = $longitude;
}
if(isset($latitude)) {
	$googlemaps->latitude = $latitude;
}
$googlemaps->googlemapsKey = $baser->siteConfig['googlemaps_key'];
$googlemaps->mapId = $_mapId;
$googlemaps->zoom = $_zoom;
$googlemaps->title = $baser->siteConfig['name'];
$googlemaps->markerText = $_markerText;
if(!$googlemaps->load($_address,$_width,$_height)){
	echo 'Google Maps を読み込めません。管理画面で正しいgooglemapsキー、住所が設定されているか確認してください。';
}
?>