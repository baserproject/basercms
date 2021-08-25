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

use BaserCore\Model\Entity\Content;
use Cake\Http\ServerRequest;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Interface ContentServiceInterface
 * @package BaserCore\Service
 */
interface ContentServiceInterface
{
    /**
     * コンテンツを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * 空のQueryを返す
     *
     * @return Query
     */
    public function getEmptyIndex(): Query;

    /**
     * getTreeIndex
     *
     * @param  array $queryParams
     * @return Query
     */
    public function getTreeIndex(array $queryParams): Query;

    /**
     * コンテンツ管理の一覧用のデータを取得
     * @param array $queryParams
     * @param string $type
     * @return Query
     */
    public function getIndex(array $queryParams, ?string $type="all"): Query;

    /**
     * getTableConditions
     *
     * @param  array $queryParams
     * @return array
     */
    public function getTableConditions(array $queryParams): array;

    /**
     * テーブル用のコンテンツ管理の一覧データを取得
     *
     * @param  array $queryParams
     * @return Query
     */
    public function getTableIndex(array $queryParams): Query;

    /**
     * getTrashIndex
     * @param  array $queryParams
     * @return Query
     */
    public function getTrashIndex(array $queryParams): Query;

    /**
     * コンテンツフォルダーのリストを取得
     * コンボボックス用
     *
     * @param int $siteId
     * @param array $options
     * @return array|bool
     */
    public function getContentFolderList($siteId = null, $options = []);

    /**
     * ツリー構造のデータを コンボボックスのデータ用に変換する
     * @param $nodes
     * @return array
     */
    public function convertTreeList($nodes);

    /**
     * コンテンツ登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     */
    public function create(array $postData);

    /**
      * コンテンツ情報を取得する
      * @return array
      */
    public function getContensInfo();
}
