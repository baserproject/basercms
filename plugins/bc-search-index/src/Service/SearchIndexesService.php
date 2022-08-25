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

namespace BcSearchIndex\Service;

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use BcSearchIndex\Model\Table\SearchIndexesTable;
use BaserCore\Utility\BcContainerTrait;
use Cake\Utility\Inflector;

/**
 * Class SearchIndexesService
 * @property SearchIndexesTable $SearchIndexes
 */
class SearchIndexesService implements SearchIndexesServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * SearchIndexes Table
     * @var SearchIndexesTable
     */
    public $SearchIndexes;

    /**
     * SearchIndexesService constructor.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function __construct()
    {
        $this->SearchIndexes = TableRegistry::getTableLocator()->get('BcSearchIndex.SearchIndexes');
    }

    /**
     * 索引を取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->SearchIndexes->get($id);
    }

    /**
     * 一覧データを取得
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     */
    public function getIndex(array $queryParams = []): Query
    {
        $query = $this->SearchIndexes->find()->order([
            'SearchIndexes.priority DESC',
            'SearchIndexes.modified DESC',
            'SearchIndexes.id'
        ]);
        if (!empty($queryParams['limit'])) {
            $query->limit($queryParams['limit']);
            unset($queryParams['limit']);
        }
        if (!empty($queryParams)) {
            $query->where($this->createAdminIndexConditions($queryParams));
        }
        return $query;
    }

    /**
     * 管理画面ページ一覧の検索条件を取得する
     *
     * @param array $data
     * @return array
     */
    protected function createAdminIndexConditions($data)
    {
        if (empty($data)) {
            return [];
        }

        $conditions = [];
        $type = $status = $keyword = $folderId = $siteId = null;
        if (isset($data['type'])) {
            $type = $data['type'];
        }
        if (isset($data['status'])) {
            $status = $data['status'];
        }
        if (isset($data['keyword'])) {
            $keyword = $data['keyword'];
        }
        if (isset($data['folder_id'])) {
            $folderId = $data['folder_id'];
        }
        if (isset($data['site_id'])) {
            $siteId = $data['site_id'];
        }

        unset($data['type']);
        unset($data['status']);
        unset($data['keyword']);
        unset($data['folder_id']);
        unset($data['site_id']);
        unset($data['site_id']);
        if (empty($data['priority'])) {
            unset($data['priority']);
        }
        foreach($data as $key => $value) {
            if (preg_match('/priority_[0-9]+$/', $key)) {
                unset($data[$key]);
            }
        }
        if (isset($data['priority'])) {
            $conditions['SearchIndexes.priority'] = $data['priority'];
        }
        if ($type) {
            $conditions['SearchIndexes.type'] = $type;
        }
        if ($siteId) {
            $conditions['SearchIndexes.site_id'] = $siteId;
        } else {
            $conditions['SearchIndexes.site_id'] = 0;
        }
        if ($folderId) {
            $content = $this->Content->find('first', ['fields' => ['lft', 'rght'], 'conditions' => ['Content.id' => $folderId], 'recursive' => -1]);
            $conditions['SearchIndexes.rght <'] = $content['Content']['rght'];
            $conditions['SearchIndexes.lft >'] = $content['Content']['lft'];
        }
        if ($status != '') {
            $conditions['SearchIndexes.status'] = $status;
        }
        if ($keyword) {
            $conditions['and']['or'] = [
                'SearchIndexes.title LIKE' => '%' . $keyword . '%',
                'SearchIndexes.detail LIKE' => '%' . $keyword . '%'
            ];
        }

        return $conditions;
    }

    /**
     * 検索インデックス再構築
     *
     * @param int $parentContentId 親となるコンテンツID
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function reconstruct($parentContentId = null)
    {
        $Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');;
        $conditions = [
            'OR' => [
                ['Sites.status IS' => null],
                ['Sites.status' => true]
            ]];
        if ($parentContentId) {
            $parentContent = $Contents->find()->select(['lft', 'rght'])->where(['id' => $parentContentId])->first();
            $conditions = array_merge($conditions, [
                'lft >' => $parentContent->lft,
                'rght <' => $parentContent->rght
            ]);
        }
        $contents = $Contents->find()->contain(['Sites'])->where($conditions)->order('lft')->all();
        $models = [];
        $db = $this->SearchIndexes->getConnection();
        $db->begin();

        if (!$parentContentId) {
            $sql = $this->SearchIndexes->getSchema()->truncateSql($this->SearchIndexes->getConnection());
            $this->SearchIndexes->getConnection()->execute($sql[0])->execute();
        }

        $result = true;
        if ($contents) {
            foreach($contents as $content) {
                $tableName = Inflector::pluralize($content->type);
                if (isset($models[$content->type])) {
                    $table = $models[$content->type];
                } else {
                    $models[$content->type] = $table = TableRegistry::getTableLocator()->get($content->plugin . '.' . $tableName);
                }
                // データの変更はないがイベントを走らせるために setNew() を実行
                $entity = $table->find()->contain(['Contents'])->where([$tableName . '.id' => $content->entity_id])->first();
                if ($entity && $entity->setNew(true) && !$table->save($entity)) {
                    $result = false;
                }
            }
        }
        if ($result) {
            $db->commit();
        } else {
            $db->rollback();
        }
        return $result;
    }

    /**
     * 公開状態確認
     *
     * @param array $data
     * @return bool|int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function allowPublish($data)
    {
        return $this->SearchIndexes->allowPublish($data);
    }

}
