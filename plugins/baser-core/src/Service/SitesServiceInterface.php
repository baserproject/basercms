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

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

interface SitesServiceInterface
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
     */
    public function create(array $postData);

    /**
     * 編集する
     * @param EntityInterface $target
     * @param array $postData
     * @return mixed
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

}
