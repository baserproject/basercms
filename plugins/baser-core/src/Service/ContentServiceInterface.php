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
use Nette\Utils\DateTime;

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
     * ゴミ箱のコンテンツを取得する
     * @param int $id
     * @return EntityInterface|array
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function getTrash($id);

    /**
     * コンテンツの子要素を取得する
     *
     * @param  int $id
     * @return Query|null
     */
    public function getChildren($id);

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
    public function getIndex(array $queryParams=[], ?string $type="all"): Query;

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
     * @param array $queryParams
     * @param string $type
     * @return Query
     */
    public function getTrashIndex(array $queryParams=[], string $type="all"): Query;

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
     * コンテンツ情報を論理削除する
     * @param int $id
     * @return bool
     *
     */
    public function delete($id);

    /**
     * コンテンツ情報を削除する
     * @param int $id
     * @param bool $enableTree(デフォルト:false) TreeBehaviorの有無
     * @return bool
     */
    public function hardDelete($id, $enableTree = false): bool;

    /**
     * コンテンツ情報と紐付いてるモデルを削除する
     * @param int $id
     * @return bool
     */
    public function hardDeleteWithAssoc($id): bool;

    /**
     * 該当するコンテンツ情報をすべて論理削除する
     *
     * @param  array $conditions
     * @return int
     */
    public function deleteAll(array $conditions): int;

    /**
     * 指定日時以前の該当する論理削除されたコンテンツ情報をすべて削除する
     *
     * @param  Datetime $dateTime
     * @return int
     */
    public function hardDeleteAll(Datetime $dateTime): int;

    /**
     * コンテンツを削除する（論理削除）
     *
     * ※ エイリアスの場合は直接削除
     * @param int $id
     * @return bool
     */
    public function treeDelete($id): bool;

    /**
     * 論理削除されたコンテンツを復元する
     *
     * @param  int $id
     * @return EntityInterface|array|null $trash
     */
    public function restore($id);

    /**
     * ゴミ箱内のコンテンツをすべて元に戻す
     *
     * @param  array $queryParams
     * @return int $count
     */
    public function restoreAll(array $queryParams = []): int;

    /**
      * コンテンツ情報を取得する
      * @return array
      */
    public function getContensInfo();

    /**
     * 再帰的に削除
     *
     * エイリアスの場合
     *
     * @param int $id
     * @return bool $result
     */
    public function deleteRecursive($id): bool;

    /**
     * レイアウトテンプレートを取得する
     *
     * @param $id
     * @return string $parentTemplate|false
     */
    public function getParentLayoutTemplate($id);

}
