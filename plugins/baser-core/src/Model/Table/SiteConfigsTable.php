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

use BaserCore\Model\Behavior\BcKeyValueBehavior;
use BaserCore\Model\Entity\SiteConfig;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;

/**
 * Class SiteConfig
 *
 * システム設定モデル
 *
 * @method SiteConfig newEntity($data = null, array $options = [])
 * @method BcKeyValueBehavior saveValue($key, $value)
 */
class SiteConfigsTable extends AppTable
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
            ->maxLength('name', 255, __d('baser_core', '255文字以内で入力してください。'))
            ->requirePresence('name', 'create', __d('baser_core', '設定名を入力してください。'))
            ->notEmptyString('name', __d('baser_core', '設定名を入力してください。'));
        $validator
            ->scalar('value')
            ->maxLength('value', 65535, __d('baser_core', '65535文字以内で入力してください。'));
        return $validator;
    }

    /**
     * Validation Key Value
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
            ->scalar('email')
            ->notEmptyString('email', __d('baser_core', '管理者メールアドレスを入力してください。'))
            ->add('email', ['emails' => [
                'rule' => 'emails',
                'provider' => 'bc',
                'message' => __d('baser_core', '管理者メールアドレスの形式が不正です。')
            ]]);
        $validator
            ->scalar('site_url')
            ->regex('site_url', '/^(http|https):/', __d('baser_core', 'WebサイトURLはURLの形式を入力してください。'))
            ->notEmptyString('site_url', __d('baser_core', 'WebサイトURLを入力してください。'));
        $validator
            ->allowEmptyString('password_reset_days')
            ->nonNegativeInteger('password_reset_days', __d('baser_core', 'パスワードの再設定日数は0以上の整数を入力してください。'));
        return $validator;
    }

    /**
     * コントロールソースを取得する
     * @param string $field
     * @return mixed array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource($field)
    {
        $controlSources = [
            'mode' => [
                0 => __d('baser_core', 'ノーマルモード'),
                1 => __d('baser_core', 'デバッグモード')
            ]];
        if (isset($controlSources[$field])) {
            return $controlSources[$field];
        } else {
            return false;
        }
    }

    /**
     * 指定したフィールドの値がDBのデータと比較して変更状態か確認
     *
     * @param string $field フィールド名
     * @param string $value 値
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isChange($field, $value)
    {
        $siteConfig = $this->getKeyValue();
        if (isset($siteConfig[$field])) {
            return !($siteConfig[$field] === $value);
        } else {
            return false;
        }
    }

}
