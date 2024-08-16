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

namespace BaserCore\View\Helper;

use BaserCore\Error\BcException;
use BaserCore\Utility\BcGmaps;
use BaserCore\Utility\BcSiteConfig;
use Cake\View\Helper;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * GoogleMapヘルパー
 *
 * @property BcBaserHelper $BcBaser
 */
#[\AllowDynamicProperties]
class BcGoogleMapsHelper extends Helper
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * ヘルパー
     *
     * @var array
     */
    public array $helpers = ['BaserCore.BcBaser'];

    /**
     * Google マップ を読み込む
     *
     * @param string $address
     * @param int $width
     * @param int $height
     * @return boolean
     * @checked
     * @noTodo
     */
    public function load($options)
    {
        $options = array_merge([
            'title' => $this->getView()->getRequest()->getAttribute('currentSite')->title,
            'address' => BcSiteConfig::get('address'),
            'longitude' => '',
            'latitude' => '',
            'width' => 600,
            'height' => 400,
            'markerText' => '',
            'zoom' => 16,
            'mapId' => 'map',
            'apiKey' => BcSiteConfig::get('google_maps_api_key'),
            'apiBaseUrl' => 'https://maps.google.com/maps/api/js',
            'apiUrl' => null,
        ], $options);

        if (empty($options['address']) && empty($options['latitude'] && empty($options['longitude'])) ) {
            if(empty($options['address'])) {
                throw new BcException(__d('baser_core', 'システム基本設定で住所を指定するか、パラーメーター address を指定してください。'));
            }
        }
        if(empty($options['latitude']) || empty($options['longitude'])) {
            try {
                $location = $this->getLocation($options['address'], $options['apiKey']);
            } catch (\Throwable $e){
                $location = [];
            }
            if($location) $options = array_merge($options, $location);
        }
        $options['apiUrl'] = $options['apiBaseUrl'] . '?key=' . $options['apiKey'];
        $options['markerText'] = $this->getMarker($options['title'], $options['address']);

        return $this->BcBaser->getElement('google_maps', $options);
    }

    /**
     * マーカーを取得する
     *
     * @param string $title
     * @param string $address
     * @return mixed
     * @checked
     * @noTodo
     */
    public function getMarker(string $title, string $address)
    {
        return $this->BcBaser->getElement('google_maps_marker', [
            'title' => $title,
            'address' => $address
        ]);
    }

    /**
     * ロケーションを取得する
     *
     * @param string $address
     * @param string $apiKey
     * @return array|null
     * @checked
     * @noTodo
     */
    public function getLocation(string $address, ?string $apiKey)
    {
        try {
            $gmaps = new BcGmaps($apiKey);
            return $gmaps->getLocation($address);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

}
