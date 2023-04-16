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

namespace BaserCore\Service;

use BaserCore\Error\BcException;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Utility\Xml;

/**
 * interface BcOfficialApiService
 */
class BcOfficialApiService implements BcOfficialApiServiceInterface
{

    /**
     * RSS情報を取得する
     *
     * @param string $rssName
     * @return array|mixed
     */
    public function getRss(string $rssName): array
    {
        $rssData = Cache::read($rssName, '_bc_update_');
        if ($rssData) return $rssData;
        $Xml = new Xml();
        try {
            $client = new Client(['redirect' => true]);
            $response = $client->get(Configure::read('BcLinks.' . $rssName));
            $rssData = $Xml->build($response->getBody()->getContents());
            $rssData = $Xml->toArray($rssData->channel);
            $rssData = $rssData['channel']['item'];
        } catch (\Throwable $e) {
            return [];
        }
        if (!$rssData) return [];
        Cache::write($rssName, $rssData, '_bc_update_');
        return $rssData;
    }

}
