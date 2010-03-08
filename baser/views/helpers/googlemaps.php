<?php
/* SVN FILE: $Id$ */
/**
 * GoogleMapヘルパー
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
 * @package			cake
 * @subpackage		cake.app.view.helpers
 * @since			Baser v 0.1.0
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
 * @package			cake
 * @subpackage		cake.app.views.helpers
 */
class GooglemapsHelper extends AppHelper {
	var $googlemapsKey = '';
	var $title = '';
	var $markerText = '';
	var $mapId = 'map';
	var $address = '';
	var $latitude = '';
	var $longitude = '';
	var $zoom = 16;
/**
 * Google マップ を読み込む
 */
	function load($address='',$width=null,$height=null){

		if($address) $this->address = $address;
		
		if(!$this->longitude || !$this->latitude){
			if(!$this->loadLocation()){
				return false;
			}
		}
		$script = $this->_getScript();
		if($script){
			if($width || $height){
				echo '<div id="'.$this->mapId.'" style="width: '.$width.'px; height:'.$height.'px"></div>';
			}else{
				echo '<div id="'.$this->mapId.'"></div>';
			}
			echo $this->_getScript();
			return true;
		}else{
			return false;
		}
	}
/**
 * Google マップ読み込み用のjavascriptを生成する
 */
	function _getScript(){

		if(!$this->longitude || !$this->latitude || !$this->mapId || !$this->googlemapsKey){
			return false;
		}
		$script = 'var map;';
		$script .= 'var markers = new Array(1);';
		$script .= 'var marker = null;';
		$script .= 'var n_markers = 0;';
		$script .= 'var markeropts = new Object();';
		$script .= 'map = new GMap2(document.getElementById("'.$this->mapId.'"));';
		$script .= 'map.setCenter(new GLatLng('.$this->latitude.','.$this->longitude.'),'.$this->zoom.');';
		$script .= 'map.addControl(new GLargeMapControl());';
		$script .= 'map.addControl(new GMapTypeControl());';
		$script .= 'map.addControl(new GOverviewMapControl());';
		$script .= 'map.setMapType(G_NORMAL_MAP);';
		if($this->title){
			$script .= 'markeropts.title = "'.$this->title.'";';
		}
		if($this->markerText){
			$script .= 'marker = new GMarker(new GPoint('.$this->longitude.','.$this->latitude.'), markeropts);';
			$script .= 'markers[n_markers] = marker;';
			$script .= "markers[0].openInfoWindowHtml('".$this->markerText."');";
			$script .= 'n_markers++;';
			$script .= 'map.addOverlay(marker);';
		}
		$googleScript = '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key='.$this->googlemapsKey.'" type="text/javascript"></script>';

		return $googleScript.'<script type="text/javascript">'.$script.'</script>';
		
	}
/**
 * 位置情報を読み込む
 */
	function loadLocation(){
		if(!$this->googlemapsKey || !$this->address){
			return false;
		}
		$location = $this->getLocation($this->googlemapsKey,$this->address);
		if($location){
			$this->latitude = $location['latitude'];
			$this->longitude = $location['longitude'];
			return true;
		}else{
			return false;
		}
	}
/**
 * 位置情報を取得する
 */
	function getLocation($key,$address){
		App::import("Component","Gmaps");
		$gmap = new GmapsComponent($key);
		if ($gmap->getInfoLocation($address)) {
			return array('latitude'=>$gmap->getLatitude(),'longitude'=>$gmap->getLongitude());
		}else{
			return false;
		}
	}
}
?>