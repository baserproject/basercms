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

use BaserCore\Error\BcException;
use Cake\Core\Plugin;
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
     * @unitTest
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
            $query->where($this->createIndexConditions($queryParams));
        }
        return $query;
    }

    /**
     * 管理画面ページ一覧の検索条件を取得する
     *
     * @param array $data
     * @return array
     * @checked
     * @noTodo
     */
    protected function createIndexConditions($options)
    {
        foreach($options as $key => $value) {
            if (preg_match('/priority_[0-9]+$/', $key) || $value === '') {
                unset($options[$key]);
            }
        }
        if (empty($options)) return [];

        $options = array_merge([
            'keyword' => null,
            'site_id' => 1,
            'content_id' => null,
            'content_filter_id' => null,
            'type' => null,
            'model' => null,
            'priority' => null,
            'status' => null,
            'folder_id' => null,
            'cf' => null,
            'm' => null,
            's' => null,
            'c' => null,
            'f' => null,
            'q' => null
        ], $options);

        if (!is_null($options['s'])) $options['site_id'] = $options['s'];
        if (!is_null($options['c'])) $options['content_id'] = $options['c'];
        if (!is_null($options['cf'])) $options['content_filter_id'] = $options['cf'];
        if (!is_null($options['m'])) $options['model'] = $options['m'];
        if (!is_null($options['f'])) $options['folder_id'] = $options['f'];
        if (!is_null($options['q'])) $options['keyword'] = $options['q'];

        if($options['status'] === 'publish' || $options['status'] === '1') {
            $conditions = $this->SearchIndexes->getConditionAllowPublish();
        } else {
            $conditions = [];
        }
        if (!is_null($options['site_id'])) $conditions['SearchIndexes.site_id'] = $options['site_id'];
        if (!is_null($options['content_id'])) $conditions['SearchIndexes.content_id'] = $options['content_id'];
        if (!is_null($options['content_filter_id'])) $conditions['SearchIndexes.content_filter_id'] = $options['content_filter_id'];
        if (!is_null($options['type'])) $conditions['SearchIndexes.type'] = $options['type'];
        if (!is_null($options['model'])) $conditions['SearchIndexes.model'] = $options['model'];
        if (!is_null($options['priority'])) $conditions['SearchIndexes.priority'] = $options['priority'];
        if (!is_null($options['folder_id'])) {
            $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
            $content = $contentsTable->find()->select(['lft', 'rght'])->where(['Contents.id' => $options['folder_id']])->first();
            $conditions['SearchIndexes.rght <'] = $content->rght;
            $conditions['SearchIndexes.lft >'] = $content->lft;
        }
        if (!is_null($options['keyword'])) {
            $query = $this->parseQuery($options['keyword']);
            foreach($query as $key => $value) {
                $conditions['and'][$key]['or'][] = ['SearchIndexes.title LIKE' => "%{$value}%"];
                $conditions['and'][$key]['or'][] = ['SearchIndexes.detail LIKE' => "%{$value}%"];
            }
        }

        return $conditions;
    }

    /**
     * 検索キーワードを分解し配列に変換する
     *
     * @param string $query
     * @return array
     * @checked
     * @noTodo
     */
    protected function parseQuery($query)
    {
        $query = str_replace('　', ' ', $query);
        if (strpos($query, ' ') !== false) {
            $query = explode(' ', $query);
        } else {
            $query = [$query];
        }
        return h($query);
    }

    /**
     * 検索インデックスを削除する
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id): bool
    {
        $entity = $this->get($id);
        return $this->SearchIndexes->delete($entity);
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
        set_time_limit(0);
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $conditions = [
            'OR' => [
                ['Sites.status IS' => null],
                ['Sites.status' => true]
            ]];
        if ($parentContentId) {
            $parentContent = $contentsTable->find()->select(['lft', 'rght'])->where(['id' => $parentContentId])->first();
            $conditions = array_merge($conditions, [
                'lft >' => $parentContent->lft,
                'rght <' => $parentContent->rght
            ]);
        }
        $contents = $contentsTable->find()->contain(['Sites'])->where($conditions)->order('lft')->all();

        $db = $this->SearchIndexes->getConnection();
        $db->begin();

        if (!$parentContentId) {
            $this->SearchIndexes->deleteAll('1=1');
        } else {
            $this->SearchIndexes->deleteAll([
                'lft >' => $parentContent->lft,
                'rght <' => $parentContent->rght
            ]);
        }

        $contentsTable->disableUpdatingSystemData();
        $contentsTable->updatingRelated = false;
        $tables = [];
        $result = true;
        if ($contents) {
            foreach($contents as $content) {
                if(!Plugin::isLoaded($content->plugin)) continue;
                $tableName = Inflector::pluralize($content->type);
                if (!isset($tables[$tableName])) {
                    $tables[$tableName] = TableRegistry::getTableLocator()->get($content->plugin . '.' . $tableName);
                }
                $entity = $tables[$tableName]->find()->contain(['Contents'])->where([$tableName . '.id' => $content->entity_id])->first();
                // データの変更はないがイベントを走らせるために setNew() を実行
                if ($entity && $entity->setNew(true) && !$tables[$tableName]->save($entity)) {
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

    /**
     * 優先度を変更する
     * @param EntityInterface $target
     * @param $priority
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function changePriority(EntityInterface $target, $priority): ?EntityInterface
    {
        $searchIndex = $this->SearchIndexes->patchEntity($target, ['priority' => $priority]);
        return $this->SearchIndexes->saveOrFail($searchIndex);
    }

    /**
     * 一括処理
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->SearchIndexes->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->$method($id)) {
                $db->rollback();
                throw new BcException(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

}
