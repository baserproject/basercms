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
use Cake\ORM\Behavior;
use Cake\Utility\Inflector;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

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
     * 新規のデータの場合時のみContent のバリデーションを実行し、エラーがある場合は中止する
     * $data['content']がある場合のみ実行する
     * @param EventInterface $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        if (!empty($data['content'])) {
            if (!empty($data['content']['id'])) {
                if (!$this->Contents->findById($data['content']['id'])->first()->isNew()) return;
            }
            $validateOptions = ['validate' => $options['validate'] ?? 'default'];
            $contentEntity = $this->Contents->newEntity($data['content'], $validateOptions);
            if ($contentEntity->hasErrors() && empty($data['content']['id'])) {
                return false;
            }
            $this->Contents->beforeMarshal($event, $data, $options);
        }
    }

    /**
     * Before delete
     *
     * afterDeleteでのContents物理削除準備をする
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (empty($entity->content)) {
            $entity->content = $this->Contents->find('all', ['withDeleted'])->where(['entity_id' => $entity->id])->first();
        }
    }

    /**
     * After delete
     *
     * 削除したデータに連携する Content を削除
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->content) {
            $this->Contents->hardDel($entity->content);
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
