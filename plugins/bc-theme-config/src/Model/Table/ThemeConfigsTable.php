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

namespace BcThemeConfig\Model\Table;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use BaserCore\Model\Table\AppTable;
use Cake\Validation\Validator;

/**
 * Class ThemeConfig
 *
 * テーマ設定モデル
 *
 * @package Baser.Model
 */
class ThemeConfigsTable extends AppTable
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
        $this->addBehavior('BaserCore.BcKeyValue');
    }

    /**
     * デフォルトのバリデーション設定
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
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
     * KeyValue のバリデーション設定
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationKeyValue(Validator $validator): Validator
    {
        // TODO ucmitz 未移行
        /*
        $this->validate = [
            'logo' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
            'main_image_1' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
            'main_image_2' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
            'main_image_3' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
            'main_image_4' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
            'main_image_5' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]]
        ];*/
        return $validator;
    }

}
