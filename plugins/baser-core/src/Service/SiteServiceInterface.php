<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use Cake\ORM\Query;
use BaserCore\Model\Entity\Site;
use Cake\Datasource\EntityInterface;

interface SiteServiceInterface
{

    /**
     * サイトを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * サイト一覧を取得
     * @param array $queryParams
     * @return Query
     */
    public function getIndex(array $queryParams): Query;
    /**
     * 新しいデータの初期値を取得する
     * @return EntityInterface
     */
    public function getNew(): EntityInterface;

    /**
     * 新規登録する
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     */
    public function create(array $postData);

    /**
     * 編集する
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     */
    public function update(EntityInterface $target, array $postData);

    /**
     * 削除する
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * 選択可能なデバイスの一覧を取得する
     *
     * 現在のサイトとすでに利用されいているデバイスは除外する
     *
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @return array
     */
    public function getSelectableDevices($mainSiteId, $currentSiteId = null): array;

    /**
     * 選択可能が言語の一覧を取得する
     *
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @return array
     */
    public function getSelectableLangs($mainSiteId, $currentSiteId = null): array;

    /**
     * URLよりサイトを取得する
     *
     * @param string $url
     * @return EntityInterface
     */
    public function findByUrl($url): EntityInterface;

    /**
     * IDよりサイトを取得する
     *
     * @param string $id
     * @return Query
     */
    public function findById($id): Query;

    /**
     * 言語リストを取得
     * @return array
     */
    public function getLangList(): array;

    /**
     * デバイスリストを取得
     * @return array
     */
    public function getDeviceList(): array;

    /**
     * サイトのリストを取得
     * @param array $options
     * @return array
     */
    public function getList($options = []): array;

    /**
     * テーマのリストを取得する
     * @param Site $site
     * @return array
     */
    public function getThemeList(Site $site): array;

}
