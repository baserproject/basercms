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

namespace BaserCore\Model\Table;

use ArrayObject;
use BaserCore\Model\Entity\Content;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use Cake\Datasource\EntityInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Note;

/**
 * Class PagesTable
 */
class PagesTable extends AppTable
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * 検索テーブルへの保存可否
     *
     * @var boolean
     */
    public $searchIndexSaving = true;

    /**
     * 公開WebページURLリスト
     * キャッシュ用
     *
     * @var mixed
     */
    protected $_publishes = -1;

    /**
     * WebページURLリスト
     * キャッシュ用
     *
     * @var mixed
     */
    protected $_pages = -1;

    /**
     * 最終登録ID
     * モバイルページへのコピー処理でスーパークラスの最終登録IDが上書きされ、
     * コントローラーからは正常なIDが取得できないのでモバイルページへのコピー以外の場合だけ保存する
     *
     * @var int
     */
    private $__pageInsertID = null;

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcContents');
        if(Plugin::isLoaded('BcSearchIndex')) {
            $this->addBehavior('BcSearchIndex.BcSearchIndexManager');
        }
        $this->addBehavior('Timestamp');
        $this->Sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $this->Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
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
        $validator->setProvider('page', 'BaserCore\Model\Validation\PageValidation');

        $validator
        ->integer('id')
        ->numeric('id', __d('baser', 'IDに不正な値が利用されています。'), 'update')
        ->requirePresence('id', 'update');

        $validator
        ->scalar('contents')
        ->allowEmptyString('contents', null)
        ->maxLengthBytes('contents', 64000, __d('baser', '本稿欄に保存できるデータ量を超えています。'))
        ->add('contents', 'custom', [
            'rule' => ['phpValidSyntax'],
            'provider' => 'page',
            'message' => __d('baser', '本稿欄でPHPの構文エラーが発生しました。')
        ])
        ->add('contents', [
            'containsScript' => [
                'rule' => ['containsScript'],
                'provider' => 'bc',
                'message' => __d('baser', '本稿欄でスクリプトの入力は許可されていません。')
            ]
        ]);

        $validator
        ->scalar('draft')
        ->allowEmptyString('draft', null)
        ->maxLengthBytes('draft', 64000, __d('baser', '本稿欄に保存できるデータ量を超えています。'))
        ->add('draft', 'custom', [
            'rule' => ['phpValidSyntax'],
            'provider' => 'page',
            'message' => __d('baser', '本稿欄でPHPの構文エラーが発生しました。')
        ])
        ->add('draft', [
            'containsScript' => [
                'rule' => ['containsScript'],
                'provider' => 'bc',
                'message' => __d('baser', '本稿欄でスクリプトの入力は許可されていません。')
            ]
        ]);

        return $validator;
    }

    /**
     * Before Save
     *
     * @param  EventInterface $event
     * @param  EntityInterface $entity
     * @param  ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!Plugin::isLoaded('BcSearchIndex') || !$this->searchIndexSaving ) {
            return;
        }
        // 検索用テーブルに登録
        if (empty($entity->content) || !empty($entity->content->exclude_search)) {
            $this->setExcluded();
        }
    }

    /**
     * 検索用データを生成する
     *
     * @param EntityInterface $entity
     * @return array|false
     * @checked
     * @unitTest
     * @noTodo
     */
    public function createSearchIndex($page)
    {
        if (!isset($page->id) || !isset($page->content->id)) {
            return false;
        }
        $content = $page->content;
        if (!isset($content->publish_begin)) {
            $content->publish_begin = '';
        }
        if (!isset($content->publish_end)) {
            $content->publish_end = '';
        }

        if (!$content->title) {
            $content->title = Inflector::camelize($content->name);
        }
        $modelId = $page->id;

        $host = '';
        $url = $content->url;
        if (!$content->site) {
            $site = $this->Sites->get($content->site_id);
        } else {
            $site = $content->site;
        }
        if ($site->useSubDomain) {
            $host = $site->alias;
            if ($site->domainType == 1) {
                $host .= '.' . BcUtil::getMainDomain();
            }
            $url = preg_replace('/^\/' . preg_quote($site->alias, '/') . '/', '', $url);
        }
        $detail = $page->contents;
        $description = '';
        if (!empty($content->description)) {
            $description = $content->description;
        }
        return [
            'model_id' => $modelId,
            'type' => __d('baser', 'ページ'),
            'content_id' => $content->id,
            'site_id' => $content->site_id,
            'title' => $content->title,
            'detail' => $description . ' ' . $detail,
            'url' => $url,
            'status' => $content->status,
            'publish_begin' => $content->publish_begin,
            'publish_end' => $content->publish_end
        ];
    }

    /**
     * ページデータをコピーする
     *
     * 固定ページテンプレートの生成処理を実行する必要がある為、
     * Content::copy() は利用しない
     *
     * @param array $postData
     * @return EntityInterface|false $result
     * @checked
     * @unitTest
     * @noTodo
     */
    public function copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId = null)
    {
        $page = $this->get($id, ['contain' => ['Contents']]);
        $oldPage = clone $page;

        // EVENT Page.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $page,
            'id' => $page->id
        ]);
        if ($event !== false) {
            $page = $event->getResult() === true? $event->getData('data') : $event->getResult();
            unset($event);
        }

        unset($page->created, $page->modified);
        $page->content = new Content([
			'name' => $page->content->name,
			'parent_id' => $newParentId,
			'title' => $newTitle,
			'author_id' => $newAuthorId,
			'site_id' => $newSiteId,
			'description' => $page->content->description,
			'eyecatch' => $page->content->eyecatch
        ]);

        if (!is_null($newSiteId) && $oldPage->content->site_id !== $newSiteId) {
            $page->content->parent_id = $this->Contents->copyContentFolderPath($oldPage->content->url, $newSiteId);
        }
        $newPage = $this->patchEntity($this->newEmptyEntity(), $page->toArray());
        $newPage = $this->saveOrFail($newPage);

        // EVENT Page.afterCopy
        $this->dispatchLayerEvent('afterCopy', [
            'data' => $newPage,
            'id' => $newPage->id,
            'oldData' => $oldPage,
            'oldId' => $id,
        ]);
        return $newPage;
    }
}
