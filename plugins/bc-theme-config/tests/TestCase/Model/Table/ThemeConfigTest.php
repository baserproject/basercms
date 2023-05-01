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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
