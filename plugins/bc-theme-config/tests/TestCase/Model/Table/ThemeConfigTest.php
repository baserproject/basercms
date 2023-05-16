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
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BcThemeConfig.Factory/ThemeConfigs',
    ];

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
}
