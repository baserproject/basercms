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
use BcSearchIndex\Service\SearchIndexesService;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use Cake\Datasource\EntityInterface;

/**
 * Class ContentFoldersTable
 */
class ContentFoldersTable extends AppTable
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 変更前ステータス
     *
     * @var bool|null
     */
    public $beforeStatus = null;

    /**
     * テンプレートを移動可能かどうか
     *
     * @var bool
     */
    public $isMovableTemplate = true;

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
        $this->addBehavior('BaserCore.BcContents');
        $this->addBehavior('Timestamp');
    }

    /**
     * validationDefault
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
            ->allowEmptyString('id', null, 'create')
            ->add('id', 'valid', ['rule' => 'numeric', 'message' => __d('baser_core', 'IDに不正な値が利用されています。')]);
        return $validator;
    }

    /**
     * Before Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // 変更前のURLを取得
        if (!empty($entity->id) && ($this->isMovableTemplate || !empty($options['reconstructSearchIndices']))) {
            $this->isMovableTemplate = false;
            $this->setBeforeRecord($entity->id);
        }
    }

    /**
     * After Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @param bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!empty($entity->content->url)) {
            $this->isMovableTemplate = true;
        }
        if (!empty($options['reconstructSearchIndices']) && $this->beforeStatus !== $entity->content->status) {
            /* @var SearchIndexesService $searchIndexService */
            $searchIndexService = $this->getService(SearchIndexesServiceInterface::class);
            $searchIndexService->reconstruct($entity->content->id);
        }
        return true;
    }

    /**
     * 保存前のURLをセットする
     *
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    private function setBeforeRecord($id)
    {
        $record = $this->get($id, contain: ['Contents']);
        if ($record->content->url) {
            $this->beforeStatus = $record->content->status;
        }
    }

    /**
     * コピーする
     *
     * @param int $id
     * @param $newParentId
     * @param $newTitle
     * @param $newAuthorId
     * @param $newSiteId
     * @return mixed page Or false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(int $id, $newParentId, $newTitle, $newAuthorId, $newSiteId)
    {
        $entity = $this->get($id, contain: ['Contents']);
        $oldEntity = clone $entity;

        // EVENT ContentFolders.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $entity,
            'id' => $id,
        ]);
        if ($event !== false) {
            $entity = ($event->getResult() === null || $event->getResult() === true)? $event->getData('data') : $event->getResult();
        }

        $entity->content = new Content([
            'name' => $entity->content->name,
            'parent_id' => $newParentId,
            'title' => $newTitle ?? $oldEntity->title . '_copy',
            'author_id' => $newAuthorId,
            'site_id' => $newSiteId,
            'description' => $entity->content->description,
            'eyecatch' => $entity->content->eyecatch,
            'layout_template' => $entity->content->layout_tmplate ?? ''
        ]);
        if (!is_null($newSiteId) && $oldEntity->content->site_id !== $newSiteId) {
            $entity->content->parent_id = $this->Contents->copyContentFolderPath($entity->content->url, $newSiteId);
        }
        unset($entity->id);
        unset($entity->created);
        unset($entity->modified);

        $entity = $this->saveOrFail($this->patchEntity($this->newEmptyEntity(), $entity->toArray()));

        // EVENT ContentFolders.afterCopy
        $this->dispatchLayerEvent('afterCopy', [
            'id' => $entity->id,
            'data' => $entity,
            'oldId' => $id,
            'oldData' => $oldEntity,
        ]);

        return $entity;

    }
}
