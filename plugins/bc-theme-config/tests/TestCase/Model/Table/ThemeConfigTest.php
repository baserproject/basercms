<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcThemeConfig\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcThemeConfig\Model\Table\ThemeConfigsTable;

/**
 * Class BcThemeConfigTest
 * @property ThemeConfigsTable $ThemeConfigsTable
 */
class ThemeConfigTest extends BcTestCase
{

    /**
     * @var ThemeConfigsTable
     */
    public $ThemeConfigsTable;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeConfigsTable = $this->getTableLocator()->get('BcThemeConfig.ThemeConfigs');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ThemeConfigsTable);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function test_initialize()
    {
        $this->assertTrue($this->ThemeConfigsTable->hasBehavior('BcKeyValue'));
    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $validator = $this->ThemeConfigsTable->getValidator('default');

        //設定名が指定しない場合、
        $errors = $validator->validate([
            'name' => ''
        ]);
        //戻り値を確認
        $this->assertEquals('設定名を入力してください。', current($errors['name']));

        //maxLength　テスト、
        $errors = $validator->validate([
            'name' => str_repeat('a', 256),
            'value' => str_repeat('a', 65536)
        ]);
        //戻り値を確認
        $this->assertEquals('255文字以内で入力してください。', current($errors['name']));
        $this->assertEquals('65535文字以内で入力してください。', current($errors['value']));
    }

    /**
     * test validationKeyValue
     */
    public function test_validationKeyValue()
    {
        $validator = $this->ThemeConfigsTable->getValidator('keyValue');
        $errors = $validator->validate([
            'logo' => 'logo.ppp',
            'main_image_1' => 'logo.ppp',
            'main_image_2' => 'logo.ppp',
            'main_image_3' => 'logo.ppp',
            'main_image_4' => 'logo.ppp',
            'main_image_5' => 'logo.ppp',
        ]);
        $this->assertArrayHasKey('logo', $errors);
        $this->assertEquals('許可されていないファイルです。', current($errors['logo']));
        $this->assertArrayHasKey('main_image_1', $errors);
        $this->assertEquals('許可されていないファイルです。', current($errors['main_image_1']));
        $this->assertArrayHasKey('main_image_2', $errors);
        $this->assertEquals('許可されていないファイルです。', current($errors['main_image_2']));
        $this->assertArrayHasKey('main_image_3', $errors);
        $this->assertEquals('許可されていないファイルです。', current($errors['main_image_3']));
        $this->assertArrayHasKey('main_image_4', $errors);
        $this->assertEquals('許可されていないファイルです。', current($errors['main_image_4']));
        $this->assertArrayHasKey('main_image_5', $errors);
        $this->assertEquals('許可されていないファイルです。', current($errors['main_image_5']));
    }

}
