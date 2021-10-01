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
     * Content のバリデーションを実行し、エラーがある場合は中止する
     * @param Event $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void|false
     * @checked
     * @unitTest
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        // TODO: validate falseできない
        $validateOptions = ['validate' => $options['validate'] ?? 'default'];
        $content = $this->Contents->findById($data['Content']['id']);
        // $contentFolder = $this->table->findById($data['ContentFolder']['id']);
        if (empty($data['Content']['id']) || $content->isEmpty()) {
            // 新規作成処理
            $newContent = $this->Contents->newEntity($data['content'], $validateOptions);
        } else {
            // 編集処理
            $newContent = $this->Contents->patchEntity($content->first(), $data['Content'], $validateOptions);
            // $newContentFolder = $this->table->patchEntity($contentFolder->first(), $data['ContentFolder'], $validateOptions);
            $data['content'] = $newContent;
            unset($data['Content']);
            // unset($data['ContentFolder']);
            // $event->setData('content', $newContent);
        }
        $this->Contents->beforeMarshal($event, $data, $options);
    }

        /**
     * BeforeMarshal→afterMarshal
     *
     * Content のバリデーションを実行し、エラーがある場合は中止する
     * @param Event $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void|false
     */
    public function afterMarshal(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        $a = $event;
    }

    /**
     * beforeSave
     *
     * @param  EventInterface $event
     * @param  EntityInterface $entity
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity)
    {
        $a = 0;
        if ($event->getData('content')->hasErrors() && empty($event->getData('content.id'))) {
            return false;
        }
        // $content = $this->ContentFolders->Contents->patchEntity($target->content, $postData['Content']);
        // $entities = [
        //     'Content' => $content,
        //     'ContentFolder' => $contentFolder
        // ];
        // try {
        //     if ($this->ContentFolders->save($contentFolder) && $this->ContentFolders->Contents->save($content)) {
        //         return $entities;
        //     }
        // } catch (\Exception $e) {
        //     return false;
        // }
        // return ($result = $this->ContentFolders->save($target))? $result : $contentFolder;
    }

    /**
     * After save
     *
     * Content を保存する
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     * @checked
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // TODO: 一旦コメントアウト
        return;
        if (empty($entity->content)) return false;

        // if (!empty($options['validate'])) {
        //     // beforeValidate で調整したデータを利用する為、$model->Content->data['Content'] を利用
        //     $data = $this->table->Content->data['Content'];
        // } else {
        //     $data = $this->table->data['Content'];
        // }

        unset($entity->content->lft);
        unset($entity->content->rght);
        if ($entity->isNew()) {
            list($plugin, $name) = explode('.', $this->table->getRegistryAlias());
            $data = $this->Contents->createContent($entity->content->toArray(), $plugin ?? "BaserCore", Inflector::classify($name), $entity->id, false);
        } else {
            $content = $this->Contents->patchEntity($this->Contents->get($entity->content->id), $entity->content->toArray());
            $data = $this->Contents->save($content, false);
        }
    }

    /**
     * Before delete
     *
     * afterDelete でのContents物理削除準備をする
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
