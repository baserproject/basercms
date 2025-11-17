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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Validation\Validator;

class PermissionGroupsTable extends AppTable
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @checked
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('permission_groups');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->hasMany('Permissions', [
            'className' => 'BaserCore.Permissions',
            'order' => 'id',
            'foreignKey' => 'permission_group_id',
            'dependent' => true,
            'sort' => ['Permissions.sort' => 'ASC'],
        ]);
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
        // id
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 60, __d('baser_core', 'ルールグループ名は60文字以内で入力してください。'))
            ->notEmptyString('name', __d('baser_core', 'ルールグループ名を入力してください。'));

        return $validator;
    }

}
