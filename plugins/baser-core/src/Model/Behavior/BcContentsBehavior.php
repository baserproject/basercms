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

namespace BaserCore\Model\Behavior;

use ArrayObject;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
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
 */
class BcContentsBehavior extends Behavior
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Contents
     *
     * @var ContentsTable $Contents
     */
    public $Contents;

    /**
     * initialize
     * @param array $config
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        $this->table = $this->table();
        $prefix = $this->table->getConnection()->config()['prefix'];
        $type = preg_replace('/^' . $prefix . '/', '', $this->table->getTable());
        if (!$this->table->__isset('Contents')) {
            $this->table->hasOne('Contents', ['className' => 'BaserCore.Contents'])
                ->setForeignKey('entity_id')
                ->setDependent(false)
                ->setJoinType('INNER')
                ->setConditions([
                    'Contents.type' => Inflector::classify($type),
                    'Contents.alias_id IS' => null,
                ]);
        }
        $this->Contents = $this->table->getAssociation('Contents');
    }

    /**
     * afterMarshal
     * contentの項目がない場合エラーをセットする
     * @param EventInterface $event
     * @param EventInterface $entity
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterMarshal(EventInterface $event, EntityInterface $entity, ArrayObject $data, ArrayObject $options)
    {
        if ($options['validate']) {
            if (!isset($data['content'])) {
                $entity->setError('content', ['_required' => __d('baser_core', '関連するコンテンツがありません')]);
            } else {
                [$plugin, $type] = pluginSplit($this->table->getRegistryAlias());
                $entity->content->plugin = $entity->content->plugin ?? $plugin;
                $entity->content->type = $entity->content->type ?? Inflector::classify($type);
            }
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
            /* @var ContentsService $contentsService */
            $contentsService = $this->getService(ContentsServiceInterface::class);
            $contentsService->hardDelete($entity->content->id);
        }
    }

}
