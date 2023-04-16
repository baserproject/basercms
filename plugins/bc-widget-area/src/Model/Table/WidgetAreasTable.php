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

namespace BcWidgetArea\Model\Table;

use BaserCore\Model\Table\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Validation\Validator;

/**
 * Class WidgetArea
 *
 * ウィジェットエリアモデル
 *
 */
class WidgetAreasTable extends AppTable
{

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
        $this->setTable('widget_areas');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->setProvider('user', 'BaserCore\Model\Validation\UserValidation');

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('name')
            ->notEmptyString('name', __d('baser_core', 'ウィジェットエリア名を入力してください。'))
            ->maxLength('name', 255, __d('baser_core', 'ウィジェットエリア名は255文字以内で入力してください。'));

        return $validator;
    }

}
