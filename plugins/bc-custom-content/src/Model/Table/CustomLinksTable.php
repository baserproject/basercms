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

namespace BcCustomContent\Model\Table;

use ArrayObject;
use BaserCore\Model\Table\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * CustomLinksTable
 *
 * @property CustomFieldsTable $CustomFields
 */
class CustomLinksTable extends AppTable
{
    /**
     * Initialize
     *
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree', ['level' => 'level']);
        $this->belongsTo('CustomFields')
            ->setClassName('BcCustomContent.CustomFields')
            ->setForeignKey('custom_field_id');
        $this->belongsTo('CustomTables')
            ->setClassName('BcCustomContent.CustomTables')
            ->setForeignKey('custom_table_id');
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @noTodo
     * @checked
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255, __d('baser_core', '255文字以内で入力してください。'))
            ->notEmptyString('name', __d('baser_core', 'フィールド名を入力してください。'))
            ->regex('name', '/^[a-z0-9_]+$/', __d('baser_core', 'フィールド名は半角小文字英数字とアンダースコアのみで入力してください。'))
            ->add('name', [
                'validateUnique' => [
                    'rule' => ['validateUnique', ['scope' => 'custom_table_id']],
                    'provider' => 'table',
                    'message' => __d('baser_core', '既に登録のあるフィールド名です。')
            ]])
            ->add('name', [
                'reserved' => [
                    'rule' => ['reserved'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'システム予約名称のため利用できません。')
            ]]);
        $validator
            ->scalar('title')
            ->maxLength('title', 255, __d('baser_core', '255文字以内で入力してください。'))
            ->notEmptyString('title', __d('baser_core', 'タイトルを入力してください。'))
            ->add('title', [
                'notBlankOnlyString' => [
                    'rule' => ['notBlankOnlyString'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'タイトルを入力してください。')
                ]
            ]);

        return $validator;
    }

    /**
     * イベント定義
     *
     * scope の設定のため、TreeBehavior より優先度を高くする
     *
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function implementedEvents(): array
    {
        $events = parent::implementedEvents();
        $events['Model.beforeSave'] = [
            'callable' => 'beforeSave',
            'priority' => 0,
        ];
        $events['Model.beforeDelete'] = [
            'callable' => 'beforeDelete',
            'priority' => 0,
        ];
        return $events;
    }

    /**
     * ツリービヘイビアのスコープを設定する
     *
     * @param int $tableId
     * @noTodo
     * @checked
     * @unitTest
     */
    public function setTreeScope(int $tableId): void
    {
        $this->getBehavior('Tree')->setConfig('scope', ['custom_table_id' => $tableId]);
    }

    /**
     * Before Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool|void
     * @checked
     * @noTodo
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            $entity->name = $this->getUniqueName($entity->name, $entity->custom_table_id);
        }
        // スコープを設定
        $this->setTreeScope($entity->custom_table_id);
    }

    /**
     * Before Delete
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @checked
     * @noTodo
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // スコープを設定
        $this->setTreeScope($entity->custom_table_id);
    }

    /**
     * 並び順を更新する
     *
     * @param array $customLinks
     * @checked
     * @noTodo
     */
    public function updateSort(array $customLinks)
    {
        $customLinks = Hash::sort($customLinks, '{n}.sort');
        foreach($customLinks as $field) {
            $targetSort = $field->sort;
            // DBのデータを取得しなおし、lft / rght を最新にする（moveOffset に影響するため）
            $field = $this->get($field->id);
            $currentSort = $this->getCurentSort($field->id, $field->custom_table_id, $field->parent_id);
            $offset = $targetSort - $currentSort;
            $this->moveOffset($field, $offset);
        }
    }

    /**
     * 同じ階層における並び順を取得
     *
     * @param int $id
     * @param int $tableId
     * @param int|null $parentId
     * @return bool|int|null
     * @checked
     * @noTodo
     */
    public function getCurentSort(int $id, int $tableId, $parentId)
    {
        $conditions = ['custom_table_id' => $tableId];
        if ($parentId) {
            $conditions['parent_id'] = $parentId;
        } else {
            $conditions['parent_id IS'] = $parentId;
        }
        $contents = $this->find()
            ->select(['id', 'parent_id', 'title'])
            ->where($conditions)
            ->order('lft');
        $order = null;
        if (!$contents->all()->isEmpty()) {
            foreach($contents as $key => $data) {
                if ($id == $data->id) {
                    $order = $key + 1;
                    break;
                }
            }
        } else {
            return false;
        }
        return $order;
    }

    /**
     * オフセットを元に関連フィールドを移動する
     *
     * @param $id
     * @param $offset
     * @return EntityInterface|bool
     * @checked
     * @noTodo
     */
    public function moveOffset($field, $offset)
    {
        $offset = (int)$offset;
        if ($offset > 0) {
            $result = $this->moveDown($field, abs($offset));
        } elseif ($offset < 0) {
            $result = $this->moveUp($field, abs($offset));
        } else {
            $result = true;
        }
        return $result? $field : false;
    }

    /**
     * 一意の name を取得する
     *
     * @param string $name
     * @param int $tableId
     * @return string
     * @checked
     * @noTodo
     */
    public function getUniqueName(string $name, int $tableId)
    {
        // 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
        $entities = $this->find()
            ->select('name')
            ->where(['name LIKE' => $name . '%', 'custom_table_id' => $tableId])
            ->order('name')
            ->all()
            ->toArray();
        if (!$entities) return $name;

        $names = Hash::extract($entities, '{n}.name');
        $numbers = [];
        foreach($names as $value) {
            if ($name === $value) {
                $numbers[1] = 1;
            } elseif (preg_match("/^" . preg_quote($name, '/') . "_([0-9]+)$/s", $value, $matches)) {
                $numbers[$matches[1]] = true;
            }
        }
        if ($numbers) {
            $prefixNo = 1;
            while(true) {
                if (!isset($numbers[$prefixNo])) break;
                $prefixNo++;
            }
            if ($prefixNo == 1) {
                return $name;
            } else {
                return $name . '_' . ($prefixNo);
            }
        } else {
            return $name;
        }
    }

}
