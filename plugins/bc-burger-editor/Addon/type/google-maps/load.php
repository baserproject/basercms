<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */

// GoogleMaps APIの取得

$googleMapsApiKey = \BcBurgerEditor\Lib\BurgerEditorUtil::getGoogleMapApiKey();

if ($googleMapsApiKey) {
	$this->BcBaser->js(['https://maps.google.com/maps/api/js?key=' . $googleMapsApiKey], ['inline' => false]);
}
