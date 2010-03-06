<?php
/* SVN FILE: $Id$ */
/**
 * GoogleMap コンポーネント
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			cake
 * @subpackage		cake.app.controllers.components
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * GoogleMap コンポーネント
 *
 * @package			cake
 * @subpackage		cake.app.controllers.components
 */
define('MAPS_HOST', 'maps.google.com');
class GmapsComponent  extends Object{
    /**
     * Latitude 
     * 
     * @var double
     */
    var $_latitude;
    /**
     * Longitude 
     *
     * @var double
     */
    var $_longitude;
    /**
     * Address 
     *
     * @var string
     */
    var $_address;
    /**
     * Country name 
     *
     * @var string
     */
    var $_countryName;
    /**
     * Country name code
     *
     * @var string
     */
    var $_countryNameCode;
    /**
     * Administrative area name
     *
     * @var string
     */
    var $_administrativeAreaName;
    /**
     * Postal Code
     *
     * @var string
     */
    var $_postalCode;
    /**
     * Google Maps Key
     *
     * @var string
     */
    var $_key;
    /**
     * Base Url
     *
     * @var string
     */
    var $_baseUrl;
    /**
     * GoogleMapsキーのセッター
     * @param string $value
     */
    function setKey($value){
        $this->_key = $value;
    }
    /**
     * Construct
     *
     * @param string $key
     */
    function __construct ($key='')
    {
        $this->_key= $key;
        $this->_baseUrl= "http://" . MAPS_HOST . "/maps/geo?output=xml&key=" . $this->_key;
    }
    /**
     * getInfoLocation
     *
     * @param string $address
     * @param string $city
     * @param string $state
     * @return boolean
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
     */
    function _connect($param) {
		
        $request_url = $this->_baseUrl . "&oe=utf-8&q=" . urlencode($param);
		App::import('Xml');
		$Xml = new Xml($request_url);
		$xmlArray = Set::reverse($Xml);
		
		$xml = $xmlArray['Kml'];
		
        if (! empty($xml['Response'])) {
            if(!isset($xml['Response']['Placemark']['Point'])){
                return false;
            }

            $point= $xml['Response']['Placemark']['Point'];
            if (! empty($point)) {
                $coordinatesSplit = split(",", $point['coordinates']);
                $this->_latitude = $coordinatesSplit[1];
                $this->_longitude = $coordinatesSplit[0];    
            }
            $this->_address= $xml['Response']['Placemark']['address'];
			
			if(isset($xml['Response']['Placemark']['AddressDetails'])){
				$this->_countryName= $xml['Response']['Placemark']['AddressDetails']['Country']['CountryName'];
				$this->_countryNameCode= $xml['Response']['Placemark']['AddressDetails']['Country']['CountryNameCode'];
                if(!empty($xml['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'])){
                    $this->_administrativeAreaName= $xml['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'];
                }
                if(!empty($xml['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea'])){
                    $administrativeArea= $xml['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea'];
                }
			}
			
            if (!empty($administrativeArea['SubAdministrativeArea']['Locality']['PostalCode']['PostalCodeNumber'])) {
                $this->_postalCode= $administrativeArea['SubAdministrativeArea']['Locality']['PostalCode']['PostalCodeNumber'];
            } elseif (!empty($administrativeArea['Locality']['PostalCode']['PostalCodeNumber'])) {
                $this->_postalCode= $administrativeArea['Locality']['PostalCode']['PostalCodeNumber'];
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
    function getPostalCode () {
        return $this->_postalCode;
    }
	/**
     * get the Address
     *
     * @return string
     */
    function getAddress () {
        return $this->_address;
    }
	/**
     * get the Country name
     *
     * @return string
     */
    function getCountryName () {
        return $this->_countryName;
    }
	/**
     * get the Country name code
     *
     * @return string
     */
    function getCountryNameCode () {
        return $this->_countryNameCode;
    }
	/**
     * get the Administrative area name
     *
     * @return string
     */
    function getAdministrativeAreaName () {
        return $this->_administrativeAreaName;
    }
    /**
     * get the Latitude coordinate
     *
     * @return double
     */
    function getLatitude () {
        return $this->_latitude;
    }
    /**
     * get the Longitude coordinate
     *
     * @return double
     */
    function getLongitude () {
        return $this->_longitude;
    }
}
?>