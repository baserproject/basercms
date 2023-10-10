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

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Utility\BcUtil;
use BcBlog\Model\Entity\BlogContent;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Event\EventInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Validation\Validator;

/**
 * ブログコンテンツモデル
 *
 * @property ContentsTable $Contents
 * @property BlogPostsTable $BlogPosts
 */
class BlogContentsTable extends BlogAppTable
{
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
        $validator->setProvider('bc', 'BaserCore\Model\Validation\BcValidation');
        $validator->setProvider('blogContent', 'BcBlog\Model\Validation\BlogContentValidation');

        $validator->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator->scalar('list_count')
            ->notEmptyString('list_count', __d('baser_core', '一覧表示件数を入力してください。'))
            ->range('list_count', [0, 101], __d('baser_core', '一覧表示件数は100までの数値で入力してください。'))
            ->add('list_count', 'halfText', [
                'provider' => 'bc',
                'rule' => 'halfText',
                'message' => __d('baser_core', '一覧表示件数は半角で入力してください。')]);

        $validator->scalar('template')
            ->maxLength('template', 20, __d('baser_core', 'コンテンツテンプレート名は半角で入力してください。'))
            ->notEmptyString('template', __d('baser_core', 'コンテンツテンプレート名を入力してください。'))
            ->add('template', 'halfText', [
                'provider' => 'bc',
                'rule' => 'halfText',
                'message' => __d('baser_core', 'コンテンツテンプレート名は半角で入力してください。')]);

        $validator->scalar('list_direction')
            ->notEmptyString('list_direction', __d('baser_core', '一覧に表示する順番を指定してください。'));

        $validator->add('eye_catch_size_thumb_width', 'checkEyeCatchSize', [
            'provider' => 'blogContent',
            'rule' => 'checkEyeCatchSize',
            'message' => __d('baser_core', 'アイキャッチ画像のサイズが不正です。')]);

        return $validator;
    }

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

        $this->setTable('blog_contents');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BaserCore.BcContents');
        if (Plugin::isLoaded('BcSearchIndex')) {
            $this->addBehavior('BcSearchIndex.BcSearchIndexManager');
        }

        $this->hasMany('BlogPosts', [
            'className' => 'BcBlog.BlogPosts',
            'order' => 'posted DESC',
            'foreignKey' => 'blog_content_id',
            'dependent' => true,
            'exclusive' => false,
        ]);
        $this->hasMany('BlogCategories', [
            'className' => 'BcBlog.BlogCategories',
            'order' => 'id',
            'limit' => 10,
            'foreignKey' => 'blog_content_id',
            'dependent' => true,
            'exclusive' => false,
        ]);
    }

    /**
     * beforeSave
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param \ArrayObject $options
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, \ArrayObject $options)
    {
        if (!Plugin::isLoaded('BcSearchIndex')) {
            return true;
        }
        if (empty($entity->content) || !empty($entity->content->exclude_search) || !$entity->content->status) {
            $this->setExcluded();
        }
        return true;
    }

    /**
     * 検索用データを生成する
     *
     * @param EntityInterface $blogContent
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createSearchIndex($blogContent)
    {
        return [
            'type' => __d('baser_core', 'ブログ'),
            'model_id' => $blogContent->id,
            'content_id' => $blogContent->content->id,
            'site_id' => $blogContent->content->site_id,
            'title' => $blogContent->content->title,
            'detail' => $blogContent->description,
            'url' => $blogContent->content->url,
            'status' => $blogContent->content->status,
            'publish_begin' => $blogContent->content->publish_begin,
            'publish_end' => $blogContent->content->publish_end
        ];
    }

    /**
     * 関連するブログ記事の検索インデックスを作成する
     *
     * @param EntityInterface $entity
     * @return void
     * @checked
     * @noTodo
     */
    public function createRelatedSearchIndexes(EntityInterface $entity)
    {
        /** @var BlogContent $entity */
        $entities = $this->BlogPosts->find()
            ->where(['BlogPosts.blog_content_id' => $entity->id])
            ->contain(['BlogContents' => ['Contents']])
            ->all();
        if (!$entities->count()) return;
        foreach($entities as $entity) {
            // 保存するために強制的に dirty に設定
            $entity->setDirty('id');
            $this->BlogPosts->save($entity);
        }
    }

    /**
     * ブログコンテンツをコピーする
     *
     * @param int $id ページID
     * @param int $newParentId 新しい親コンテンツID
     * @param string $newTitle 新しいタイトル
     * @param int $newAuthorId 新しいユーザーID
     * @param int $newSiteId 新しいサイトID
     * @return mixed EntityInterface|false
     * @checked
     */
    public function copy(
        int $id,
        int $newParentId,
        string $newTitle,
        int $newAuthorId,
        int $newSiteId = null
    )
    {
        $data = $this->find()->where(['BlogContents.id' => $id])->contain('Contents')->first();
        $oldData = clone $data;

        // EVENT BlogContents.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $data,
            'id' => $id,
        ]);
        if ($event !== false) {
            $data = $event->getResult() === true || is_null($event->getResult())? $event->getData('data') : $event->getResult();
        }

        $url = $data->content->url;
        $siteId = $data->content->site_id;
        $name = $data->content->name;
        $eyeCatch = $data->content->eyecatch;
        unset($data->id);
        unset($data->created);
        unset($data->modified);
        $data->content = new Content([
            'name' => $name,
            'parent_id' => $newParentId,
            'title' => $newTitle ?? $oldData->title . '_copy',
            'author_id' => $newAuthorId,
            'site_id' => $newSiteId,
            'exclude_search' => false,
            'description' => $data->content->description,
            'eyecatch' => $data->content->eyecatch
        ]);
        $newBlogContent = $this->patchEntity($this->newEmptyEntity(), $data->toArray());
        if (!is_null($newSiteId) && $siteId != $newSiteId) {
            $data->content = new Content([
                'site_id' => $newSiteId,
                'parent_id' => $this->Contents->copyContentFolderPath($url, $newSiteId)
            ]);
        }
        $this->getConnection()->begin();

        try {
            $result = $this->save($newBlogContent);
            if (!$result) {
                $this->getConnection()->rollback();
                return false;
            }
            $newBlogContent = clone $result;
            $blogPosts = $this->BlogPosts->find()
                ->where(['BlogPosts.blog_content_id' => $id])
                ->order(['BlogPosts.id'])
                ->all();
            if ($blogPosts) {
                foreach($blogPosts as $blogPost) {
                    $blogPost->blog_category_id = null;
                    $blogPost->blog_content_id = $newBlogContent->id;
                    if (!$this->BlogPosts->copy(null, $blogPost)) {
                        $this->getConnection()->rollback();
                        return false;
                    }
                }
            }

            // TODO ucmitz 未実装
            // >>>
//            if ($eyeCatch) {
//                $content = clone $data->content;
//                $content->eyecatch = $eyeCatch;
//                $content = $this->Contents->renameToBasenameFields(true);
//                $result = $this->Content->save($content);
//                if(!$result) {
//                    $this->getConnection()->rollback();
//                    return false;
//                }
//                $newBlogContent->content = $result;
//            }
            // <<<

            // EVENT BlogContents.afterCopy
            $this->dispatchLayerEvent('afterCopy', [
                'id' => $newBlogContent->id,
                'data' => $newBlogContent,
                'oldId' => $id,
                'oldData' => $oldData,
            ]);

            $this->getConnection()->commit();
            return $newBlogContent;
        } catch (PersistenceFailedException $e) {
            $this->getConnection()->rollback();
            return false;
        }
    }

    /**
     * アイキャッチサイズフィールドの値をDB用に変換する
     *
     * @param BlogContent $data
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deconstructEyeCatchSize($data)
    {
        $data->eye_catch_size = BcUtil::serialize([
            'thumb_width' => $data->eye_catch_size_thumb_width,
            'thumb_height' => $data->eye_catch_size_thumb_height,
            'mobile_thumb_width' => $data->eye_catch_size_mobile_thumb_width,
            'mobile_thumb_height' => $data->eye_catch_size_mobile_thumb_height,
        ]);
        return $data;
    }

    /**
     * アイキャッチサイズフィールドの値をフォーム用に変換する
     *
     * @param EntityInterface $data
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function constructEyeCatchSize($data)
    {
        $eyeCatchSize = BcUtil::unserialize($data->eye_catch_size);
        // プロパティを変更するとエラー情報が消えてしまうので一旦退避
        $errors = $data->getErrors();
        $data->eye_catch_size_thumb_width = $eyeCatchSize['thumb_width'];
        $data->eye_catch_size_thumb_height = $eyeCatchSize['thumb_height'];
        $data->eye_catch_size_mobile_thumb_width = $eyeCatchSize['mobile_thumb_width'];
        $data->eye_catch_size_mobile_thumb_height = $eyeCatchSize['mobile_thumb_height'];
        $data->setErrors($errors);
        return $data;
    }

}
