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
     * @unitTest
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
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255, __d('baser_core', '255文字以内で入力してください。'))
            ->requirePresence('name', 'create', __d('baser_core', '設定名を入力してください。'))
            ->notEmptyString('name', __d('baser_core', '設定名を入力してください。'));
        $validator
            ->scalar('value')
            ->maxLength('value', 65535, __d('baser_core', '65535文字以内で入力してください。'));
        return $validator;
    }

    /**
     * KeyValue のバリデーション設定
     *
     * @param Validator $validator
     * @noTodo
     * @checked
     * @unitTest
     * @return Validator
     */
    public function validationKeyValue(Validator $validator): Validator
    {
        // color_main
        $validator
            ->allowEmptyString('color_main')
            ->add('color_main', [
                'hexColorPlus' => [
                    'rule' => 'hexColorPlus',
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'メインのカラーコードの形式が間違っています。')
                ]
            ]);

        // color_sub
        $validator
            ->allowEmptyString('color_sub')
            ->add('color_sub', [
                'hexColorPlus' => [
                    'rule' => 'hexColorPlus',
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'サブのカラーコードの形式が間違っています。')
                ]
            ]);

        // color_link
        $validator
            ->allowEmptyString('color_link')
            ->add('color_link', [
                'hexColorPlus' => [
                    'rule' => 'hexColorPlus',
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'テキストリンクのカラーコードの形式が間違っています。')
                ]
            ]);

        // color_hover
        $validator
            ->allowEmptyString('color_hover')
            ->add('color_hover', [
                'hexColorPlus' => [
                    'rule' => 'hexColorPlus',
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'テキストホバーのカラーコードの形式が間違っています。')
                ]
            ]);

        // logo
        $validator->add('logo', [
            'fileExt' => [
                'rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'],
                'provider' => 'bc',
                'message' => __d('baser_core', '許可されていないファイルです。')
            ]
        ]);

        // main_image_1
        $validator->add('main_image_1', [
            'fileExt' => [
                'rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'],
                'provider' => 'bc',
                'message' => __d('baser_core', '許可されていないファイルです。')
            ]
        ]);

        // main_image_2
        $validator->add('main_image_2', [
            'fileExt' => [
                'rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'],
                'provider' => 'bc',
                'message' => __d('baser_core', '許可されていないファイルです。')
            ]
        ]);

        // main_image_3
        $validator->add('main_image_3', [
            'fileExt' => [
                'rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'],
                'provider' => 'bc',
                'message' => __d('baser_core', '許可されていないファイルです。')
            ]
        ]);

        // main_image_4
        $validator->add('main_image_4', [
            'fileExt' => [
                'rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'],
                'provider' => 'bc',
                'message' => __d('baser_core', '許可されていないファイルです。')
            ]
        ]);

        // main_image_5
        $validator->add('main_image_5', [
            'fileExt' => [
                'rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'],
                'provider' => 'bc',
                'message' => __d('baser_core', '許可されていないファイルです。')
            ]
        ]);
        return $validator;
    }

}
