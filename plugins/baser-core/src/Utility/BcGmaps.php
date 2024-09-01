<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Utility;

use BaserCore\Error\BcException;
use Cake\Cache\Cache;
use Cake\Http\Client;
use Cake\Http\Client\Exception\NetworkException;
use Cake\Utility\Exception\XmlException;
use Cake\Utility\Xml;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcGmaps
 */
class BcGmaps
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct($apiKey)
    {
        if(!$apiKey) {
            throw new BcException(__d('baser_core', 'システム基本設定にて、Google Maps API キーを入力してください。'));
        }
        $this->_gmapsApiUrl = self::GMAPS_API_BASE_URL . "?key=" . $apiKey;
    }

    /**
     * ロケーション情報を取得する
     *
     * @param string $address
     * @return array|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLocation($address)
    {
        if(!$address) return null;
        $encodedAddress = base64_encode($address);
        $location = Cache::read($encodedAddress, '_bc_gmaps_');

        if($location) {
            return $location;
        } else {
            $requestUrl = $this->_gmapsApiUrl . "&address=" . urlencode($address);
            try {
                $xml = BcUtil::retry(self::RETRY_TIMES, function() use ($requestUrl) {
                    $http = new Client();
                    try {
                        $response = $http->get($requestUrl);
                    } catch (NetworkException $e) {
                        return [];
                    }
                    $result = Xml::build($response->getStringBody());
                    if (!empty($result->error_message)) {
                        throw new XmlException($result->error_message);
                    }
                    return $result;
                }, self::RETRY_INTERVAL);
                $xmlArray = Xml::toArray($xml);
            } catch (\Throwable $e) {
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
                    $location = ['latitude' => $point['lat'], 'longitude' => $point['lng']];
                    Cache::write($encodedAddress, $location, '_bc_gmaps_');
                    return $location;
                }
            }
        }
        return null;
    }

}
