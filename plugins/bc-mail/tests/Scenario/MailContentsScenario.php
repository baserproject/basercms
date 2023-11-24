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
use BaserCore\Test\Factory\ContentFolderFactory;
use BcMail\Test\Factory\MailContentFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * MailContentsScenario
 *
 * メールフィールドを生成する
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcMail.Factory/MailContents
 * - plugin.BaserCore.Factory/Contents
 */
class MailContentsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        ContentFactory::make([
            'id' => 1,
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'url' => '/',
            'site_id' => 1,
            'title' => 'baserCMSサンプル',
            'entity_id' => 1,
            'parent_id' => null,
            'rght' => 6,
            'lft' => 1,
            'status' => true,
            'created_date' => '2023-02-16 16:41:37',
        ])->persist();
        ContentFolderFactory::make([
            'id' => 1
        ])->persist();

        MailContentFactory::make([
            'id' => 1,
            'description' => 'description test',
            'sender_name' => 'baserCMSサンプル',
            'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
            'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
            'ssl_on' => 0,
            'save_info' => 1,
        ])->persist();
        ContentFactory::make([
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ',
            'entity_id' => 1,
            'parent_id' => 1,
            'rght' => 3,
            'lft' => 2,
            'status' => true,
            'created_date' => '2023-02-16 16:41:37',
        ])->persist();

        MailContentFactory::make([
            'description' => 'description test 2',
            'sender_name' => '送信先名を入力してください',
            'subject_user' => 'お問い合わせ頂きありがとうございます',
            'subject_admin' => 'お問い合わせを頂きました',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
        ])->persist();
        ContentFactory::make([
            'id' => 2,
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/form/',
            'site_id' => 1,
            'title' => 'テスト',
            'entity_id' => 2,
            'rght' => 5,
            'parent_id' => 1,
            'lft' => 4,
            'status' => true,
            'created_date' => '2023-02-16 16:41:37',
        ])->persist();
    }
}
