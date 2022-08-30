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

use BaserCore\Model\Entity\Content;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\QueryInterface;
use Cake\ORM\Query;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Interface ContentsServiceInterface
 * @package BaserCore\Service
 */
interface ContentsServiceInterface extends CrudBaseServiceInterface
{

    /**
     * ゴミ箱のコンテンツを取得する
     * @param int $id
     * @return EntityInterface|array
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrash($id);

    /**
     * コンテンツの子要素を取得する
     *
     * @param  int $id
     * @return Query|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getChildren($id);

    /**
     * 空のQueryを返す
     *
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEmptyIndex(): Query;

    /**
     * getTreeIndex
     *
     * @param  array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTreeIndex(array $queryParams): Query;

    /**
     * getTableConditions
     *
     * @param  array $queryParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTableConditions(array $queryParams): array;

    /**
     * テーブル用のコンテンツ管理の一覧データを取得
     *
     * @param  array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTableIndex(array $queryParams): Query;

    /**
     * getTrashIndex
     * @param array $queryParams
     * @param string $type
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrashIndex(array $queryParams=[], string $type="all"): Query;

    /**
     * コンテンツフォルダーのリストを取得
     * コンボボックス用
     *
     * @param int $siteId
     * @param array $options
     * @return array|bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getContentFolderList($siteId = null, $options = []);

    /**
     * ツリー構造のデータを コンボボックスのデータ用に変換する
     * @param $nodes
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function convertTreeList($nodes);

    /**
     * aliasを作成する
     *
     * @param  array $postData
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function alias(array $postData);

    /**
     * コンテンツ情報を削除する
     * @param int $id
     * @param bool $enableTree(デフォルト:false) TreeBehaviorの有無
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDelete($id, $enableTree = false): bool;

    /**
     * コンテンツ情報と紐付いてるモデルを削除する
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDeleteWithAssoc($id): bool;

    /**
     * 該当するコンテンツ情報をすべて論理削除する
     *
     * @param  array $conditions
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteAll(array $conditions): int;

    /**
     * 指定日時以前の該当する論理削除されたコンテンツ情報をすべて削除する
     *
     * @param  \Datetime $dateTime
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDeleteAll(\Datetime $dateTime): int;

    /**
     * 論理削除されたコンテンツを復元する
     *
     * @param  int $id
     * @return EntityInterface|array|null $trash
     * @checked
     * @noTodo
     * @unitTest
     */
    public function restore($id);

    /**
     * ゴミ箱内のコンテンツをすべて元に戻す
     *
     * @param  array $queryParams
     * @return int $count
     * @checked
     * @noTodo
     * @unitTest
     */
    public function restoreAll(array $queryParams = []): int;

    /**
     * コンテンツ情報を取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getContentsInfo();

    /**
     * 再帰的に削除
     *※ エイリアスの場合は直接削除
     * @param int $id
     * @return void
     * @throws Exception
     * @return bool $result
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteRecursive($id): bool;

    /**
     * レイアウトテンプレートを取得する
     *
     * @param $id
     * @return string $parentTemplate|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getParentLayoutTemplate($id);

    /**
     * コンテンツIDよりURLを取得する
     *
     * @param int $id
     * @return string URL
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUrl($url, $full = false, $useSubDomain = false, $base = false);

    /**
     * 公開状態にする
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish($id): EntityInterface;

    /**
     * 非公開状態にする
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish($id): EntityInterface;

    /**
     * exists
     *
     * @param  int $id
     * @param bool $withTrash ゴミ箱の物も含めるか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function move($origin, $target);

    /**
     * 移動元のコンテンツと移動先のディレクトリから移動が可能かチェックする
     *
     * @param int $currentId int 移動元コンテンツID
     * @param int $targetParentId int 移動先コンテンツID (ContentFolder)
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isMovable($currentId, $targetParentId);

    /**
     * ID を指定して公開状態かどうか判定する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isPublishById($id);

    /**
     * 公開状態を取得する
     *
     * @param Content $content コンテンツデータ
     * @return bool 公開状態
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAllowPublish($content, $self = false);

    /**
     * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
     *
     * @param $url
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function existsContentByUrl($url);

    /**
     * タイトル、URL、公開状態が更新されているか確認する
     *
     * @param int $id コンテンツID
     * @param array $newData 新しいコンテンツデータ
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isChangedStatus($id, $newData);

    /**
     * TreeBehaviorの設定値を更新する
     *
     * @param  string $targetConfig
     * @param  array $conditions
     * @return TreeBehavior
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setTreeConfig($targetConfig, $conditions);

    /**
     * 公開済の conditions を取得
     *
     * @return array 公開条件（conditions 形式）
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getConditionAllowPublish();

    /**
     * 条件に基づいて指定したフィールドの隣のデータを所得する
     *
     * @param  array $options
     * @return array $neighbors
     * @throws BcException site_idがない場合Exceptionを投げる
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNeighbors(array $options);


    /**
     * エンコードされたURLをデコードせずにパースする
     * ※DBのレコードがエンコードされたまま保存されてる場合があるためその値を取得する際にデコードが邪魔になる際使用する
     * @param  string $fullUrl
     * @return array $parsedUrl
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function encodeParsedUrl($fullUrl);

    /**
     * ツリー構造のパスを取得する
     * @param string $id
     * @return QueryInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPath($id): QueryInterface;

    /**
     * IDを指定してタイトルリストを取得する
     * @param $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTitlesById($ids): array;

    /**
     * 一括処理
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch($method, array $ids): bool;

}
