<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] グーグルマップ
 */
$_width = 600;
$_height = 400;
$_zoom = 16;
$_mapId = 'map';
$_address = h($this->BcBaser->siteConfig['address']);
$_markerText = '<span class="sitename">' . h($this->BcBaser->siteConfig['name']) . '</span><br /><span class="address">' . $_address . '</span>';
if (isset($width)) {
	$_width = $width;
}
if (isset($height)) {
	$_height = $height;
}
if (isset($zoom)) {
	$_zoom = $zoom;
}
if (isset($mapId)) {
	$_mapId = $mapId;
}
if (isset($address)) {
	$_address = $address;
}
if (isset($markerText)) {
	$_markerText = $markerText;
}
if (isset($longitude)) {
	$this->BcGooglemaps->longitude = $longitude;
}
if (isset($latitude)) {
	$this->BcGooglemaps->latitude = $latitude;
}
$this->BcGooglemaps->mapId = $_mapId;
$this->BcGooglemaps->zoom = $_zoom;
$this->BcGooglemaps->title = $this->BcBaser->siteConfig['name'];
$this->BcGooglemaps->markerText = $_markerText;
if (!$this->BcGooglemaps->load($_address, $_width, $_height)) {
	echo __d('baser', 'Google Maps を読み込めません。管理画面で正しい住所が設定されているか確認してください。');
}
