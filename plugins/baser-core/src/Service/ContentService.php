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

use Cake\Utility\Hash;
use Exception;
use Cake\ORM\Query;
use Nette\Utils\DateTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Entity\Content;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\SitesTable;
use Cake\Datasource\ConnectionManager;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Class ContentService
 * @package BaserCore\Service
 * @property ContentsTable $Contents
 */
class ContentService implements ContentServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Contents
     *
     * @var ContentsTable
     */
    public $Contents;

    /**
     * Sites
     *
     * @var SitesTable
     */
    public $Sites;

    public function __construct()
    {
        $this->Contents = TableRegistry::getTableLocator()->get("BaserCore.Contents");
        $this->Sites = TableRegistry::getTableLocator()->get("BaserCore.Sites");
    }


    /**
     * コンテンツを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Contents->get($id, [
            'contain' => ['Sites'],
        ]);
    }

    /**
     * ゴミ箱のコンテンツを取得する
     * @param int $id
     * @return EntityInterface|array
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrash($id)
    {
        return $this->Contents->getTrash($id);
    }

    /**
     * コンテンツの子要素を取得する
     *
     * @param  int $id
     * @return Query|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getChildren($id)
    {
        try {
            $query = $this->Contents->find('children', ['for' => $id]);
        } catch (\Exception $e) {
            return null;
        }
        return $query->isEmpty() ? null : $query;
    }

    /**
     * 空のQueryを返す

     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEmptyIndex(): Query
    {
        return $this->getIndex(['site_id' => 0]);
    }

    /**
     * getTreeIndex
     *
     * @param  array $queryParams
     * @return Query
     * @checked
     * @unitTest
     */
    public function getTreeIndex(array $queryParams): Query
    {
        // ツリーの全体確認テスト用の条件（実運用では使わない）
//        $site = $this->Contents->Sites->getRootMain();
//        if ($queryParams['site_id'] === 'all') {
//            $queryParams = ['or' => [
//                ['Sites.use_subdomain' => false],
//                ['Contents.site_id' => 1]
//            ]];
//        }
        return $this->getIndex($queryParams, 'threaded')->order(['lft']);
    }

    /**
     * テーブルインデックス用の条件を返す
     *
     * @param  array $queryParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTableConditions(array $queryParams): array
    {
        $options = [];
        $conditions['site_id'] = $queryParams['site_id'];

        if (!empty($queryParams['withTrash'])) {
            $conditions['withTrash'] = $queryParams['withTrash'];
            if ($conditions['withTrash']) {
                $options = array_merge($options, ['withDeleted']);
            }
        }

        if ($queryParams['name']) {
            $conditions['OR'] = [
                'name LIKE' => '%' . $queryParams['name'] . '%',
                'title LIKE' => '%' . $queryParams['name'] . '%'
            ];
            $conditions['name'] = $queryParams['name'];
        }
        if ($queryParams['folder_id']) {
            $Contents = $this->Contents->find('all', $options)->select(['lft', 'rght'])->where(['id' => $queryParams['folder_id']]);
            $conditions['rght <'] = $Contents->first()->rght;
            $conditions['lft >'] = $Contents->first()->lft;
        }
        if ($queryParams['author_id']) {
            $conditions['author_id'] = $queryParams['author_id'];
        }
        if ($queryParams['self_status'] !== '') {
            $conditions['self_status'] = $queryParams['self_status'];
        }
        if ($queryParams['type']) {
            $conditions['type'] = $queryParams['type'];
        }

        return $conditions;
    }

    /**
     * コンテンツ管理の一覧用のデータを取得
     * @param array $queryParams
     * @param string $type
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams=[], ?string $type="all"): Query
    {
        $columns = ConnectionManager::get('default')->getSchemaCollection()->describe('contents')->columns();

        $query = $this->Contents->find($type)->contain(['Sites']);

        if (!empty($queryParams['withTrash'])) {
            $query = $query->applyOptions(['withDeleted']);
        }

        if (!empty($queryParams['name'])) {
            $query = $query->where(['OR' => [
                'Contents.name LIKE' => '%' . $queryParams['name'] . '%',
                'Contents.title LIKE' => '%' . $queryParams['name'] . '%'
            ]]);
            unset($queryParams['name']);
        }

        if (!empty($queryParams['title'])) {
            $query = $query->andWhere(['Contents.title LIKE' => '%' . $queryParams['title'] . '%']);
        }

        foreach($queryParams as $key => $value) {
            if (in_array($key, $columns)) {
                $query = $query->andWhere(['Contents.' . $key => $value]);
            } elseif ($key[-1] === '!' && in_array($key = mb_substr($key, 0, -1), $columns)) {
                $query = $query->andWhere(['Contents.' . $key . " IS NOT " => $value]);
            }
        }

        if (!empty($queryParams['num'])) {
            $query = $query->limit($queryParams['num']);
        }

        return $query;
    }

    /**
     * テーブル用のコンテンツ管理の一覧データを取得
     *
     * @param  array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTableIndex(array $queryParams): Query
    {

        $conditions = [
            'open' => '1',
            'name' => '',
            'folder_id' => '',
            'type' => '',
            'self_status' => '1',
            'author_id' => '',
        ];
        if ($queryParams) {
            $queryParams = array_merge($conditions, $queryParams);
        }
        return $this->getIndex($this->getTableConditions($queryParams));
    }


    /**
     * getTrashIndex
     * @param array $queryParams
     * @param string $type
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrashIndex(array $queryParams=[], string $type="all"): Query
    {
        $queryParams = array_merge($queryParams, ['withTrash' => true]);
        return $this->getIndex($queryParams, $type)->where(['deleted_date IS NOT NULL']);
    }

    /**
     * コンテンツフォルダーのリストを取得
     * コンボボックス用
     *
     * @param int $siteId
     * @param array $options
     * @return array|bool
     * @checked

     * @unitTest
     */
    public function getContentFolderList($siteId = null, $options = [])
    {
        $options = array_merge([
            'excludeId' => null
        ], $options);

        $conditions = [
            'type' => 'ContentFolder',
            'alias_id IS NULL'
        ];

        if (!is_null($siteId)) {
            $conditions['site_id'] = $siteId;
        }
        if ($options['excludeId']) {
            $conditions['id <>'] = $options['excludeId'];
        }
        if (!empty($options['conditions'])) {
            $conditions = array_merge($conditions, $options['conditions']);
        }
        $folders = $this->Contents->find('treeList')->where([$conditions]);
        if ($folders) {
            return $this->convertTreeList($folders->all()->toArray());
        }
        return false;
    }

    /**
     * ツリー構造のデータを コンボボックスのデータ用に変換する
     * @param array $nodes
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function convertTreeList($nodes)
    {
        if (!$nodes) {
            return [];
        }
        foreach($nodes as $key => $value) {
            if (preg_match("/^([_]+)/i", $value, $matches)) {
                $value = preg_replace("/^[_]+/i", '', $value);
                $prefix = str_replace('_', '　　　', $matches[1]);
                $value = $prefix . '└' . $value;
            }
            $nodes[$key] = $value;
        }
        return $nodes;
    }

    /**
     * コンテンツ登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $content = $this->Contents->newEmptyEntity();
        $content = $this->Contents->patchEntity($content, $postData, ['validate' => 'default']);
        return ($result = $this->Contents->save($content)) ? $result : $content;
    }

    /**
     * コンテンツ情報を論理削除する
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id)
    {
        $content = $this->get($id);
        return $this->Contents->delete($content);
    }

    /**
     * コンテンツ情報を削除する
     * @param int $id
     * @param bool $enableTree(デフォルト:false) TreeBehaviorの有無
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDelete($id, $enableTree = false): bool
    {
        $content = $this->getTrash($id);
        return $this->Contents->hardDel($content, $enableTree);
    }

    /**
     * コンテンツ情報と紐付いてるモデルを物理削除する
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDeleteWithAssoc($id): bool
    {
        $content = $this->getTrash($id);
        $service = $content->plugin . '\\Service\\' . $content->type . 'ServiceInterface';
        if(interface_exists($service)) {
            $target = $this->getService($service);
        } else {
            $target = TableRegistry::getTableLocator()->get($content->plugin . Inflector::pluralize($content->type));
        }
        if($target) {
            try {
                $result = $target->delete($content->entity_id);
            } catch (\Exception $e) {
                $result = false;
            }
        }
        return $result;
    }


    /**
     * 該当するコンテンツ情報をすべて論理削除する
     *
     * @param  array $conditions
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteAll(array $conditions=[]): int
    {
        $conditions = array_merge(['deleted_date IS NULL'], $conditions);
        return $this->Contents->deleteAll($conditions);
    }

    /**
     * 指定日時以前の該当する論理削除されたコンテンツ情報をすべて物理削除する
     *
     * @param  Datetime $dateTime
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDeleteAll(Datetime $dateTime): int
    {
        return $this->Contents->hardDeleteAll($dateTime);
    }

    /**
     * コンテンツを削除する（論理削除）
     *
     * ※ エイリアスの場合は直接削除
     * @param int $id
     * @return bool
     * @checked
     * @unitTest
     */
    public function treeDelete($id): bool
    {
        try {
            $content = $this->get($id);
        } catch (RecordNotFoundException $e) {
            return false;
        }

        if ($content->alias_id) {
            $result = $this->Contents->removeFromTree($content);
        } else {
            // $result = $this->Contents->softDeleteFromTree($id); TODO: キャッシュ系が有効化されてからsoftDeleteFromTreeを使用する
            $result = $this->deleteRecursive($id); // 一時措置
        }

        return $result;
    }

    /**
     * 論理削除されたコンテンツを復元する
     *
     * @param  int $id
     * @return EntityInterface|array|null $trash
     * @checked
     * @noTodo
     * @unitTest
     */
    public function restore($id)
    {
        $trash = $this->getTrash($id);
        return $this->Contents->restore($trash) ? $trash : null;
    }

    /**
     * ゴミ箱内のコンテンツをすべて元に戻す
     *
     * @param  array $queryParams
     * @return int $count
     * @checked
     * @noTodo
     * @unitTest
     */
    public function restoreAll(array $queryParams = []): int
    {
        $count = 0;
        $trash = $this->getTrashIndex($queryParams);
        foreach ($trash as $entity) {
            if ($this->Contents->restore($entity)) {
                $count++;
            }
        }
        return $count;
    }

    /**
      * コンテンツ情報を取得する
      * @return array
      */
    public function getContensInfo ()
    {
        $sites = $this->Sites->getPublishedAll();
        $contentsInfo = [];
        foreach($sites as $key => $site) {
            $contentsInfo[$key]['published'] = $this->Contents->find()
                    ->where(['site_id' => $site->id, 'status' => true])
                    ->count();
            $contentsInfo[$key]['unpublished'] = $this->Contents->find()
                    ->where(['site_id' => $site->id, 'status' => false])
                    ->count();
            $contentsInfo[$key]['total'] = $contentsInfo[$key]['published'] + $contentsInfo[$key]['unpublished'];
            $contentsInfo[$key]['display_name'] = $site->display_name;
        }
        return $contentsInfo;
    }

    /**
     * ツリー構造より論理削除する
     * TODO: キャッシュビヘイビアー実装後復活させる
     * @param $id
     * @return bool
     */
    // public function softDeleteFromTree($id)
    // {
    //     // TODO:　キャッシュ系をオフにする
    //     // $this->softDelete(true);
    //     // $this->Behaviors->unload('BcCache');
    //     // $this->Behaviors->unload('BcUpload');
    //     $result = $this->deleteRecursive($id);
    //     // $this->Behaviors->load('BcCache');
    //     // $this->Behaviors->load('BcUpload');
    //     // $this->delAssockCache();
    //     return $result;
    // }

/**
     * 再帰的に論理削除
     *
     * エイリアスの場合
     *
     * @param int $id
     * @return bool $result
     * @checked
     * @unitTest
     */
    public function deleteRecursive($id): bool
    {
        if (!$id) {
            return false;
        }
        $parent = $this->get($id);

        if ($children = $this->getChildren($id)) {
            // 親から消していくとTreeBehaviorにより削除重複が起きるため、子要素から削除する
            $target = array_reverse(array_merge([$parent], $children->toArray()));
        } else {
            $target = [$parent];
        }

        foreach($target as $node) {
            if (empty($node->alias_id)) {
                // エイリアス以外の場合
                // 一旦階層構造から除外しリセットしてゴミ箱に移動（論理削除）
                $node->parent_id = null;
                $node->url = '';
                $node->status = false;
                $node->self_status = false;
                unset($node->lft);
                unset($node->rght);
                // TODO: $this->updatingSystemDataのsetter getterを用意する必要あり
                $this->updatingSystemData = false;
                // ここでは callbacks を false にすると lft rght が更新されないので callbacks は true に設定する（default: true）
                // $this->clear(); // TODO: これは何か再確認する humuhimi
                $this->Contents->save($node, ['validate' => false]); // 論理削除用のvalidationを用意するべき
                $this->updatingSystemData = true;
                $result = $this->Contents->delete($node);
                // =====================================================================
                // 通常の削除の際、afterDelete で、関連コンテンツのキャッシュを削除しているが、
                // 論理削除の場合、afterDelete が呼ばれない為、ここで削除する
                // =====================================================================
                $this->Contents->deleteAssocCache($node);
            } else {
                // エイリアスの場合、直接削除
                $result = $this->Contents->removeFromTree($node);
            }
            if (!$result) return false;
        }
        return $result;
    }

    /**
     * 直属の親フォルダのレイアウトテンプレートを取得する
     *
     * @param $id
     * @return string $parentTemplate|false
     */
    public function getParentLayoutTemplate($id)
    {
        if (!$id) {
            return false;
        }
        // ===========================================================================================
        // 2016/09/22 ryuring
        // PHP 7.0.8 環境にて、コンテンツ一覧追加時、検索インデックス作成のため、BcContentsComponent が
        // 呼び出されるが、その際、モデルのマジックメソッドの戻り値を返すタイミングで処理がストップしてしまう。
        // そのため、ビヘイビアのメソッドを直接実行して対処した。
        // CakePHPも、PHP自体のエラーも発生せず、ただ止まる。PHP7のバグ？PHP側のメモリーを256Mにしても変わらず。
        // ===========================================================================================
        $contents = $this->Contents->find('path', ['for' => $id])->all()->toArray();
        $contents = array_reverse($contents);
        unset($contents[0]);
        if (!$contents) {
            return false;
        }
        $parentTemplates = Hash::extract($contents, '{n}.layout_template');
        foreach($parentTemplates as $parentTemplate) {
            if ($parentTemplate) {
                break;
            }
        }
        return $parentTemplate;
    }

}
