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
            'maxlength' => 255,
            'options' => 'placeholder|性',
            'group_field' => 'name',
            'group_valid' => 'name',
            'valid' => 0,
            'not_empty' => 1,
            'use_field' => 1,
            'no_send' => 0,
            'sort' => 1,
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
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
    }
}
