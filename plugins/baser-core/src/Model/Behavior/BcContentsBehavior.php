<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\Utility\Inflector;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\ContentsTable;

/**
 * Class BcContentsBehavior
 * @package BaserCore\Model\Behavior
 */
class BcContentsBehavior extends Behavior
{
    /**
     * Contents
     *
     * @var ContentsTable $Contents
     */
    public $Contents;

    /**
     * initialize
     * @param  array $config
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        $this->table = $this->table();
        if (!$this->table-> __isset('Contents')) {
            $this->table->hasOne('Contents', ['className' => 'BaserCore.Contents'])
            ->setForeignKey('entity_id')
            ->setDependent(false)
            ->setConditions([
                'Contents.type' => Inflector::classify($this->table->getTable()),
                'Contents.alias_id IS' => null,
            ]);
        }
        $this->Contents = $this->table->getAssociation('Contents');
    }

    /**
     * BeforeMarshal
     *
     * Content のバリデーションを実行
     * 本体のバリデーションも同時に実行する為、Contentのバリデーション判定は、 beforeSave にて確認
     * @param Event $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     */
public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        if (!empty($data['content'])) {
            $validateOptions = ['validate' => $options['validate'] ?? 'default'];
            $contentEntity = $this->Contents->newEntity($data['content'], $validateOptions);
            if ($contentEntity->hasErrors() && empty($data['content']['id'])) {
                return $contentEntity->getErrors();
                // $this->table->newEntity($data, $validateOptions)->setErrors($contentEntity->getErrors());
                // $this->table->setErrors($contentEntity->getErrors());
            }
            if (!empty($contentEntity)) {
                $data['content'] = $contentEntity->toArray();
            }
        }
    }

    /**
     * Before save
     *
     * Content のバリデーション結果確認
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @return bool
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity)
    {
        return !$entity->content->hasErrors();
    }

    /**
     * After save
     *
     * Content を保存する
     *
     * @param Model $model
     * @param bool $created
     * @param array $options
     * @return bool
     * FIXME:
     */
    public function afterSave(EventInterface $event, EntityInterface $entity)
    {
        if (empty($entity->content)) return;

        // if (!empty($options['validate'])) {
        //     // beforeValidate で調整したデータを利用する為、$model->Content->data['Content'] を利用
        //     $data = $this->Content->data['Content'];
        // } else {
        //     $data = $model->data['Content'];
        // }
        unset($entity->content->lft);
        unset($entity->content->rght);
        if ($entity->isNew()) {
            list($plugin, $name) = explode('.', $this->table->getRegistryAlias());
            $data = $this->Contents->createContent($entity->toArray(), $plugin ?? "BaserCore", Inflector::classify($name), $entity->id, false);
        } else {
            // $this->Contents->patchEntity($entity->content, )
            // $data = $this->Contents->save($data, false);
        }
        if (!$entity->content) {
            $this->table->content = $entity->content;
        }
    }

    /**
     * Before delete
     *
     * 削除した Content ID を一旦保管し、afterDelete で Content より削除する
     *
     * @param Model $model
     * @param bool $cascade
     * @return bool
     */
    public function beforeDelete(Model $model, $cascade = true)
    {
        $data = $model->find('first', [
            'conditions' => [$model->alias . '.id' => $model->id]
        ]);
        if (!empty($data['Content']['id'])) {
            $this->_deleteContentId = $data['Content']['id'];
        }
        return true;
    }

    /**
     * After delete
     *
     * 削除したデータに連携する Content を削除
     *
     * @param Model $model
     */
    public function afterDelete(Model $model)
    {
        if ($this->_deleteContentId) {
            $softDelete = $model->Content->softDelete(null);
            $model->Content->softDelete(false);
            $model->Content->removeFromTree($this->_deleteContentId, true);
            $model->Content->softDelete($softDelete);
            $this->_deleteContentId = null;
        }
    }

    /**
     * 公開されたコンテンツを取得する
     *
     * @param Model $model
     * @param string $type
     * @param array $query
     * @return array|null
     */
    public function findPublished(Model $model, $type = 'first', $query = [])
    {
        $conditionAllowPublish = $model->Content->getConditionAllowPublish();
        if (!empty($query['conditions'])) {
            $query['conditions'] = array_merge(
                $conditionAllowPublish,
                $query['conditions']
            );
        } else {
            $query['conditions'] = $conditionAllowPublish;
        }
        return $model->find($type, $query);
    }

}
