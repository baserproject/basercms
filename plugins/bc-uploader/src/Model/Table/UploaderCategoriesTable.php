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

 namespace BcUploader\Model\Table;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\Table\AppTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * ファイルカテゴリモデル
 *
 */
class UploaderCategoriesTable extends AppTable
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('uploader_categories');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->hasMany('UploaderFiles', [
            'className' => 'BcUploader.UploaderFiles',
            'order' => 'created DESC',
            'foreignKey' => 'uploader_category_id',
            'dependent' => true,
            'exclusive' => false,
        ]);
    }

    /**
     * MailField constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     * @checked
     * @noTodo
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('name')
            ->notEmptyString('name', __d('baser_core', 'カテゴリ名を入力してください。'));
        return $validator;
    }

    /**
     * コピーする
     *
     * @param int $id
     * @param EntityInterface $entity
     * @return EntityInterface|false
     * @checked
     * @noTodo
     */
    public function copy($id = null, $entity = [])
    {
        if ($id) $entity = $this->find()->where(['UploaderCategories.id' => $id])->first();
        $oldEntity = clone $entity;

        // EVENT UploaderCategories.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $entity,
            'id' => $id,
        ]);
        if ($event !== false) {
            $entity = $event->getResult() === true ? $event->getData('data') : $event->getResult();
        }

        $entity->name .= '_copy';
        unset($entity->id);
        unset($entity->created);
        unset($entity->modified);

        try {
            $entity = $this->saveOrFail($this->patchEntity($this->newEmptyEntity(), $entity->toArray()));

            // EVENT UploaderCategories.afterCopy
            $this->dispatchLayerEvent('afterCopy', [
                'id' => $entity->id,
                'data' => $entity,
                'oldId' => $id,
                'oldData' => $oldEntity,
            ]);

            return $entity;
        } catch (PersistenceFailedException $e) {
            $entity = $e->getEntity();
            if($entity->getError('name')) {
                return $this->copy(null, $entity);
            }
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
