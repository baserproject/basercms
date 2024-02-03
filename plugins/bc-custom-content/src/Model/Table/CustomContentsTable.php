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

use BaserCore\Model\Table\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Validation\Validator;

/**
 * CustomContentsTable
 *
 * @property CustomTablesTable $CustomTables
 */
class CustomContentsTable extends AppTable
{
    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcContents');
        $this->addBehavior('Timestamp');
        $this->belongsTo('CustomTables', ['className' => 'BcCustomContent.CustomTables'])
            ->setForeignKey('custom_table_id');
    }


    /**
     * デフォルトのバリデーションを設定する
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     */
    public function validationWithTable(Validator $validator): Validator
    {
        $validator->setProvider('bc', 'BaserCore\Model\Validation\BcValidation');
        $validator->requirePresence('list_count', 'update')
            ->notEmptyString('list_count', __d('baser_core', '一覧表示件数は必須項目です。'))
            ->range('list_count', [0, 101], __d('baser_core', '一覧表示件数は100までの数値で入力してください。'))
            ->add('list_count', 'halfText', [
                'provider' => 'bc',
                'rule' => 'halfText',
                'message' => __d('baser_core', '一覧表示件数は半角で入力してください。')]);
        return $validator;
    }

}
