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

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Model\Entity\Content;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcBlog\Model\Table\BlogContentsTable;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * BlogContentsService
 * @property BlogContentsTable $BlogContents
 */
class BlogContentsService implements BlogContentsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * BlogContents
     * @var BlogContentsTable|Table
     */
    public BlogContentsTable|Table $BlogContents;

    /**
     * Construct
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogContents = TableRegistry::getTableLocator()->get("BcBlog.BlogContents");
    }

    /**
     * 一覧データを取得
     *
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query
    {
        $queryParams = array_merge([
            'status' => ''
        ], $queryParams);

        $query = $this->BlogContents->find()->orderBy([
            'BlogContents.id'
        ]);

        if (!empty($queryParams['limit'])) {
            $query->limit($queryParams['limit']);
        }

        $query = $this->createIndexConditions($query, $queryParams);
        return $query;
    }

    /**
     * createIndexConditions
     * @param Query $query
     * @param array $params
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createIndexConditions(Query $query, array $params): Query
    {
        $params = array_merge([
            'name' => null,
            'title' => null,
            'description' => null,
            'status' => '',
        ], $params);

        if (!is_null($params['description'])) {
            $query->where(['description LIKE' => '%' . $params['description'] . '%']);
        }

        if ($params['status'] === 'publish') {
            $fields = $this->BlogContents->getSchema()->columns();
            $query = $query->contain(['Contents'])->select($fields);
            $query->where($this->BlogContents->Contents->getConditionAllowPublish());
        }

        if( !is_null($params['name'])) {
            $query->matching('Contents', function($q) use ($params) {
                return $q->where(['Contents.name' => $params['name']]);
            });
        }

        if( !is_null($params['title'])) {
            $query->matching('Contents', function($q) use ($params) {
                return $q->where(['Contents.title LIKE' => '%' . $params['title'] . '%']);
            });
        }
        return $query;
    }

    /**
     * 単一データ取得
     *
     * @param int $id
     * @param array $options
     *  - `status`: ステータス。 publish を指定すると公開状態のもののみ取得（初期値：全て）
     *  - `contentId`: コンテンツID。指定した場合、エイリアスも指定可能。
     * @return \Cake\Datasource\EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, array $options = []): EntityInterface|null
    {
        $options = array_merge([
            'status' => '',
            'contentId' => null,
            'contain' => ['Contents' => ['Sites']]
        ], $options);

        $conditions = ['BlogContents.id' => $id];
        if ($options['status'] === 'publish') {
            $conditions = array_merge($conditions, $this->BlogContents->Contents->getConditionAllowPublish());
        }
        if ($options['contentId']) {
            $this->BlogContents->onAlias();
            $conditions = array_merge($conditions, ['Contents.id' => $options['contentId']]);
        }

        $result = $this->BlogContents->get($id,
            conditions: $conditions,
            contain: $options['contain']
        );

        if ($options['contentId']) {
            $this->BlogContents->offAlias();
        }

        return $result;
    }

    /**
     * 初期値を取得する
     *
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew()
    {
        return $this->BlogContents->newEntity([
            'comment_use' => true,
            'comment_approve' => false,
            'layout' => 'default',
            'template' => 'default',
            'list_count' => 10,
            'list_direction' => 'DESC',
            'feed_count' => 10,
            'tag_use' => false,
            'status' => false,
            'eye_catch_size_thumb_width' => Configure::read('BcBlog.eye_catch_size_thumb_width'),
            'eye_catch_size_thumb_height' => Configure::read('BcBlog.eye_catch_size_thumb_height'),
            'eye_catch_size_mobile_thumb_width' => Configure::read('BcBlog.eye_catch_size_mobile_thumb_width'),
            'eye_catch_size_mobile_thumb_height' => Configure::read('BcBlog.eye_catch_size_mobile_thumb_height'),
            'use_content' => true
        ], [
            'validate' => false,
        ]);
    }

    /**
     * 更新
     *
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        $blogContent = $this->BlogContents->patchEntity($target, $postData);
        /* @var \BcBlog\Model\Entity\BlogContent $blogContent */
        $blogContent = $this->BlogContents->deconstructEyeCatchSize($blogContent);
        return $this->BlogContents->saveOrFail($blogContent);
    }

    /**
     * ブログ登録
     *
     * @param array $data
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData, $options = []): ?EntityInterface
    {
        $blogContent = $this->getNew();
        $blogContent = $this->BlogContents->patchEntity($blogContent, $postData, $options);
        /* @var \BcBlog\Model\Entity\BlogContent $blogContent */
        $blogContent = $this->BlogContents->deconstructEyeCatchSize($blogContent);
        return $this->BlogContents->saveOrFail($blogContent);
    }

    /**
     * ブログをコピーする
     *
     * @param array $postData
     * @return EntityInterface $result
     * @checked
     * @unitTest
     * @noTodo
     */
    public function copy($postData)
    {
        return $this->BlogContents->copy(
            $postData['entity_id'] ?? null,
            $postData['parent_id'] ?? null,
            $postData['title'] ?? null,
            BcUtil::loginUser()->id,
            $postData['site_id'] ?? null
        );
    }

    /**
     * ブログを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        $blogContent = $this->get($id, ['contain' => []]);
        return $this->BlogContents->delete($blogContent);
    }

    /**
     * リストを取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return $this->BlogContents
            ->find('list', keyField:'id', valueField: 'content.title')
            ->contain('Contents')->toArray();
    }

    /**
     * コントロールソースを取得する
     *
     * @param null $field
     * @param array $options
     * @return array|false コントロールソース
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource($field = null, $options = [])
    {
        switch($field) {
            case 'id':
                $controlSources['id'] = $this->BlogContents->find('list',
                    keyField: 'id',
                    valueField: 'content.title'
                )
                    ->contain(['Contents'])
                    ->where([
                        'plugin' => 'BcBlog',
                        'type' => 'BlogContent',
                    ])->toArray();
                break;
            default:
                break;
        }
        if (isset($controlSources[$field])) {
            return $controlSources[$field];
        } else {
            return false;
        }
    }

    /**
     * コンテンツテンプレートの相対パスを取得する
     *
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getContentsTemplateRelativePath(array $options): string
    {
        $options = array_merge([
            'template' => 'posts',
            'contentsTemplate' => '',
            'contentUrl' => ''
        ], $options);

        if (!$options['contentsTemplate']) {
            $conditions = array_merge(
                ['Contents.url' => $options['contentUrl'][0]],
                $this->BlogContents->Contents->getConditionAllowPublish()
            );
            $blogContent = $this->BlogContents->find()->where($conditions)->contain(['Contents'])->first();
            if ($blogContent) {
                $options['contentsTemplate'] = $blogContent->template;
            } else {
                $options['contentsTemplate'] = 'default';
            }
        }

        return 'BcBlog...' . DS . 'Blog' . DS . $options['contentsTemplate'] . DS . $options['template'];
    }

    /**
     * URL よりブログコンテンツを取得する
     *
     * Contents を含む
     *
     * @param string $url
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findByUrl(string $url): ?EntityInterface
    {
        return $this->BlogContents->find()->where(['Contents.url' => $url])->contain('Contents')->first();
    }

    /**
     * コンテンツ ID よりブログコンテンツを取得する
     *
     * Contents を含む
     *
     * @param int $contentId
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findByContentId(int $contentId): ?EntityInterface
    {
        return $this->BlogContents->find()->where(['Contents.id' => $contentId])->contain('Contents')->first();
    }

    /**
     * 検索インデックスの再構築が必要か判定
     *
     * @param Content|EntityInterface $before
     * @param Content|EntityInterface $after
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function checkRequireSearchIndexReconstruction(EntityInterface $before, EntityInterface $after)
    {
        if (!Plugin::isLoaded('BcSearchIndex')) return false;
        if ($before->name !== $after->name) return true;
        if ($before->status !== $after->status) return true;
        if ($before->parent_id !== $after->parent_id) return true;
        return false;
    }

}
