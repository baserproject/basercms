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

namespace BaserCore\Test\TestCase\View;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\AppView;

/**
 * Class AppViewTest
 * @package BaserCore\Test\TestCase\View;
 * @property AppView $AppView
 */
class AppViewTest extends BcTestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->AppView = new AppView();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->AppView);
        parent::tearDown();
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertNotEmpty($this->AppView->BcPage);
        $this->assertNotEmpty($this->AppView->BcBaser);
    }

    /**
     * メソッドが未実装でwarningが出るためサンプルメソッド配置
     * @test
     * @return void
     */
    public function sampleTest(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
