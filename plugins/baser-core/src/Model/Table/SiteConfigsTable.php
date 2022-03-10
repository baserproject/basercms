<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Model\Table;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\AppTable;
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
 * @package BaserCore\Model\Table
 * @method SiteConfig newEntity($data = null, array $options = [])
 * @method BcKeyValueBehavior setValue($key, $value)
 */
class SiteConfigsTable extends AppTable
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
        $validator->setProvider('siteConfig', 'BaserCore\Model\Validation\SiteConfigValidation');

        $validator
            ->scalar('email')
            ->email('email', 255, __d('baser', '管理者メールアドレスの形式が不正です。'))
            ->notEmptyString('email', __d('baser', '管理者メールアドレスを入力してください。'));
        $validator
            ->scalar('mail_encode')
            ->notEmptyString('mail_encode', __d('baser', 'メール送信文字コードを入力してください。初期値は「ISO-2022-JP」です。'));
        $validator
            ->scalar('main_site_display_name')
            ->notEmptyString('main_site_display_name', __d('baser', 'メインサイト表示名を入力してください。'));
        $validator
            ->scalar('site_url')
            ->notEmptyString('site_url', __d('baser', 'WebサイトURLを入力してください。'));
        $validator
            ->scalar('admin_ssl')
            ->add('admin_ssl', [
                'adminSSlSslUrlExists' => [
                    'rule' => 'sslUrlExists',
                    'provider' => 'siteConfig',
                    'message' => __d('baser', '管理画面をSSLで利用するには、SSL用のWebサイトURLを入力してください。')
                ]]);
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
                0 => __d('baser', 'ノーマルモード'),
                1 => __d('baser', 'デバッグモード')
        ]];
        if (isset($controlSources[$field])) {
            return $controlSources[$field];
        } else {
            return false;
        }
    }

    /**
     * コンテンツ一覧を表示してから、コンテンツの並び順が変更されていないかどうか
     * 60秒をブラウザのロード時間を加味したバッファとする
     * @param $listDisplayed
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isChangedContentsSortLastModified($listDisplayed)
    {
        $lastModified = $this->getValue('contents_sort_last_modified');
        $changed = false;
        if ($lastModified) {
            $user = BcUtil::loginUser();
            if(!$user) {
                return false;
            }
            [$lastModified, $userId] = explode('|', $lastModified);
            $lastModified = strtotime($lastModified);
            if ($user->id !== (int) $userId) {
                $listDisplayed = strtotime($listDisplayed);
                if ($lastModified >= ($listDisplayed - 60)) {
                    $changed = true;
                }
            }
        }
        return $changed;
    }

    /**
     * コンテンツ並び順変更時間を更新する
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updateContentsSortLastModified()
    {
        $user = BcUtil::loginUser();
        if(!$user) {
            return false;
        }
        return $this->saveValue(
            'contents_sort_last_modified',
            date('Y-m-d H:i:s') . '|' . $user->id
        );
    }

    /**
     * コンテンツ並び替え順変更時間をリセットする
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetContentsSortLastModified()
    {
        return $this->saveValue('contents_sort_last_modified', '');
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
