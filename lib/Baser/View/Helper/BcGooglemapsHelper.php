<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcGmapsComponent', 'Controller/Component');

/**
 * GoogleMapヘルパー
 *
 * @package Baser.View.Helper
 */
class BcGooglemapsHelper extends AppHelper
{

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
	public function load($address = '', $width = null, $height = null)
	{
		if ($address) {
			$this->address = $address;
		}
		$script = $this->_getScript();
		if ($script) {
			if ($width || $height) {
				echo '<div id="' . $this->mapId . '" style="width: ' . $width . 'px; height:' . $height . 'px"><noscript>※ ' . __d('baser', 'JavaScript を有効にしてください。') . '</noscript></div>';
			} else {
				echo '<div id="' . $this->mapId . '"><noscript>※ ' . __d('baser', 'JavaScript を有効にしてください。') . '</noscript></div>';
			}
			echo $script;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Google マップ読み込み用のjavascriptを生成する
	 * @return string
	 * @todo リファクタリング
	 */
	protected function _getScript()
	{

		if (!$this->mapId) {
			return false;
		}
		$apiKey = empty($this->BcBaser->siteConfig['google_maps_api_key'])? "" : h($this->BcBaser->siteConfig['google_maps_api_key']);
		$address = $this->address;
		$script = <<< DOC_END
			var geo = new google.maps.Geocoder();
			var lat = '{$this->latitude}';
			var lng = '{$this->longitude}';
			if(!lat || !lng) {
				geo.geocode({ address: '{$address}' }, function(results, status) {
					if(status === 'OK') {
						lat = results[0].geometry.location.lat();
						lng = results[0].geometry.location.lng();
						loadMap(lat, lng);
					}
				});
			} else {
				loadMap(lat, lng)
			}
			function loadMap(lat, lng){
				var latlng = new google.maps.LatLng(lat,lng);
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
				if('{$this->markerText}') {
					var infowindow = new google.maps.InfoWindow({
						content: '{$this->markerText}'
					});
					infowindow.open(map,marker);
					google.maps.event.addListener(marker, 'click', function() {
						infowindow.open(map,marker);
					});
				}
			}
DOC_END;
		$apiKey = empty($this->BcBaser->siteConfig['google_maps_api_key'])? "" : $this->BcBaser->siteConfig['google_maps_api_key'];
		if (empty($apiKey)) {
			$adminLink = $this->BcBaser->getUrl(["admin" => true, 'plugin' => '', 'controller' => 'site_configs', 'action' => 'form']);
			echo sprintf(__d('baser', 'Googleマップを利用するには、Google Maps APIのキーの登録が必要です。<a href="https://developers.google.com/maps/web/" target="_blank">キーを取得</a>して、<a href="%s">システム管理</a>より設定してください。'), $adminLink);
		}
		$apiUrl = 'https://maps.google.com/maps/api/js';
		$googleScript = '<script src="' . $apiUrl . '?key=' . $apiKey . '"></script>';
		return $googleScript . '<script>' . $script . '</script>';
	}

	/**
	 * 位置情報を読み込む
	 *
	 * @return boolean
	 * @deprecated GoogleMapsAPIキーの利用制限をかける場合、BcGmapsが利用できない為、非推奨
	 * 地図取得は、Javascript なので、利用制限はリファラ制限となる。BcGmapsの場合、IP制限しか利用できない為、
	 * 両方の制限を利用する場合、APIキーが二つ必要となり現実的ではない。
	 */
	public function loadLocation()
	{

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
	 * @deprecated GoogleMapsAPIキーの利用制限をかける場合、BcGmapsが利用できない為、非推奨
	 * 地図取得は、Javascript なので、利用制限はリファラ制限となる。BcGmapsの場合、IP制限しか利用できない為、
	 * 両方の制限を利用する場合、APIキーが二つ必要となり現実的ではない。
	 */
	public function getLocation($address)
	{
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
