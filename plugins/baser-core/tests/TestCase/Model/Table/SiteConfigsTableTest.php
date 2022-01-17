<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;

use Cake\Routing\Router;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\SiteConfigsTable;

/**
 * Class SiteConfigsTableTest
 * @package BaserCore\Test\TestCase\Model\Table
 * @property SiteConfigsTable $SiteConfigs
 */
class SiteConfigsTableTest extends BcTestCase
{

    /**
     * Fixtures
     * @var string[]
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SiteConfigs = $this->getTableLocator()->get('BaserCore.SiteConfigs');
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->SiteConfigs);
        Router::reload();
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function testInitialize()
    {
        $this->assertTrue($this->SiteConfigs->hasBehavior('BcKeyValue'));
    }

    /**
     * test validationDefault
     */
    public function testValidationDefault()
    {
        $validator = $this->SiteConfigs->getValidator('default');
        $errors = $validator->validate([
            'name' => ''
        ]);
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals('設定名を入力してください。', current($errors['name']));
        $errors = $validator->validate([
            'name' => str_repeat('a', 256),
            'value' => str_repeat('a', 65536)
        ]);
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals('255文字以内で入力してください。', current($errors['name']));
        $this->assertArrayHasKey('value', $errors);
        $this->assertEquals('65535文字以内で入力してください。', current($errors['value']));
    }

    /**
     * test validationKeyValue Irregular
     */
    public function testValidationKeyValueIrregular()
    {
        $validator = $this->SiteConfigs->getValidator('keyValue');
        $errors = $validator->validate([
            'email' => '',
            'mail_encode' => '',
            'site_url' => ''
        ]);
        $this->assertArrayHasKey('email', $errors);
        $this->assertEquals('管理者メールアドレスを入力してください。', current($errors['email']));
        $this->assertArrayHasKey('mail_encode', $errors);
        $this->assertEquals('メール送信文字コードを入力してください。初期値は「ISO-2022-JP」です。', current($errors['mail_encode']));
        $this->assertArrayHasKey('site_url', $errors);
        $this->assertEquals('WebサイトURLを入力してください。', current($errors['site_url']));
    }

    /**
     * test validationKeyValue Regular
     */
    public function testValidationKeyValueRegular()
    {
        $validator = $this->SiteConfigs->getValidator('keyValue');
        $errors = $validator->validate([
                'formal_name' => 'hoge',
                'name' => 'hoge',
                'email' => 'hoge@basercms.net',
                'mail_encode' => 'ISO-2022-JP',
                'site_url' => 'hoge',
        ]);
        $this->assertEmpty($errors);
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field フィールド名
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getControlSourceDataProvider
     */
    public function testGetControlSource($field, $expected, $message = null)
    {
        $result = $this->SiteConfigs->getControlSource($field);
        $this->assertEquals($expected, $result, $message);
    }

    public function getControlSourceDataProvider()
    {
        return [
            ['mode', [
                0 => 'ノーマルモード',
                1 => 'デバッグモード',
            ], 'コントロールソースを取得できません'],
            ['hoge', false, '存在しないキーです'],
        ];
    }

    /**
     * コンテンツ一覧を表示してから、コンテンツの並び順が変更されていないかどうか
     * @param bool $isLogin ログインしているかどうか
     * @param string $saveValue 保存値
     * @param string $listDisplayed 表示した時間
     * @param bool $expected 期待値
     * @dataProvider isChangedContentsSortLastModifiedDataProvider
     */
    public function testIsChangedContentsSortLastModified($isLogin, $saveValue, $listDisplayed, $expected)
    {
        if($isLogin) Router::setRequest($this->loginAdmin($this->getRequest()));
        $this->SiteConfigs->saveValue('contents_sort_last_modified', $saveValue);
        $result = $this->SiteConfigs->isChangedContentsSortLastModified($listDisplayed);
        $this->assertEquals($expected, $result);
    }

    public function isChangedContentsSortLastModifiedDataProvider()
    {
        return [
            [false, '', '2021/08/01', false], // 保存値なし
            [false, '2021/08/01|1', '2021/08/01', false], // 未ログイン
            [true, '2021/08/01|1', '2021/08/01', false], // 同じユーザーの変更
            [true, '2021/08/01 10:00:00|2', '2021/08/01 10:00:30', true], // バッファ内
            [true, '2021/08/01 10:00:00|2', '2021/08/01 10:01:01', false],  // バッファ外
        ];
    }

    /**
     * コンテンツ並び順変更時間を更新する
     */
    public function testUpdateContentsSortLastModified()
    {
        // 未ログイン
        $this->SiteConfigs->saveValue('contents_sort_last_modified', '');
        $this->SiteConfigs->updateContentsSortLastModified();
        $this->assertEquals('', $this->SiteConfigs->getValue('contents_sort_last_modified'));
        // ログイン
        $this->loginAdmin($this->getRequest());
        $this->SiteConfigs->updateContentsSortLastModified();
        $lastModified = $this->SiteConfigs->getValue('contents_sort_last_modified');
        [$lastModified, $userId] = explode('|', $lastModified);
        $this->assertEquals(1, $userId);
        $this->assertNotEmpty($lastModified);
    }

    /**
     * コンテンツ並び替え順変更時間をリセットする
     */
    public function testResetContentsSortLastModified()
    {
        $this->loginAdmin($this->getRequest());
        $this->SiteConfigs->updateContentsSortLastModified();
        $this->SiteConfigs->resetContentsSortLastModified();
        $this->assertEmpty($this->SiteConfigs->getValue('contents_sort_last_modified'));
    }

    /**
     * testIsChanged
     *
     * @param string $field フィールド名
     * @param string $value 値
     * @param bool $expected 期待値
     * @dataProvider isChangeDataProvider
     */
    public function testIsChange($field, $value, $expected)
    {
        $result = $this->SiteConfigs->isChange($field, $value);
        $this->assertEquals($expected, $result);
    }

    public function isChangeDataProvider()
    {
        return [
            ['use_site_device_setting', "1", false],
            ['use_site_lang_setting', "0", false],
            ['use_site_device_setting', "0", true],
            ['use_site_lang_setting', "1", true]
        ];
    }

}
