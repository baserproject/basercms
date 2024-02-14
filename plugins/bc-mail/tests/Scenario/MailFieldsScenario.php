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

namespace BcMail\Test\Scenario;

use BaserCore\Test\Factory\ContentFactory;
use BcMail\Test\Factory\MailContentFactory;
use BcMail\Test\Factory\MailFieldsFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * MailFieldsScenario
 *
 * メールフィールドを生成する
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcMail.Factory/MailFields
 */
class MailFieldsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        MailFieldsFactory::make([
            'id' => 1,
            'mail_content_id' => 1,
            'no' => 1,
            'name' => '性',
            'field_name' => 'name_1',
            'type' => 'text',
            'head' => 'お名前',
            'attention' => '',
            'before_attachment' => '',
            'after_attachment' => '',
            'source' => '資料請求|問い合わせ|その他',
            'size' => 1,
            'text_rows' => 1,
            'maxlength' => 1,
            'options' => '',
            'class' => '',
            'default_value' => '',
            'description' => '',
            'group_field' => '',
            'group_valid' => '',
            'valid' => '',
            'valid_ex' => 'VALID_NOT_UNCHECKED',
            'auto_convert' => '',
            'not_empty' => 1,
            'use_field' => 1,
            'no_send' => 0,
            'sort' => '15',
            'created' => '2015-01-27 12:56:54',
            'modified' => null
        ])->persist();
        MailFieldsFactory::make([
            'id' => 2,
            'mail_content_id' => 1,
            'no' => 2,
            'name' => '名',
            'field_name' => 'name_2',
            'type' => 'text',
            'head' => 'お名前',
            'maxlength' => 255,
            'options' => 'placeholder|性',
            'group_field' => 'name',
            'group_valid' => 'name',
            'valid' => 0,
            'not_empty' => 1,
            'use_field' => 1,
            'no_send' => 0,
            'sort' => 2,
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ])->persist();
        MailFieldsFactory::make([
            'id' => 3,
            'mail_content_id' => 1,
            'no' => 3,
            'name' => '性別',
            'field_name' => 'sex',
            'type' => 'radio',
            'head' => '性別',
            'maxlength' => 0,
            'source' => '男性|女性',
            'valid' => 0,
            'not_empty' => 0,
            'use_field' => 1,
            'no_send' => 0,
            'sort' => 3,
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ])->persist();

        MailContentFactory::make([
            'id' => 100,
            'description' => '<p>このコンテンツはメールフォーム機能により作られており、この文章については管理画面の [お問い合わせ] &rarr; [設定] より更新ができます。また、メールフォームは [コンテンツ管理] よりいくつでも作成することができます。</p>',
            'sender_1' => '',
            'sender_2' => '',
            'sender_name' => 'baserCMSサンプル',
            'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
            'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
            'widget_area' => NULL,
            'ssl_on' => 0,
            'save_info' => 1,
            'auth_captcha' => 1,
            'publish_begin' => NULL,
            'publish_end' => NULL,
            'created' => NULL,
            'modified' => NULL,
        ])->persist();
        ContentFactory::make(['plugin' => 'BcMail', 'type' => 'MailContent'])
            ->treeNode(100, 1, 0, 'test', '/contact/', 100, true)->persist();

    }
}
