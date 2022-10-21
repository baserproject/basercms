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

use Cake\ORM\Query;
use Cake\Datasource\EntityInterface;

/**
 * SitesServiceInterface
 */
interface SitesServiceInterface extends CrudBaseServiceInterface
{

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
     * テーマのリストを取得する
     * @return array
     */
    public function getThemeList(): array;

    /**
     * 公開状態にする
     *
     * @param int $id
     * @return bool
     */
    public function publish($id): bool;

    /**
     * 非公開状態にする
     *
     * @param int $id
     * @return bool
     */
    public function unpublish($id): bool;

    /**
     * サイトのルートコンテンツを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function getRootContent($id);

}
