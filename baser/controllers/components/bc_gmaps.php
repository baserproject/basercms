<?php
/* SVN FILE: $Id$ */
/**
 * GoogleMap コンポーネント
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			cake
 * @subpackage		cake.app.controllers.components
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * defines
 */
define('MAPS_HOST', 'maps.googleapis.com');
/**
 * GoogleMap コンポーネント
 *
 * @package cake
 * @subpackage cake.app.controllers.components
 */
class BcGmapsComponent  extends Object {
/**
 * Latitude
 *
 * @var double
 * @access protected
 */
	var $_latitude;
/**
 * Longitude
 *
 * @var double
 * @access protected
 */
	var $_longitude;
/**
 * Address
 *
 * @var string
 * @access protected
 */
	var $_address;
/**
 * Country name
 *
 * @var string
 * @access protected
 */
	var $_countryName;
/**
 * Country name code
 *
 * @var string
 * @access protected
 */
	var $_countryNameCode;
/**
 * Administrative area name
 *
 * @var string
 * @access protected
 */
	var $_administrativeAreaName;
/**
 * Postal Code
 *
 * @var string
 * @access protected
 */
	var $_postalCode;
/**
 * Base Url
 *
 * @var string
 * @access protected
 */
	var $_baseUrl;
/**
 * Construct
 * 
 * @return void
 * @access private
 */
	function __construct () {
		
		$this->_baseUrl= "http://" . MAPS_HOST . "/maps/api/geocode/xml?";
		
	}
/**
 * getInfoLocation
 *
 * @param string $address
 * @param string $city
 * @param string $state
 * @return boolean
 * @access public
 */
	function getInfoLocation ($address) {
		
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
 * @access protected
 */
	function _connect($param) {

		$request_url = $this->_baseUrl . "sensor=false&language=ja&address=" . urlencode($param);
		
		App::import('Xml');
		$Xml = new Xml($request_url);
		
		
		$xmlArray = Set::reverse($Xml);

		$xml = $xmlArray['GeocodeResponse'];

		if (! empty($xml['Result'])) {
			if(!isset($xml['Result']['Geometry']['Location'])) {
				return false;
			}

			$point= $xml['Result']['Geometry']['Location'];
			if (! empty($point)) {
				$this->_latitude = $point['lat'];
				$this->_longitude = $point['lng'];
			}
			
			/*
			$this->_address= $xml['Response']['Placemark']['address'];

			if(isset($xml['Response']['Placemark']['AddressDetails'])) {
				$this->_countryName= $xml['Response']['Placemark']['AddressDetails']['Country']['CountryName'];
				$this->_countryNameCode= $xml['Response']['Placemark']['AddressDetails']['Country']['CountryNameCode'];
				if(!empty($xml['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'])) {
					$this->_administrativeAreaName= $xml['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'];
				}
				if(!empty($xml['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea'])) {
					$administrativeArea= $xml['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea'];
				}
			}

			if (!empty($administrativeArea['SubAdministrativeArea']['Locality']['PostalCode']['PostalCodeNumber'])) {
				$this->_postalCode= $administrativeArea['SubAdministrativeArea']['Locality']['PostalCode']['PostalCodeNumber'];
			} elseif (!empty($administrativeArea['Locality']['PostalCode']['PostalCodeNumber'])) {
				$this->_postalCode= $administrativeArea['Locality']['PostalCode']['PostalCodeNumber'];
			}*/
			return true;
		} else {
			return false;
		}
	
	}
/**
 * get the Postal Code
 *
 * @return string
 * @access public
 */
	/*function getPostalCode () {
		
		return $this->_postalCode;
		
	}*/
/**
 * get the Address
 *
 * @return string
 * @access public
 */
	/*function getAddress () {
		
		return $this->_address;
		
	}*/
/**
 * get the Country name
 *
 * @return string
 * @access public
 */
	/*function getCountryName () {
		
		return $this->_countryName;
		
	}*/
/**
 * get the Country name code
 *
 * @return string
 * @access public
 */
	/*function getCountryNameCode () {

		return $this->_countryNameCode;
		
	}*/
/**
 * get the Administrative area name
 *
 * @return string
 * @access public
 */
	/*function getAdministrativeAreaName () {

		return $this->_administrativeAreaName;

	}*/
/**
 * get the Latitude coordinate
 *
 * @return double
 * @access public
 */
	function getLatitude () {

		return $this->_latitude;

	}
/**
 * get the Longitude coordinate
 *
 * @return double
 * @access public
 */
	function getLongitude () {
		
		return $this->_longitude;
		
	}
	
}
