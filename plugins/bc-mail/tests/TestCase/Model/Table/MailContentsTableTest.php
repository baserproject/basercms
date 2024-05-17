<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcMail\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Model\Entity\MailContent;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Test\Scenario\MailContentsScenario;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class MailContentTest
 *
 * @property MailContent $MailContent
 */
class MailContentsTableTest extends BcTestCase
{
    use ScenarioAwareTrait;
    use BcContainerTrait;
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->MailContent = $this->getTableLocator()->get('BcMail.MailContents');
        parent::setUp();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->MailContent);
        parent::tearDown();
    }

    /**
     * validate
     */
    public function testNotErrors()
    {
        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'name' => '0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789',
            'sender_name' => '01234567890123456789012345678901234567890123456789',
            'subject_user' => '01234567890123456789012345678901234567890123456789',
            'subject_admin' => '01234567890123456789012345678901234567890123456789',
            'layout_template' => '01234567890123456789',
            'form_template' => '01234567890123456789',
            'mail_template' => '01234567890123456789',
            'redirect_url' => 'https://basercms.net/',
            'sender_1' => 'test1@example.co.jp',
            'sender_2' => 'test2@example.co.jp',
            'ssl_on' => ''
        ]);

        $this->assertCount(0, $errors);
    }

    public function testEmptyErrors()
    {
        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'name' => '',
            'title' => '',
            'sender_name' => '',
            'subject_user' => '',
            'subject_admin' => '',
            'layout_template' => '',
            'form_template' => '',
            'mail_template' => '',
            'redirect_url' => '',
            'sender_1' => '',
            'sender_2' => '',
            'ssl_on' => ''
        ]);
        $this->assertEquals('自動返信メール件名[ユーザー宛]を入力してください。', current($errors['subject_user']));
        $this->assertEquals('自動返信メール件名[管理者宛]を入力してください。', current($errors['subject_admin']));
    }

    public function testCheckMaxLength()
    {
        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'sender_name' => str_repeat('a', 256),
            'subject_user' => str_repeat('a', 256),
            'subject_admin' => str_repeat('a', 256),
            'form_template' => str_repeat('a', 21),
            'mail_template' => str_repeat('a', 21),
            'redirect_url' => '/' . str_repeat('a', 256),
            'sender_1' => str_repeat('a', 256) . '@example.com',
            'sender_2' => str_repeat('a', 256) . '@example.com',
            'ssl_on' => ''
        ]);

        $this->assertEquals('自動返信メール件名[ユーザー宛]は255文字以内で入力してください。', current($errors['subject_user']));
        $this->assertEquals('自動返信メール件名[管理者宛]は255文字以内で入力してください。', current($errors['subject_admin']));
        $this->assertEquals('フォームテンプレート名は20文字以内で入力してください。', current($errors['form_template']));
        $this->assertEquals('送信メールテンプレート名は20文字以内で入力してください。', current($errors['mail_template']));
        $this->assertEquals('リダイレクトURLは255文字以内で入力してください。', current($errors['redirect_url']));
    }

    public function testHalfTextErrors()
    {
        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'form_template' => '１２３ａｂｃ',
            'mail_template' => '１２３ａｂｃ',
            'ssl_on' => ''
        ]);

        $this->assertEquals('メールフォームテンプレート名は半角のみで入力してください。', current($errors['form_template']));
        $this->assertEquals('送信メールテンプレートは半角のみで入力してください。', current($errors['mail_template']));
    }

    public function testInputTypeErrors()
    {
        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'redirect_url' => 'hoge',
            'sender_1' => 'hoge',
            'sender_2' => 'hoge',
            'ssl_on' => ''
        ]);

        $this->assertEquals('送信先メールアドレスのEメールの形式が不正です。', current($errors['sender_1']));
        $this->assertEquals('BCC用送信先メールアドレスのEメールの形式が不正です。', current($errors['sender_2']));
        $this->assertEquals('リダイレクトURLはURLの形式を入力してください。', current($errors['redirect_url']));
    }

    public function testSSLTrue()
    {
        Configure::write('BcEnv.sslUrl', 'on');
        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'ssl_on' => ['on'],
        ]);
        $this->assertCount(0, $errors);
    }

    public function testSSLFalse()
    {
        Configure::write('BcEnv.sslUrl', '');
        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'ssl_on' => ['on'],
        ]);

        $this->assertEquals('SSL通信を利用するには、システム設定で、事前にSSL通信用のWebサイトURLを指定してください。', current($errors['ssl_on']));
    }

    public function testURLErrors()
    {
        //エラーテスト
        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'redirect_url' => 'hoge',
        ]);

        $this->assertEquals('リダイレクトURLはURLの形式を入力してください。', current($errors['redirect_url']));

        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'redirect_url' => 'あああ',
        ]);

        $this->assertEquals('リダイレクトURLはURLの形式を入力してください。', current($errors['redirect_url']));

        //正常テスト
        $validator = $this->MailContent->getValidator('default');
        //スラッシュから始まるURL
        $errors = $validator->validate([
            'redirect_url' => '/baser/admin/baser-core/users/index',
        ]);
        $this->assertCount(0, $errors);

        //httpから始まるURL
        $validator = $this->MailContent->getValidator('default');
        $errors = $validator->validate([
            'redirect_url' => 'https://basercms.net',
        ]);

        $this->assertCount(0, $errors);
    }

    /**
     * SSL用のURLが設定されているかチェックする
     */
    public function testCheckSslUrl()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 英数チェック
     */
    public function testAlphaNumeric()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * afterSave
     *
     * @param boolean $exclude_search
     * @dataProvider afterSaveDataProvider
     */
    public function testAfterSave($exclude_search)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 初期化
        $data = [
            'MailContent' => [
                'description' => 'hogeDescription',
            ],
            'Content' => [
                'name' => 'hogeName',
                'title' => 'hogeTitle',
                'self_status' => true,
                'status' => true,
                'exclude_search' => $exclude_search,
                'parent_id' => 1,
                'site_id' => 0
            ]
        ];

        // データ保存
        $this->MailContent->save($data);

        // Contentチェック
        $this->SearchIndex = ClassRegistry::init('SearchIndex');
        $result = $this->SearchIndex->find('first', [
            'conditions' => ['title' => 'hogeTitle']
        ]);
        if (!$exclude_search) {
            $this->assertNotEmpty($result, 'SearchIndexにデータを追加できません');
        } else {
            $this->assertEmpty($result, 'SearchIndexのデータを削除できません');
        }
    }

    public static function afterSaveDataProvider()
    {
        return [
            [false],
            [true],
        ];
    }

    /**
     * beforeDelete
     */
    public function testBeforeDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 削除実行
        $this->MailContent->data = ['MailContent' => ['name' => 'contact']];
        $this->MailContent->delete(1);

        $contents = $this->MailContent->find('all');
        $this->MailField = ClassRegistry::init('MailField');
        $fields = $this->MailField->find('all');

        // Mail関連チェック
        $this->assertEmpty($contents, 'メールコンテンツデータを削除できません');
        $this->assertEmpty($fields, '関連したメールフィールドデータを削除できません');

        // SearchIndexチェック
        $this->SearchIndex = ClassRegistry::init('SearchIndex');
        $result = $this->SearchIndex->find('all', ...[
            'conditions' => ['type' => 'メール', 'model_id' => 1]
        ]);
        $this->assertEmpty($result, '関連したSearchIndexを削除できません');
    }

    /**
     * 検索用データを生成する
     *
     * @param int $id 入力するidの値
     * @param int $mailContentId MailContentインスタンスに設定するid
     * @param int $expected idの期待値
     * @param string $message テスト失敗時に表示するメッセージ
     * @dataProvider createContentDataProvider
     */
    public function createSearchIndex($id, $mailContentId, $expected, $message)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // 初期化
        $data = ['MailContent' => [
            'id' => $id,
            'title' => 'タイトル',
            'description' => '説明',
            'name' => '名前',
            'status' => 'ステータス',
        ]];
        $this->MailContent->id = $mailContentId;

        $result = $this->MailContent->createContent($data);
        $expected = [
            'SearchIndex' => [
                'type' => 'メール',
                'model_id' => $expected,
                'category' => '',
                'title' => 'タイトル',
                'detail' => '説明',
                'url' => '/名前/index',
                'status' => 'ステータス'
            ]
        ];
        $this->assertEquals($expected, $result, $message);
    }

    /**
     * 検索用データを生成する
     */
    public function testCreateSearchIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public static function createContentDataProvider()
    {
        return [
            [1, 2, 1, '検索用データを正しく生成できません'],
            [null, 11, 11, '検索用データを正しく生成できません'],
        ];
    }

    /**
     * メールコンテンツデータをコピーする
     *
     * @param int $id
     * @param int $newParentId 新しい親コンテンツID
     * @param string $newTitle 新しいタイトル
     * @param int $newAuthorId 新しい作成者ID
     * @param int $newSiteId 新しいサイトID
     * @param array $expected 期待値
     * @dataProvider copyDataProvider
     */
    public function testCopy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $db = $this->MailContent->getDataSource();
        switch ($db->config['datasource']) {
            case 'Database/BcSqlite':
                $this->markTestIncomplete('このテストは、まだ実装されていません。');
                $command = '.schema';
            default:
        }

        $result = $this->MailContent->copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId);

        if (!is_null($id)) {
            $this->assertMatchesRegularExpression('/hogeName/', $result['Content']['title'], 'メールコンテンツデータをコピーできません');
            // メールフィールドもコピーされているか
            $this->MailField = ClassRegistry::init('MailField');
            $field = $this->MailField->find(
                'first',
                [
                    'conditions' => ['id' => 19]
                ]
            );

            $this->assertEquals(2, $field['MailField']['mail_content_id'], 'メールフィールドデータをコピーできません');
        }
    }

    public static function copyDataProvider()
    {
        return [
            [1, 1, 'hogeName', 1, 0]
        ];
    }

    /**
     * フォームが公開中かどうかチェックする
     */
    public function testIsAccepting()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 公開済の conditions を取得
     */
    public function testGetConditionAllowAccepting()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 公開されたコンテンツを取得する
     */
    public function testFindAccepting()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test createSearchIndex
     */

    public function test_createSearchIndex()
    {
        $this->loadFixtureScenario(MailContentsScenario::class);
        $mailContentServices = $this->getService(MailContentsServiceInterface::class);

        $mailContent = $mailContentServices->get(1);
        $mailContent->content->publish_begin = '2015-01-27 12:56:53';
        $mailContent->content->publish_end = '2015-02-27 12:56:53';

        $result = $this->MailContent->createSearchIndex($mailContent);
        $this->assertEquals($result['type'], 'メール');
        $this->assertEquals($result['model_id'], 1);
        $this->assertEquals($result['site_id'], 1);
        $this->assertEquals($result['title'], 'お問い合わせ');
        $this->assertEquals($result['detail'], 'description test');
        $this->assertEquals($result['url'], '/contact/');
        $this->assertTrue($result['status']);
        $this->assertEquals($result['publish_begin'], '2015-01-27 12:56:53');
        $this->assertEquals($result['publish_end'], '2015-02-27 12:56:53');
    }
}
