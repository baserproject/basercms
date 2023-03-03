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

namespace BcMail\Model\Table;
use BaserCore\Event\BcEventDispatcherTrait;
use Cake\Validation\Validator;

/**
 * メール設定モデル
 */
class MailConfigsTable extends MailAppTable
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
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcKeyValue');
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
        $validator
            ->scalar('name')
            ->maxLength('name', 255, __d('baser', '255文字以内で入力してください。'))
            ->requirePresence('name', 'create', __d('baser', '設定名を入力してください。'))
            ->notEmptyString('name', __d('baser', '設定名を入力してください。'));
        $validator
            ->scalar('value')
            ->maxLength('value', 65535, __d('baser', '65535文字以内で入力してください。'));
        return $validator;
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
    public function validationKeyValue(Validator $validator): Validator
    {
        $validator
            ->scalar('site_name')
            ->notEmptyString('site_name', __d('baser', 'Webサイト名を入力してください。'));
        return $validator;
    }

}
