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

namespace BcBlog\Model\Table;

use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BlogCategoriesTable
 * @property BlogCategoriesTable $BlogCategoriesTable
 */
class BlogCategoriesTable extends BlogAppTable
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * バリデーション設定
     *
     * @var array
     */
    public $validationParams = [];

    /**
     * actsAs
     *
     * @var array
     */
    public $actsAs = ['Tree', 'BcCache'];

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('blog_categories');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('Tree');
        $this->hasMany('BlogPosts', [
            'className' => 'BcBlog.BlogPosts',
            'order' => 'posted DESC',
            'limit' => 10,
            'foreignKey' => 'blog_category_id',
            'dependent' => false,
            'exclusive' => false,
        ]);

        $this->belongsTo('BlogContents', [
            'className' => 'BcBlog.BlogContents',
            'foreignKey' => 'blog_content_id',
        ]);
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->setProvider('blogCategory', 'BcBlog\Model\Validation\BlogCategoryValidation');

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255, __d('baser_core', 'カテゴリ名は255文字以内で入力してください。'))
            ->requirePresence('name', 'create', __d('baser_core', 'カテゴリ名を入力してください。'))
            ->notEmptyString('name', __d('baser_core', 'カテゴリ名を入力してください。'))
            ->add('name', [
                'alphaNumericDashUnderscore' => [
                    'rule' => ['alphaNumericDashUnderscore'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'カテゴリ名はは半角英数字とハイフン、アンダースコアのみが利用可能です。')]])
            ->add('name', [
                'duplicateBlogCategory' => [
                    'rule' => ['duplicateBlogCategory'],
                    'provider' => 'blogCategory',
                    'message' => __d('baser_core', '入力されたカテゴリ名は既に登録されています。')]]);

        $validator
            ->scalar('title')
            ->maxLength('title', 50, __d('baser_core', 'カテゴリタイトルは255文字以内で入力してください。'))
            ->requirePresence('title', 'create', __d('baser_core', 'カテゴリタイトルを入力してください。'))
            ->notEmptyString('title', __d('baser_core', 'カテゴリタイトルを入力してください。'));

        return $validator;
    }

    /**
     * 関連する記事データをカテゴリ無所属に変更し保存する
     *
     * @param boolean $cascade
     * @return boolean
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, \ArrayObject $options)
    {
        // TODO ucmitz 未実装
        return true;
        $ret = true;
        if (!empty($this->data['BlogCategory']['id'])) {
            $id = $this->data['BlogCategory']['id'];
            $this->BlogPost->unBindModel(['belongsTo' => ['BlogCategory']]);
            $datas = $this->BlogPost->find('all', ['conditions' => ['BlogPost.blog_category_id' => $id]]);
            if ($datas) {
                foreach ($datas as $data) {
                    $data['BlogPost']['blog_category_id'] = '';
                    $this->BlogPost->set($data);
                    if (!$this->BlogPost->save()) {
                        $ret = false;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * カテゴリリストを取得する
     *
     * @param int $blogContentId
     * @param array $options
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     *
     */
    public function getCategoryList($blogContentId = null, $options = [])
    {
        $options = array_merge([
            'siteId' => null,
            'depth' => 1,
            'type' => null,
            'limit' => false,
            'viewCount' => false,
            'parentId' => null,
            'fields' => [
                'BlogCategories.id',
                'BlogCategories.name',
                'BlogCategories.title',
                'BlogCategories.lft',
                'BlogCategories.rght'
            ],
        ], $options);
        $fields = $options['fields'];
        $depth = $options['depth'];
        $parentId = $options['parentId'];
        unset($options['fields'], $options['depth'], $options['parentId']);
        $datas = [];
        if (!$options['type']) {
            $datas = $this->_getCategoryList($blogContentId, $parentId, $options['viewCount'], $depth, 1, $fields, $options);
        } elseif ($options['type'] == 'year') {
            $postedDates = $this->BlogPosts->getPostedDates($blogContentId, [
                'category' => true,
                'limit' => $options['limit'],
                'viewCount' => $options['viewCount'],
                'type' => 'year'
            ]);
            foreach ($postedDates as $postedDate) {
                if (empty($postedDate['category'])) continue;
                if ($options['viewCount']) $postedDate['category']->count = $postedDate['count'];
                $datas[$postedDate['year']][] = $postedDate['category'];
            }
        }
        return $datas;
    }

    /**
     * カテゴリリストを取得する（再帰処理）
     *
     * @param int $blogContentId
     * @param int $parentId
     * @param bool $viewCount
     * @param int $depth
     * @param int $current
     * @param array $fields
     * @param array $options
     * @return ResultSetInterface
     * @noTodo
     * @checked
     * @unitTest
     */
    protected function _getCategoryList(
        int   $blogContentId = null,
        int   $parentId = null,
        bool  $viewCount = false,
        int   $depth = 1,
        int   $current = 1,
        array $fields = [],
        array $options = [])
    {
        $options = array_merge([
            'id' => null,
            'siteId' => null,
            'order' => 'BlogCategories.lft asc',
            'conditions' => [],
            'threaded' => false
        ], $options);

        // 検索条件
        $conditions = $options['conditions'];
        if (!empty($options['id'])) {
            $parentId = false;
            $conditions['BlogCategories.id'] = $options['id'];
        }
        if (is_null($parentId)) {
            $conditions['BlogCategories.parent_id IS'] = null;
        } elseif ($parentId !== false) {    // 親を指定する場合
            $conditions['BlogCategories.parent_id'] = $parentId;
        }
        if ($options['siteId'] !== false && !is_null($options['siteId'])) {
            $conditions['Contents.site_id'] = $options['siteId'];
        }
        if (!is_null($blogContentId)) {
            $conditions['BlogCategories.blog_content_id'] = $blogContentId;
        }

        // 検索設定
        $findType = 'all';
        if ($options['threaded']) {
            $findType = 'threaded';
            $options['order'] = 'BlogCategories.lft';
            unset($conditions['BlogCategories.parent_id']);
            $fields = [];
        } else {
            if ($fields) {
                if (is_array($fields)) {
                    $distinct = [$fields[0], 'BlogCategories.lft'];
                } else {
                    $distinct = [$fields, 'BlogCategories.lft'];
                }
            }
        }

        // 検索実行
        $query = $this->find($findType)
            ->contain(['BlogPosts' => ['BlogContents' => ['Contents']]])
            ->where($conditions)
            ->select($fields)
            ->order($options['order']);
        if ($distinct) {
            $query->distinct($distinct);
        }
        $entities = $query->all();

        // all の場合に、付属情報を追加
        if ($findType == 'all' && $entities) {
            foreach ($entities as $entity) {
                // 表示件数
                if ($viewCount) {
                    $childrenIds = $this->find('list', ['keyField' => 'id', 'valueField' => 'id'])
                        ->where([
                            ['BlogCategories.lft > ' => $entity->lft],
                            ['BlogCategories.rght < ' => $entity->rght]
                        ])->toArray();
                    $categoryId = [$entity->id];
                    if ($childrenIds) {
                        $categoryId = array_merge($categoryId, $childrenIds);
                    }
                    $entity->count = $this->BlogPosts->find()
                        ->where(array_merge(
                            ['BlogPosts.blog_category_id IN' => $categoryId],
                            $this->BlogPosts->getConditionAllowPublish()
                        ))->count();
                }
                // 子カテゴリ
                if ($current < $depth) {
                    $children = $this->_getCategoryList(
                        $blogContentId,
                        $entity->id,
                        $viewCount,
                        $depth,
                        $current + 1,
                        $fields,
                        $options
                    );
                    if ($children) $entity->children = $children;
                }
            }
        }
        return $entities;
    }

    /**
     * アクセス制限としてカテゴリの新規追加ができるか確認する
     *
     * @param array $userGroupId ユーザーグループID
     * @param int $blogContentId ブログコンテンツID
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hasNewCategoryAddablePermission($userGroupId, $blogContentId)
    {
        /* @var PermissionsService $permissionsService */
        $permissionsService = $this->getService(PermissionsServiceInterface::class);
        $addUrl = preg_replace('|^/index.php|', '', Router::url([
            'plugin' => 'BcBlog',
            'prefix' => 'Api/Admin',
            'controller' => 'BlogCategories',
            'action' => 'add',
            $blogContentId
        ]));
        return $permissionsService->check($addUrl, $userGroupId);
    }

    /**
     * 子カテゴリを持っているかどうか
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     */
    public function hasChild($id)
    {
        return (bool)$this->childCount($id);
    }

    /**
     * カテゴリ名よりカテゴリを取得
     *
     * @param int $blogContentId
     * @param string $name
     * @param array $options
     * @return array|null
     */
    public function getByName($blogContentId, $name, $options = [])
    {
        $options = array_merge([
            'conditions' => [
                'BlogCategory.blog_content_id' => $blogContentId,
                'BlogCategory.name' => urlencode($name),
            ],
            'recursive' => -1
        ], $options);
        $this->unbindModel(['hasMany' => ['BlogPost']]);
        return $this->find('first', $options);
    }

    /**
     * コピーする
     *
     * @param $id
     * @param null $newParentId
     * @return EntityInterface page Or false
     * @throws \Throwable
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy($id, $newParentId = null)
    {
        $entity = $this->get($id);
        $oldEntity = clone $entity;

        // EVENT BlogCategories.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $entity,
            'id' => $id,
        ]);
        if ($event !== false) {
            $entity = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
        }

        $entity->name .= '_copy';
        $entity->parent_id = $newParentId;
        $entity->no = $this->getMax('no', ['BlogCategories.blog_content_id' => $entity->blog_content_id]) + 1;
        unset($entity->id);
        unset($entity->created);
        unset($entity->modified);

        try {
            $entity = $this->saveOrFail($this->patchEntity($this->newEmptyEntity(), $entity->toArray()));

            // EVENT BlogCategories.afterCopy
            $this->dispatchLayerEvent('afterCopy', [
                'id' => $entity->id,
                'data' => $entity,
                'oldId' => $id,
                'oldData' => $oldEntity,
            ]);

            return $entity;
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
