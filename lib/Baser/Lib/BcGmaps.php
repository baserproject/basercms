<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib
 * @since           baserCMS v 4.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcGmaps
 *
 * @package Baser.Lib
 */
class BcGmaps extends CakeObject
{

	/**
	 * 接続試行回数
	 * @var int
	 */
	const RETRY_TIMES = 5;

	/**
	 * 接続試行の間隔(ミリ秒)
	 * @var int
	 */
	const RETRY_INTERVAL = 250;

	/**
	 * APIのベースとなるURL
	 * @var string
	 */
	const GMAPS_API_BASE_URL = "https://maps.googleapis.com/maps/api/geocode/xml";

	/**
	 * API URL
	 *
	 * @var string
	 */
	protected $_gmapsApiUrl;

	/**
	 * Construct
	 *
	 * @param string $apiKey
	 * @return void
	 */
	public function __construct($apiKey)
	{
		$this->_gmapsApiUrl = self::GMAPS_API_BASE_URL . "?key=" . $apiKey;
	}

	/**
	 * getInfoLocation
	 *
	 * @param string $address
	 * @return array|null
	 */
	public function getInfoLocation($address)
	{
		if (!empty($address)) {
			return $this->_geocode($address);
		}
		return null;
	}

	/**
	 * connect to Google Maps
	 *
	 * @param string $param
	 * @return array|null
	 */
	protected function _geocode($param)
	{
		$requestUrl = $this->_gmapsApiUrl . "&address=" . urlencode($param);
		App::uses('Xml', 'Utility');
		try {
			$xml = retry(self::RETRY_TIMES, function() use ($requestUrl) {
				// @var SimpleXMLElement $reuslt
				$result = Xml::build($requestUrl);
				if (!empty($result->error_message)) {
					throw new XmlException($result->error_message);
				}
				return $result;
			}, self::RETRY_INTERVAL);
			$xmlArray = Xml::toArray($xml);
		} catch (XmlException $e) {
			return null;
		} catch (\Exception $e) {
			return null;
		}

		$xml = $xmlArray['GeocodeResponse'];
		$result = null;
		if (!empty($xml['result']['geometry'])) {
			$result = $xml['result'];
		} elseif (!empty($xml['result'][0])) {
			$result = $xml['result'][0];
		}

		if (isset($result['geometry']['location'])) {
			$point = $result['geometry']['location'];
			if (!empty($point)) {
				return ['latitude' => $point['lat'], 'longitude' => $point['lng']];
			}
		}
		return null;
	}

}
