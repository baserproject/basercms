<?php
/* SVN FILE: $Id$ */
/**
 * GoogleMapヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			cake
 * @subpackage		cake.app.view.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * GoogleMapヘルパー
 *
 * @package cake
 * @subpackage cake.app.views.helpers
 */
class BcGooglemapsHelper extends AppHelper {
/**
 * タイトル
 * 
 * @var string
 * @access public
 */
	var $title = '';
/**
 * マーカーテキスト
 * @var string
 * @access public
 */
	var $markerText = '';
/**
 * 地図を表示するDOM ID
 * 
 * @var string
 * @access public
 */
	var $mapId = 'map';
/**
 * 住所
 * 
 * @var string
 * @access public
 */
	var $address = '';
/**
 * latitude
 * 
 * @var string
 * @access public
 */
	var $latitude = '';
/**
 * longitude
 * 
 * @var string
 * @access public
 */
	var $longitude = '';
/**
 * ズーム
 * @var int
 * @access public
 */
	var $zoom = 16;
/**
 * Google マップ を読み込む
 * 
 * @param string $address
 * @param int $width
 * @param int $height
 * @return boolean
 * @access public 
 */
	function load($address='',$width=null,$height=null) {

		if($address) $this->address = $address;

		if(!$this->longitude || !$this->latitude) {
			if(!$this->loadLocation()) {
				return false;
			}
		}
		$script = $this->_getScript();
		if($script) {
			if($width || $height) {
				echo '<div id="'.$this->mapId.'" style="width: '.$width.'px; height:'.$height.'px"></div>';
			}else {
				echo '<div id="'.$this->mapId.'"></div>';
			}
			echo $this->_getScript();
			return true;
		}else {
			return false;
		}
		
	}
/**
 * Google マップ読み込み用のjavascriptを生成する
 * 
 * @return string
 * @access public 
 */
	function _getScript() {

		if(!$this->longitude || !$this->latitude || !$this->mapId) {
			return false;
		}
		
		$script =
<<< DOC_END
			var latlng = new google.maps.LatLng({$this->latitude},{$this->longitude});
			var options = {
				zoom: {$this->zoom},
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				navigationControl: true,
				mapTypeControl: true,
				scaleControl: true
			};
			var map = new google.maps.Map(document.getElementById("{$this->mapId}"), options);
			var marker = new google.maps.Marker({
				position: latlng,
				map: map,
				title:"{$this->title}"
			});
DOC_END;
	
		if($this->markerText) {
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

		$googleScript = '<script src="http://maps.google.com/maps/api/js?sensor=false&amp;language=ja" type="text/javascript"></script>';

		return $googleScript.'<script type="text/javascript">'.$script.'</script>';

	}
/**
 * 位置情報を読み込む
 * 
 * @return boolean
 * @access public
 */
	function loadLocation() {
		
		if(!$this->address) {
			return false;
		}
		$location = $this->getLocation($this->address);
		if($location) {
			$this->latitude = $location['latitude'];
			$this->longitude = $location['longitude'];
			return true;
		}else {
			return false;
		}
		
	}
/**
 * 位置情報を取得する
 *
 * @param string $address
 * @return boolean 
 * @access public
 */

	function getLocation($address) {
		
		App::import("Component","BcGmaps");
		$gmap = new BcGmapsComponent();
		if ($gmap->getInfoLocation($address)) {
			return array('latitude'=>$gmap->getLatitude(),'longitude'=>$gmap->getLongitude());
		}else {
			return false;
		}
		
	}
	
}