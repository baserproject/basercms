<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcGmapsComponent', 'Controller/Component');

/**
 * GoogleMapヘルパー
 *
 * @package Baser.View.Helper
 */
class BcGooglemapsHelper extends AppHelper {

/**
 * タイトル
 *
 * @var string
 */
	public $title = '';

/**
 * マーカーテキスト
 * @var string
 */
	public $markerText = '';
	
/**
 * ヘルパー
 * 
 * @var array
 */
	public $helpers = ['BcBaser'];

/**
 * 地図を表示するDOM ID
 *
 * @var string
 */
	public $mapId = 'map';

/**
 * 住所
 *
 * @var string
 */
	public $address = '';

/**
 * latitude
 *
 * @var string
 */
	public $latitude = '';

/**
 * longitude
 *
 * @var string
 */
	public $longitude = '';

/**
 * ズーム
 * @var int
 */
	public $zoom = 16;

/**
 * Google マップ を読み込む
 *
 * @param string $address
 * @param int $width
 * @param int $height
 * @return boolean
 */
	public function load($address = '', $width = null, $height = null) {

		if ($address)
			$this->address = $address;

		if (!$this->longitude || !$this->latitude) {
			if (!$this->loadLocation()) {
				return false;
			}
		}
		$script = $this->_getScript();
		if ($script) {
			if ($width || $height) {
				echo '<div id="' . $this->mapId . '" style="width: ' . $width . 'px; height:' . $height . 'px"><noscript>※ ' . __d('baser', 'JavaScript を有効にしてください。') . '</noscript></div>';
			} else {
				echo '<div id="' . $this->mapId . '"><noscript>※ ' . __d('baser', 'JavaScript を有効にしてください。') . '</noscript></div>';
			}
			echo $this->_getScript();
			return true;
		} else {
			return false;
		}
	}

/**
 * Google マップ読み込み用のjavascriptを生成する
 *
 * @return string
 */
	protected function _getScript() {

		if (!$this->longitude || !$this->latitude || !$this->mapId) {
			return false;
		}

		$script = <<< DOC_END
			var latlng = new google.maps.LatLng({$this->latitude},{$this->longitude});
			var options = {
				zoom: {$this->zoom},
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				navigationControl: true,
				mapTypeControl: true,
				scaleControl: true,
				scrollwheel: false,
			};
			var map = new google.maps.Map(document.getElementById("{$this->mapId}"), options);
			var marker = new google.maps.Marker({
				position: latlng,
				map: map,
				title:"{$this->title}"
			});
DOC_END;

		if ($this->markerText) {
			$script .=
				<<< INFO_END
			var infowindow = new google.maps.InfoWindow({
				content: '{$this->markerText}'
			});
			infowindow.open(map,marker);
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker);
			});
INFO_END;
		}
		$apiKey = empty($this->BcBaser->siteConfig['google_maps_api_key']) ? "" : $this->BcBaser->siteConfig['google_maps_api_key'];
		if (empty($apiKey)) {
			$adminLink = $this->BcBaser->getUrl(["admin"=>true, 'plugin' => '', 'controller' => 'site_configs', 'action'=>'form']);
			echo sprintf(__d('baser', 'Googleマップを利用するには、Google Maps APIのキーの登録が必要です。<a href="https://developers.google.com/maps/web/" target="_blank">キーを取得</a>して、<a href="%s">システム管理</a>より設定してください。'), $adminLink);
		}
		if($this->request->is('ssl')) {
			$apiUrl = 'https://maps.google.com/maps/api/js';
		} else {
			$apiUrl = 'http://maps.google.com/maps/api/js';
		}
		$googleScript = '<script src="' . $apiUrl . '?key=' . h($apiKey) . '"></script>';
		return $googleScript . '<script>' . $script . '</script>';
	}

/**
 * 位置情報を読み込む
 *
 * @return boolean
 */
	public function loadLocation() {

		if (!$this->address) {
			return false;
		}
		$location = $this->getLocation($this->address);
		if ($location) {
			$this->latitude = $location['latitude'];
			$this->longitude = $location['longitude'];
			return true;
		} else {
			return false;
		}
	}

/**
 * 位置情報を取得する
 *
 * @param string $address
 * @return array|boolean
 */
	public function getLocation($address) {
		App::uses('BcGmaps', 'Lib');
		$apiKey = Configure::read('BcSite.google_maps_api_key');
		$gmap = new BcGmaps($apiKey);
		$result = $gmap->getInfoLocation($address);
		if ($result) {
			return ['latitude' => $result['latitude'], 'longitude' => $result['longitude']];
		} else {
			return false;
		}
	}

}
