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
                'alphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'カテゴリ名は半角英数字とハイフン、アンダースコアのみが利用可能です。')]])
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
     * @return void
     * @notodo
     * @checked
     * @unitTest
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, \ArrayObject $options)
    {
        $blogPosts = $this->BlogPosts->find()
            ->where(['BlogPosts.blog_category_id' => $entity->id])->toArray();
        foreach ($blogPosts as $item) {
            $item->blog_category_id = '';
            $this->BlogPosts->save($item);
        }
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
            'threaded' => false,
            'categoryPostCounts' => [],
        ], $options);
        if ($viewCount && !$options['threaded'] && empty($options['categoryPostCounts'])) {
            $options['categoryPostCounts'] = $this->getCategoryPostCounts($blogContentId);
        }

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
            ->where($conditions)
            ->select($fields)
            ->orderBy($options['order']);
        if ($distinct) {
            $query->distinct($distinct);
        }
        if ($options['siteId'] !== false && !is_null($options['siteId'])) {
            $query->matching('BlogPosts.BlogContents.Contents', function ($q) use ($options) {
                return $q->where(['Contents.site_id' => $options['siteId']]);
            });
        }
        $entities = $query->all();

        // all の場合に、付属情報を追加
        if ($findType == 'all' && $entities) {
            foreach ($entities as $entity) {
                // 表示件数
                if ($viewCount) {
                    $entity->count = $options['categoryPostCounts'][$entity->id] ?? 0;
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
     * カテゴリごとの記事数を集計
     *
     * @param int|null $blogContentId
     * @return array
     */
    private function getCategoryPostCounts(?int $blogContentId): array
    {
        $conditions = [];
        if ($blogContentId) {
            $conditions['BlogCategories.blog_content_id'] = $blogContentId;
        }
        $categories = $this->find()
            ->select(['id', 'lft', 'rght'])
            ->where($conditions)
            ->all()
            ->toList();
        if (!$categories) {
            return [];
        }

        $postCounts = [];
        $postConditions = [
            'blog_category_id IN' => array_column($categories, 'id'),
            ...$this->BlogPosts->getConditionAllowPublish()
        ];
        $query = $this->BlogPosts->find();
        $query->select([
                'blog_category_id',
                'post_count' => $query->func()->count('*'),
            ])
            ->where($postConditions)
            ->groupBy('blog_category_id');
        foreach ($query as $countRow) {
            $postCounts[$countRow['blog_category_id']] = $countRow['post_count'];
        }

        $categoryPostCounts = [];
        foreach ($categories as $category) {
            $categoryId = $category['id'];
            $totalCount = $postCounts[$categoryId] ?? 0;

            // 子カテゴリの記事件数を親へ加算
            foreach ($categories as $targetCategory) {
                $targetCategoryId = $targetCategory['id'];
                if ($targetCategoryId === $categoryId) {
                    continue;
                }
                if ($targetCategory['lft'] <= $category['lft'] || $targetCategory['rght'] >= $category['rght']) {
                    continue;
                }
                $totalCount += $postCounts[$targetCategoryId] ?? 0;
            }

            $categoryPostCounts[$categoryId] = $totalCount;
        }

        return $categoryPostCounts;
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
            'prefix' => 'Admin',
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
     * @unitTest
     */
    public function hasChild($id)
    {
        $entity = $this->find()->where(['id' => $id])->first();
        return (bool)$this->childCount($entity);
    }

    /**
     * カテゴリ名よりカテゴリを取得
     *
     * @param int $blogContentId
     * @param string $name
     * @param array $options
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getByName($blogContentId, $name, $options = [])
    {
        $options = array_merge([
            'conditions' => [
                'BlogCategories.blog_content_id' => $blogContentId,
                'BlogCategories.name' => urlencode($name),
            ],
            'recursive' => -1
        ], $options);
        return $this->find('all', $options)->first();

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

    /**
     * 親カテゴリを取得する
     * @param $parent_id
     * @return EntityInterface
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getParent($parent_id)
    {
        return $this->find()->where(['id' => $parent_id])->first();
    }

    /**
     * 同一階層（同じ親）における並び順を取得する
     *
     * 指定した親配下（親なしはルート）のカテゴリを lft 昇順に並べ、対象 id の 1 始まりの順位を返す。
     * id が空の場合は件数（＝末尾の位置）を返す。該当データが無い場合は false、
     * id に一致するカテゴリが無い場合は null。
     *
     * TreeBehavior はスコープを持たず lft/rght は全ブログ横断の単一フォレストで管理されるため、
     * ルート階層（親なし）では全ブログのルートカテゴリを対象とした順位を返す。
     * moveUp()/moveDown() が実際に移動する兄弟の並びと一致させるための仕様。
     *
     * @param int|null $id 対象カテゴリID（null で末尾の位置＝件数）
     * @param int|null $parentId 親カテゴリID（null でルート）
     * @return int|false|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getOrderSameParent($id, $parentId)
    {
        if ($parentId) {
            $conditions = ['BlogCategories.parent_id' => $parentId];
        } else {
            $conditions = ['BlogCategories.parent_id IS' => null];
        }
        $categories = $this->find()
            ->select(['BlogCategories.id', 'BlogCategories.parent_id', 'BlogCategories.title'])
            ->where($conditions)
            ->orderBy('BlogCategories.lft');
        if ($categories->all()->isEmpty()) {
            return false;
        }
        if (!$id) {
            return $categories->all()->count();
        }
        $order = null;
        foreach ($categories as $key => $category) {
            if ($id == $category->id) {
                $order = $key + 1;
                break;
            }
        }
        return $order;
    }

    /**
     * オフセットを元にカテゴリを移動する
     *
     * TreeBehavior により、同一階層（兄弟）内で指定位置数だけ上下に移動する。
     * オフセットが正の場合は下へ、負の場合は上へ移動し、0 の場合は何もしない。
     * 子カテゴリを持つ場合は、その子孫も含めて移動する。
     *
     * @param int $id 移動対象のカテゴリID
     * @param int $offset 移動する位置数（正: 下、負: 上）
     * @return EntityInterface|bool 成功時は対象エンティティ、失敗時は false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function moveOffset(int $id, int $offset)
    {
        $category = $this->get($id);
        if ($offset > 0) {
            $result = $this->moveDown($category, abs($offset));
        } elseif ($offset < 0) {
            $result = $this->moveUp($category, abs($offset));
        } else {
            $result = true;
        }
        return $result ? $category : false;
    }

    /**
     * ブログカテゴリのツリー構造をリセットする
     *
     * コンテンツ管理のリセットと同様に、全てのカテゴリをルート直下にフラット化し、
     * lft / rght を現在の並び順（lft 昇順）で振り直す。
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetTree(): bool
    {
        $this->removeBehavior('Tree');
        $this->getConnection()->begin();
        $result = true;
        $categories = $this->find()->orderBy(['BlogCategories.lft' => 'ASC'])->all();
        $count = 0;
        foreach($categories as $category) {
            $count++;
            $category->parent_id = null;
            $category->lft = $count;
            $count++;
            $category->rght = $count;
            if (!$this->save($category)) $result = false;
        }
        $this->addBehavior('Tree');
        if (!$result) {
            $this->getConnection()->rollback();
            return false;
        }
        $this->getConnection()->commit();
        return true;
    }
}
