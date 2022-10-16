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
 * @package BaserCore\Model\Table
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
     * @param  Validator $validator
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
            ->add('id', 'valid', ['rule' => 'numeric', 'message' => __d('baser', 'IDに不正な値が利用されています。')]);
        return $validator;
    }

    /**
     * Before Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
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
        return parent::beforeSave($event, $entity, $options);
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
        $record = $this->get($id, ['contain' => ['Contents']]);
        if ($record->content->url) {
            $this->beforeStatus = $record->content->status;
        }
    }
}
