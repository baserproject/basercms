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

namespace BcBlog\Service;

use BaserCore\Error\BcException;
use BcBlog\Model\Entity\BlogCategory;
use BcBlog\Model\Table\BlogCategoriesTable;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BlogCategoriesService
 */
class BlogCategoriesService implements BlogCategoriesServiceInterface
{

    /**
     * @var BlogCategoriesTable|Table
     */
    public BlogCategoriesTable|Table $BlogCategories;

    /**
     * Construct
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogCategories = TableRegistry::getTableLocator()->get("BcBlog.BlogCategories");
    }

    /**
     * 単一レコードを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, array $queryParams = []): EntityInterface
    {
        $queryParams = array_merge([
            'status' => ''
        ], $queryParams);
        $conditions = $contain = [];
        if ($queryParams['status'] === 'publish') {
            $contain = ['BlogContents' => ['Contents']];
            $conditions = $this->BlogCategories->BlogContents->Contents->getConditionAllowPublish();
        }
        return $this->BlogCategories->get($id,
        conditions: $conditions,
        contain: $contain);
    }

    /**
     * 一覧を取得する
     *
     * @param int $blogContentId
     * @param array $queryParams
     * @param string $type
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(int $blogContentId, array $queryParams, $type = 'all'): Query
    {
        $queryParams = array_merge([
            'status' => ''
        ], $queryParams);
        $query = $this->BlogCategories->find($type);
        $query = $this->createIndexConditions($query, $blogContentId, $queryParams);
        return $query;
    }

    /**
     * createIndexConditions
     *
     * @param Query $query
     * @param int $blogContentId
     * @param array $params
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    private function createIndexConditions(Query $query, int $blogContentId, array $params = []): Query
    {
        foreach($params as $key => $value) {
            if ($value === '') unset($params[$key]);
        }
        $params = array_merge([
            'name' => null,
            'title' => null,
            'status' => ''
        ], $params);

        $conditions = [];
        if ($params['status'] === 'publish') {
            $fields = $this->BlogCategories->getSchema()->columns();
            $query = $query->contain(['BlogContents' => ['Contents']])
                ->select($fields);
            $conditions = $this->BlogCategories->BlogContents->Contents->getConditionAllowPublish();
            $conditions = array_merge($conditions, ['BlogCategories.status' => true]);
        }
        if ($blogContentId) {
            $conditions = array_merge($conditions, ['BlogCategories.blog_content_id' => $blogContentId]);
        }
        if(!is_null($params['name'])) {
            $conditions['BlogCategories.name LIKE'] = '%' . $params['name'] . '%';
        }
        if(!is_null($params['title'])) {
            $conditions['BlogCategories.title LIKE'] = '%' . $params['title'] . '%';
        }

        return $query->where($conditions);
    }

    /**
     * getTreeIndex
     *
     * @param int $blogContentId
     * @param array $queryParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTreeIndex(int $blogContentId, array $queryParams): array
    {
        $srcCategories = $this->getIndex($blogContentId, $queryParams, 'treeList')->orderBy(['lft'])->all();
        $categories = [];
        foreach ($srcCategories->toArray() as $key => $value) {
            /* @var BlogCategory $category */
            $category = $this->BlogCategories->find()->where(['BlogCategories.id' => $key])->first();
            if (!preg_match("/^([_]+)/i", $value, $matches)) {
                $category->depth = 0;
                $category->layered_title = $category->title;
                $categories[] = $category;
                continue;
            }
            $category->layered_title = sprintf(
                "%s└%s",
                str_replace('_', '　', $matches[1]),
                $category->title
            );
            $category->depth = strlen($matches[1]);
            $categories[] = $category;
        }
        return $categories;
    }

    /**
     * コントロールソース取得
     *
     * @param string $field
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource(string $field, array $options): mixed
    {
        switch ($field) {
            case 'parent_id':
                if (!isset($options['blogContentId'])) {
                    return false;
                }
                $conditions = [];
                if (isset($options['conditions'])) {
                    $conditions = $options['conditions'];
                }
                $conditions['BlogCategories.blog_content_id'] = $options['blogContentId'];
                if (!empty($options['excludeParentId'])) {
                    $children = $this->BlogCategories->find('children', for: $options['excludeParentId']);
                    $excludeIds = [$options['excludeParentId']];
                    foreach ($children as $child) {
                        $excludeIds[] = $child->id;
                    }
                    $conditions['NOT']['BlogCategories.id IN'] = $excludeIds;
                }
                $parents = $this->BlogCategories->find('treeList')->where($conditions)->orderBy(['lft'])->all();
                $controlSources['parent_id'] = [];
                foreach ($parents as $key => $parent) {
                    if (preg_match("/^([_]+)/i", $parent, $matches)) {
                        $parent = preg_replace("/^[_]+/i", '', $parent);
                        $prefix = str_replace('_', '　', $matches[1]);
                        $parent = $prefix . '└' . $parent;
                    }
                    $controlSources['parent_id'][$key] = $parent;
                }
                break;
        }

        return $controlSources[$field] ?? false;
    }

    /**
     * 新規エンティティ取得
     *
     * @param int $blogContentId
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(int $blogContentId): EntityInterface
    {
        return $this->BlogCategories->newEntity([
            'blog_content_id' => $blogContentId,
            'status' => true
        ], [
            'validate' => false,
        ]);
    }

    /**
     * 新規作成
     *
     * @param int $blogContentId
     * @param array $postData
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(int $blogContentId, array $postData): ?EntityInterface
    {
        $postData['no'] = $this->BlogCategories->getMax('no', [
                'BlogCategories.blog_content_id' => $blogContentId
            ]) + 1;
        $postData['blog_content_id'] = $blogContentId;
        $blogCategory = $this->BlogCategories->newEmptyEntity();
        $blogCategory = $this->BlogCategories->patchEntity($blogCategory, $postData);
        return $this->BlogCategories->saveOrFail($blogCategory);
    }

    /**
     * 更新する
     *
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface|null
     * @throws PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface
    {
        $blogCategory = $this->BlogCategories->patchEntity($target, $postData);
        return $this->BlogCategories->saveOrFail($blogCategory);
    }

    /**
     * 削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        try {
            $blogCategory = $this->BlogCategories->get($id);
            $result = $this->BlogCategories->deleteOrFail($blogCategory);
        } catch(RecordNotFoundException $e) {
            $result = true;
        }
        return $result;
    }

    /**
     * 一括処理
     *
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
        $db = $this->BlogCategories->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->$method($id)) {
                $db->rollback();
                throw new BcException(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * IDを指定して名前リストを取得する
     *
     * @param $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNamesById($ids): array
    {
        return $this->BlogCategories->find('list')->where(['id IN' => $ids])->toArray();
    }

    /**
     *ブログカテゴリーリスト取得

     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList($blogContentId, array $queryParams = []): array
    {
        $queryParams = array_merge([
            'status' => ''
        ], $queryParams);

        $query = $this->BlogCategories->find('list', keyField: 'id', valueField: 'title')
            ->contain(['BlogContents' => ['Contents']]);

        if ($queryParams['status'] === 'publish') {
            $query->where($this->BlogCategories->BlogContents->Contents->getConditionAllowPublish());
        }

        $conditions = [];
        if ($blogContentId) $conditions = ['BlogCategories.blog_content_id' => $blogContentId];
        return $query->where($conditions)->toArray();
    }
}
