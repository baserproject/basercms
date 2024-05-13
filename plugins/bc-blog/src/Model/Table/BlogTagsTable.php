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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Datasource\EntityInterface;
use Cake\Routing\Router;
use Cake\Validation\Validator;

/**
 * ブログタグモデル
 *
 */
class BlogTagsTable extends BlogAppTable
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('blog_tags');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('BlogPosts', [
            'className' => 'BcBlog.BlogPosts',
            'foreignKey' => 'blog_tag_id',
            'targetForeignKey' => 'blog_post_id',
            'through' => 'BcBlog.BlogPostsBlogTags',
            'joinTable' => 'blog_posts_blog_tags',
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
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->requirePresence('name', 'create', __d('baser_core', 'ブログタグを入力してください。'))
            ->notEmptyString('name', __d('baser_core', 'ブログタグを入力してください。'))
            ->add('name', [
                'duplicate' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser_core', '既に登録のあるタグです。')
                ]]);
        return $validator;
    }

    /**
     * アクセス制限としてブログタグの新規追加ができるか確認する
     *
     * @param array $userGroupId ユーザーグループID
     * @param int $blogContentId ブログコンテンツID
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hasNewTagAddablePermission($userGroupId, $blogContentId)
    {
        /* @var PermissionsService $permissionsService */
        $permissionsService = $this->getService(PermissionsServiceInterface::class);
        $addUrl = preg_replace('|^/index.php|', '', Router::url([
            'plugin' => 'BcBlog',
            'prefix' => 'Api/Admin',
            'controller' => 'BlogTags',
            'action' => 'add',
            $blogContentId
        ]));
        return $permissionsService->check($addUrl, $userGroupId);
    }

    /**
     * 指定した名称のブログタグ情報を取得する
     *
     * @param $name
     * @return EntityInterface
     */
    public function getByName($name)
    {
        return $this->find()->where(['BlogTags.name' => $name])->first();
    }

    /**
     * コピーする
     * @param $id
     * @return EntityInterface
     * @throws \Throwable
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy($id)
    {
        $entity = $this->find()->where(['BlogTags.id' => $id])->first();
        $oldEntity = clone $entity;

        // EVENT BlogTags.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $entity,
            'id' => $id,
        ]);
        if ($event !== false) {
            $entity = ($event->getResult() === null || $event->getResult() === true)? $event->getData('data') : $event->getResult();
        }

        $entity->name .= '_copy';
        unset($entity->id);
        unset($entity->created);
        unset($entity->modified);

        try {
            $entity = $this->saveOrFail($this->patchEntity($this->newEmptyEntity(), $entity->toArray()));

            // EVENT BlogTags.afterCopy
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
