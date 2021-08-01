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

namespace BaserCore\Service\Admin;

use BaserCore\Model\Entity\Site;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Interface SiteManageServiceInterface
 * @package BaserCore\Service
 */
interface SiteManageServiceInterface
{

    /**
     * 取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * 新規データ用の初期値を含んだエンティティを取得する
     * @return Site
     */
    public function getNew(): EntityInterface;

    /**
     * 全件取得する
     * @param array $options
     * @return Query
     */
    public function getIndex(array $queryParams): Query;

    /**
     * 新規登録する
     * @param array $postData
     * @return EntityInterface|false
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
     * サイト全体の設定値を取得する
     * @param string $name
     * @return mixed
     */
    public function getSiteConfig($name);

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
    public function getSiteList($options = []): array;

    /**
     * テーマのリストを取得する
     * @param Site $site
     * @return array
     */
    public function getThemeList(Site $site): array;

    /**
     * デバイス設定を利用するかどうか
     * @return bool
     */
    public function isUseSiteDeviceSetting(): bool;

    /**
     * 言語設定を利用するかどうか
     * @return bool
     */
    public function isUseSiteLangSetting(): bool;

    /**
     * 現在の画面で表示しているものがメインサイトかどうか
     * @param Site $site
     * @return bool
     */
    public function isMainOnCurrentDisplay($site): bool;

}
