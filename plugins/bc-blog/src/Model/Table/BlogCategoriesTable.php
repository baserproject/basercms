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

use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BlogCategoriesTable
 */
class BlogCategoriesTable extends BlogAppTable
{

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
            ->maxLength('name', 255, __d('baser', 'カテゴリ名は255文字以内で入力してください。'))
            ->requirePresence('name', 'create', __d('baser', 'カテゴリ名を入力してください。'))
            ->notEmptyString('name', __d('baser', 'カテゴリ名を入力してください。'))
            ->add('name', [
                'alphaNumericDashUnderscore' => [
                    'rule' => ['alphaNumericDashUnderscore'],
                    'provider' => 'bc',
                    'message' => __d('baser', 'カテゴリ名はは半角英数字とハイフン、アンダースコアのみが利用可能です。')]])
            ->add('name', [
                'duplicateBlogCategory' => [
                    'rule' => ['duplicateBlogCategory'],
                    'provider' => 'blogCategory',
                    'message' => __d('baser', '入力されたカテゴリ名は既に登録されています。')]]);

        $validator
            ->scalar('title')
            ->maxLength('title', 50, __d('baser', 'カテゴリタイトルは255文字以内で入力してください。'))
            ->requirePresence('title', 'create', __d('baser', 'カテゴリタイトルを入力してください。'))
            ->notEmptyString('title', __d('baser', 'カテゴリタイトルを入力してください。'));

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
            'fields' => ['BlogCategory.id', 'BlogCategory.name', 'BlogCategory.title', 'BlogCategory.lft', 'BlogCategory.rght'],
        ], $options);
        $fields = $options['fields'];
        $depth = $options['depth'];
        $parentId = $options['parentId'];
        unset($options['fields']);
        unset($options['depth']);
        unset($options['parentId']);
        $datas = [];
        if (!$options['type']) {
            $datas = $this->_getCategoryList($blogContentId, $parentId, $options['viewCount'], $depth, 1, $fields, $options);
        } elseif ($options['type'] == 'year') {
            $options = [
                'category' => true,
                'limit' => $options['limit'],
                'viewCount' => $options['viewCount'],
                'type' => 'year'
            ];
            $_datas = $this->BlogPost->getPostedDates($blogContentId, $options);
            $datas = [];
            foreach ($_datas as $data) {
                if ($options['viewCount']) {
                    $data['BlogCategory']['count'] = $data['count'];
                }
                $datas[$data['year']][] = ['BlogCategory' => $data['BlogCategory']];
            }
        }
        return $datas;
    }

    /**
     * カテゴリリストを取得する（再帰処理）
     *
     * @param int $blogContentId
     * @param int $parentId
     * @param int $viewCount
     * @param int $depth
     * @param int $current
     * @param array $fields
     * @param array $options
     * @return array
     */
    protected function _getCategoryList($blogContentId = null, $parentId = null, $viewCount = false, $depth = 1, $current = 1, $fields = [], $options = [])
    {
        $options = array_merge([
            'id' => null,
            'siteId' => null,
            'order' => 'BlogCategory.lft asc',
            'conditions' => [],
            'threaded' => false
        ], $options);
        $conditions = $options['conditions'];
        if (!empty($options['id'])) {
            $parentId = false;
        }
        // 親を指定する場合
        if ($parentId !== false) {
            $conditions['BlogCategory.parent_id'] = $parentId;
        }
        if (!empty($options['id'])) {
            $conditions['BlogCategory.id'] = $options['id'];
        }
        if ($options['siteId'] !== false && !is_null($options['siteId'])) {
            $conditions['Content.site_id'] = $options['siteId'];
        }
        if (!is_null($blogContentId)) {
            $conditions['BlogCategory.blog_content_id'] = $blogContentId;
        }
        $findType = 'all';
        if ($options['threaded']) {
            $findType = 'threaded';
            $options['order'] = 'BlogCategory.lft';
            unset($conditions['BlogCategory.parent_id']);
            $fields = [];
        } else {
            if ($fields) {
                if (is_array($fields)) {
                    $fields[0] = 'DISTINCT ' . $fields[0];
                } else {
                    $fields = 'DISTINCT ' . $fields;
                }
            }
        }
        $findOptions = [
            'conditions' => $conditions,
            'fields' => $fields,
            'order' => $options['order'],
            'recursive' => 0,
            'joins' => [
                [
                    'type' => 'LEFT',
                    'table' => 'blog_contents',
                    'alias' => 'BlogContent',
                    'conditions' => "BlogCategory.blog_content_id=BlogContent.id",
                ],
                [
                    'type' => 'LEFT',
                    'table' => 'contents',
                    'alias' => 'Content',
                    'conditions' => "Content.entity_id=BlogContent.id AND Content.type='BlogContent'",
                ]
            ]
        ];
        $datas = $this->find($findType, $findOptions);
        if ($datas && $findType == 'all') {
            foreach ($datas as $key => $data) {
                if ($viewCount) {
                    $childrenIds = $this->find('list', [
                        'fields' => ['id'],
                        'conditions' => [
                            ['BlogCategory.lft > ' => $data['BlogCategory']['lft']],
                            ['BlogCategory.rght < ' => $data['BlogCategory']['rght']],
                        ],
                        'recursive' => -1
                    ]);
                    $categoryId = [$data['BlogCategory']['id']];
                    if ($childrenIds) {
                        $categoryId = array_merge($categoryId, $childrenIds);
                    }
                    $datas[$key]['BlogCategory']['count'] = $this->BlogPost->find('count', [
                        'conditions' =>
                        array_merge(
                            ['BlogPost.blog_category_id' => $categoryId],
                            $this->BlogPost->getConditionAllowPublish()
                        ),
                        'cache' => false
                    ]);
                }
                if ($current < $depth) {
                    $children = $this->_getCategoryList($blogContentId, $data['BlogCategory']['id'], $viewCount, $depth, $current + 1, $fields, $options);
                    if ($children) {
                        $datas[$key]['BlogCategory']['children'] = $children;
                    }
                }
            }
        }
        return $datas;
    }

    /**
     * アクセス制限としてカテゴリの新規追加ができるか確認する
     *
     * Ajaxを利用する箇所にて BcBaserHelper::link() が利用できない場合に利用
     *
     * @param int $userGroupId ユーザーグループID
     * @param int $blogContentId ブログコンテンツID
     */
    public function hasNewCategoryAddablePermission($userGroupId, $blogContentId)
    {
        if (ClassRegistry::isKeySet('Permission')) {
            $Permission = ClassRegistry::getObject('Permission');
        } else {
            $Permission = ClassRegistry::init('Permission');
        }
        $ajaxAddUrl = preg_replace('|^/index.php|', '', Router::url(['plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'ajax_add', $blogContentId]));
        return $Permission->check($ajaxAddUrl, $userGroupId);
    }

    /**
     * 子カテゴリを持っているかどうか
     *
     * @param int $id
     * @return bool
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
}
