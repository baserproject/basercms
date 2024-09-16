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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use Cake\Routing\Router;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\SiteConfigsTable;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SiteConfigsTableTest
 * @property SiteConfigsTable $SiteConfigs
 */
class SiteConfigsTableTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

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
            'site_url' => ''
        ]);
        $this->assertArrayHasKey('email', $errors);
        $this->assertEquals('管理者メールアドレスを入力してください。', current($errors['email']));
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
                'site_url' => 'https://localhost/',
        ]);
        $this->assertEmpty($errors);
    }

    /**
     * test validationKeyValue url
     */
    public function testvalidationKeyValueURL()
    {
        $validator = $this->SiteConfigs->getValidator('keyValue');
        $errors = $validator->validate([
            'site_url' => 'hoge',
        ]);
        $this->assertEquals('WebサイトURLはURLの形式を入力してください。', current($errors['site_url']));

        $validator = $this->SiteConfigs->getValidator('keyValue');
        $errors = $validator->validate([
            'site_url' => '/hoge',
        ]);
        $this->assertEquals('WebサイトURLはURLの形式を入力してください。', current($errors['site_url']));
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

    public static function getControlSourceDataProvider()
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
     * testIsChanged
     *
     * @param string $field フィールド名
     * @param string $value 値
     * @param bool $expected 期待値
     * @dataProvider isChangeDataProvider
     */
    public function testIsChange($field, $value, $expected)
    {
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $result = $this->SiteConfigs->isChange($field, $value);
        $this->assertEquals($expected, $result);
    }

    public static function isChangeDataProvider()
    {
        return [
            ['use_site_device_setting', "1", false],
            ['use_site_lang_setting', "0", false],
            ['use_site_device_setting', "0", true],
            ['use_site_lang_setting', "1", true]
        ];
    }

}
