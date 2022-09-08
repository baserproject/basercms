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

namespace BaserCore\Controller\Component;

use Cake\Controller\Component;

/**
 * Class BcGmapsComponent
 *
 * GoogleMap コンポーネント
 *
 * @package Baser.Controller.Component
 */
class BcGmapsComponent extends Component
{

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
     * BcGmaps
     *
     * @var \BcGmaps
     */
    protected $_BcGmaps;

    /**
     * Construct
     * @return void
     */
    public function __construct(ComponentCollection $collection, $settings = [])
    {
        parent::__construct($collection, $settings);
        if (isset($settings['apiKey'])) {
            $apiKey = $settings['apiKey'];
        } else {
            $apiKey = Configure::read('BcSite.google_maps_api_key');
        }
        $this->_BcGmaps = new BcGmaps($apiKey);
    }

    /**
     * getInfoLocation
     *
     * @param string $address
     * @return boolean
     */
    public function getInfoLocation($address)
    {
        $result = $this->_BcGmaps->getInfoLocation($address);
        if ($result) {
            $this->_latitude = $result['latitude'];
            $this->_longitude = $result['longitude'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * get the Latitude coordinate
     *
     * @return double
     */
    public function getLatitude()
    {
        return $this->_latitude;
    }

    /**
     * get the Longitude coordinate
     *
     * @return double
     */
    public function getLongitude()
    {
        return $this->_longitude;
    }

}
