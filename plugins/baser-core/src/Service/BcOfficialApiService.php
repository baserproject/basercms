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

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Utility\Xml;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

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
     * @checked
     * @noTodo
     */
    public function getRss(string $rssName): array
    {
        $rssData = Cache::read($rssName, '_bc_update_');
        if ($rssData) return $rssData;

        // 直近で取得に失敗している場合は、一定時間は再取得せず空を返す
        // （外部に出られない環境でダッシュボードを開くたびに待たされるのを防ぐ）
        $failedAt = Cache::read($rssName . '.failed', '_bc_update_');
        if ($failedAt && (time() - (int)$failedAt) < self::FAILURE_RETRY_INTERVAL) {
            return [];
        }

        $Xml = new Xml();
        try {
            $client = $this->createClient();
            $response = $client->get(Configure::read('BcLinks.' . $rssName));
            $rssData = $Xml->build($response->getBody()->getContents());
            $rssData = $Xml->toArray($rssData->channel);
            $rssData = $rssData['channel']['item'];
        } catch (\Throwable $e) {
            Cache::write($rssName . '.failed', time(), '_bc_update_');
            return [];
        }
        if (!$rssData) return [];
        Cache::delete($rssName . '.failed', '_bc_update_');
        Cache::write($rssName, $rssData, '_bc_update_');
        return $rssData;
    }

    /**
     * 取得失敗後に再取得を試みるまでの秒数
     */
    public const FAILURE_RETRY_INTERVAL = 600;

    /**
     * 外部取得に使う HTTP クライアントのタイムアウト（秒）
     */
    public const CLIENT_TIMEOUT = 5;

    /**
     * 外部取得に使う HTTP クライアントを生成する
     *
     * @return Client
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function createClient(): Client
    {
        return new Client(['redirect' => true, 'timeout' => self::CLIENT_TIMEOUT]);
    }

}
