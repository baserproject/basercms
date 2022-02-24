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
     * aliasを作成する
     *
     * @param  int $id
     * @param  array $postData
     * @return \Cake\Datasource\EntityInterface
     */
    public function alias(int $id, array $postData);

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
    public function getContentsInfo();

    /**
     * 再帰的に削除
     *※ エイリアスの場合は直接削除
     * @param int $id
     * @return void
     * @throws Exception
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

    /**
     * コンテンツIDよりURLを取得する
     *
     * @param int $id
     * @return string URL
     */
    public function getUrlById($id, $full = false);

    /**
     * コンテンツ管理上のURLを元に正式なURLを取得する
     *
     * ドメインからのフルパスでない場合、デフォルトでは、
     * サブフォルダ設置時等の baseUrl（サブフォルダまでのパス）は含まない
     *
     * @param string $url コンテンツ管理上のURL
     * @param bool $full http からのフルのURLかどうか
     * @param bool $useSubDomain サブドメインを利用しているかどうか
     * @param bool $base $full が false の場合、ベースとなるURLを含めるかどうか
     * @return string URL
     */
    public function getUrl($url, $full = false, $useSubDomain = false, $base = false);

    /**
     * コンテンツ情報を更新する
     *
     * @param  EntityInterface $content
     * @param  array $contentData
     * @return EntityInterface
     */
    public function update($content, $contentData);

    /**
     * 公開状態にする
     *
     * @param int $id
     * @return EntityInterface
     */
    public function publish($id): EntityInterface;

    /**
     * 非公開状態にする
     *
     * @param int $id
     * @return EntityInterface
     */
    public function unpublish($id): EntityInterface;

    /**
     * exists
     *
     * @param  int $id
     * @param bool $withTrash ゴミ箱の物も含めるか
     * @return bool
     */
    public function exists($id, $withTrash = false): bool;

    /**
     * コンテンツを移動する
     *
     * 基本的に targetId の上に移動する前提となる
     * targetId が空の場合は、同親中、一番下に移動する
     *
     * @param array $origin
     * @param array $target
     * @return Content|bool|false
     */
    public function move($origin, $target);

    /**
     * 移動元のコンテンツと移動先のディレクトリから移動が可能かチェックする
     *
     * @param int $currentId int 移動元コンテンツID
     * @param int $targetParentId int 移動先コンテンツID (ContentFolder)
     * @return bool
     */
    public function isMovable($currentId, $targetParentId);

    /**
     * ID を指定して公開状態かどうか判定する
     *
     * @param int $id
     * @return bool
     */
    public function isPublishById($id);

    /**
     * 公開状態を取得する
     *
     * @param Content $content コンテンツデータ
     * @return bool 公開状態
     */
    public function isAllowPublish($content, $self = false);

    /**
     * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
     *
     * @param $url
     * @return bool
     */
    public function existsContentByUrl($url);

    /**
     * タイトル、URL、公開状態が更新されているか確認する
     *
     * @param int $id コンテンツID
     * @param array $newData 新しいコンテンツデータ
     * @return bool
     */
    public function isChangedStatus($id, $newData);

    /**
     * TreeBehaviorの設定値を更新する
     *
     * @param  string $targetConfig
     * @param  array $conditions
     * @return TreeBehavior
     */
    public function setTreeConfig($targetConfig, $conditions);

    /**
     * 公開済の conditions を取得
     *
     * @return array 公開条件（conditions 形式）
     */
    public function getConditionAllowPublish();
}
