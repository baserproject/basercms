<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib
 * @since			baserCMS v 4.0.10
 * @license			http://basercms.net/license/index.html
 */

/**
 * BcGmaps
 *
 * @package Baser.Lib
 */
class BcGmaps extends Object {

/**
 * 接続試行回数
 * @var int
 */
	const RETRY_TIMES = 3;

/**
 * 接続試行の間隔(ミリ秒)
 * @var int
 */
	const RETRY_INTERVAL = 250;

/**
 * APIのベースとなるURL 
 * @var string
 */
	const GMAPS_API_BASE_URL = "http://maps.googleapis.com/maps/api/geocode/xml?";

/**
 * API URL
 * 
 * @var string
 */
	protected $_gmapsApiUrl;
	
/**
 * Latitude
 *
 * @var double
 */
	protected $_latitude;

/**
 * Longitude
 *
 * @var double
 */
	protected $_longitude;

/**
 * Construct
 * @return void
 */
	public function __construct($googleMapsApiKey) {
		$apiKey = empty($this->Controller->siteConfig['google_maps_api_key']) ? "" : $this->Controller->siteConfig['google_maps_api_key'];
		$this->_gmapsApiUrl = self::GMAPS_API_BASE_URL . "key=" . $apiKey;
	}

/**
 * getInfoLocation
 *
 * @param string $address
 * @return boolean
 */
	public function getInfoLocation($address) {
		if (!empty($address)) {
			return $this->_connect($address);
		}
		return false;
	}

/**
 * connect to Google Maps
 *
 * @param string $param
 * @return array|boolean
 */
	protected function _connect($param) {
		$requestUrl = $this->_gmapsApiUrl . "&address=" . urlencode($param);

		App::uses('Xml', 'Utility');

		try {
			$xml = retry(self::RETRY_TIMES, function () use ($requestUrl) {
				return Xml::build($requestUrl);
			}, self::RETRY_INTERVAL);
			$xmlArray = Xml::toArray($xml);
		} catch (XmlException $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}

		$xml = $xmlArray['GeocodeResponse'];

		$result = null;
		if (!empty($xml['result']['geometry'])) {
			$result = $xml['result'];
		} elseif(!empty($xml['result'][0])) {
			$result = $xml['result'][0];
		}

		if ($result) {
			if (!isset($result['geometry']['location'])) {
				return false;
			}
			$point = $result['geometry']['location'];
			if (!empty($point)) {
				$this->_latitude = $point['lat'];
				$this->_longitude = $point['lng'];
			}
			return ['latitude' => $point['lat'], 'longitude' => $point['lng']];
		} else {
			return false;
		}
	}
	
/**
 * get the Latitude coordinate
 *
 * @return double
 */
	public function getLatitude() {
		return $this->_latitude;
	}

/**
 * get the Longitude coordinate
 *
 * @return double
 */
	public function getLongitude() {
		return $this->_longitude;
	}

}