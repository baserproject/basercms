<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller.Component
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

define('MAPS_HOST', 'maps.googleapis.com');

/**
 * GoogleMap コンポーネント
 *
 * @package Baser.Controller.Component
 */
class BcGmapsComponent extends Component {

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
 * Address
 *
 * @var string
 */
	protected $_address;

/**
 * Country name
 *
 * @var string
 */
	protected $_countryName;

/**
 * Country name code
 *
 * @var string
 */
	protected $_countryNameCode;

/**
 * Administrative area name
 *
 * @var string
 */
	protected $_administrativeAreaName;

/**
 * Postal Code
 *
 * @var string
 */
	protected $_postalCode;

/**
 * Base Url
 *
 * @var string
 */
	protected $_baseUrl;

/**
 * Construct
 * 
 * @return void
 */
	public function __construct() {
		$this->_baseUrl = "http://" . MAPS_HOST . "/maps/api/geocode/xml?";
	}

/**
 * getInfoLocation
 *
 * @param string $address
 * @param string $city
 * @param string $state
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
 * @return boolean
 */
	protected function _connect($param) {
		$apiKey = empty($this->Controller->siteConfig['google_maps_api_key']) ? "" : $this->Controller->siteConfig['google_maps_api_key'];
		$requestUrl = $this->_baseUrl . "key=" . $apiKey . "&address=" . urlencode($param);

		App::uses('Xml', 'Utility');

		try {
			$xmlArray = Xml::toArray(Xml::build($requestUrl));
		} catch(XmlException $e) {
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
			return true;
		} else {
			return false;
		}
	}

/**
 * get the Postal Code
 *
 * @return string
 */
	//public function getPostalCode () {
	//	return $this->_postalCode;
	//}

/**
 * get the Address
 *
 * @return string
 */
	//public function getAddress () {
	//	return $this->_address;
	//}

/**
 * get the Country name
 *
 * @return string
 */
	//public function getCountryName () {
	//	return $this->_countryName;
	//}

/**
 * get the Country name code
 *
 * @return string
 */
	//public function getCountryNameCode () {
	//	return $this->_countryNameCode;
	//}

/**
 * get the Administrative area name
 *
 * @return string
 */
	//public function getAdministrativeAreaName () {
	//	return $this->_administrativeAreaName;
	//}

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
