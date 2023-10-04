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

namespace BcContentLink\Model\Table;

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Table\AppTable;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class ContentLinksTable
 *
 * リンク モデル
 */
class ContentLinksTable extends AppTable
{
    /**
     * initialize
     *
     * コンテンツテーブルと連携するための、BcContentsBehavior を追加する
     *
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcContents');
    }

    /**
     * Validation Default
     *
     * バリデーションの設定を行う。
     *
     * - url
     *  - 入力必須
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
        ->numeric('id', __d('baser_core', 'IDに不正な値が利用されています。'), 'update')
        ->requirePresence('id', 'update');

        $validator
        ->scalar('url')
        ->notEmptyString('url', __d('baser_core', 'リンク先URLを入力してください。'), 'update');

        return $validator;
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
     * @throws \Throwable
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(int $id, $newParentId, $newTitle, $newAuthorId, $newSiteId)
    {
        $entity = $this->get($id, ['contain' => ['Contents']]);
        $oldEntity = clone $entity;

        // EVENT ContentLinks.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $entity,
            'id' => $id,
        ]);
        if ($event !== false) {
            $entity = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
        }

        $entity->content = new Content([
            'name' => $entity->content->name,
            'parent_id' => $newParentId,
            'title' => $newTitle ?? $oldEntity->title . '_copy',
            'author_id' => $newAuthorId,
            'site_id' => $newSiteId,
            'description' => $entity->content->description,
            'eyecatch' => $entity->content->eyecatch,
            'layout_template' => $entity->content->layout_tmplate ?? '',
            'url' => $entity->content->url
        ]);
        if (!is_null($newSiteId) && $oldEntity->content->site_id !== $newSiteId) {
            $entity->content->parent_id = $this->Contents->copyContentFolderPath($entity->content->url, $newSiteId);
        }
        unset($entity->id);
        unset($entity->created);
        unset($entity->modified);

        $entity = $this->saveOrFail($this->patchEntity($this->newEmptyEntity(), $entity->toArray()));

        // EVENT ContentLinks.afterCopy
        $this->dispatchLayerEvent('afterCopy', [
            'id' => $entity->id,
            'data' => $entity,
            'oldId' => $id,
            'oldData' => $oldEntity,
        ]);

        return $entity;
    }
}
