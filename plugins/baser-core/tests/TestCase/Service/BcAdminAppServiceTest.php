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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\BcAdminAppService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcAdminAppServiceTest
 * @property BcAdminAppService $BcAdmin
 */
class BcAdminAppServiceTest extends BcTestCase
{

    /**
     * @var BcAdminAppService|null
     */
    public $BcAdmin;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdmin = new BcAdminAppService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdmin);
        parent::tearDown();
    }

    /**
     * test getViewVarsForAll
     */
    public function test_getViewVarsForAll()
    {
        $vars = $this->BcAdmin->getViewVarsForAll();
        $this->assertTrue(isset($vars['permissionMethodList']));
        $this->assertTrue(isset($vars['permissionAuthList']));
    }

    /**
     * test getViewVarsForAll
     * @return void
     */
    public function testGetViewVarsForAll(){
        $permissionMethodList = [
            '*' => '全て',
            'GET' => '表示のみ',
            'POST' => '表示と編集',
        ];

        $permissionAuthList = [
            0 => '拒否',
            1 => '許可',
        ];

        $useAdminSideBanner = null;

        $vars = $this->BcAdmin->getViewVarsForAll();
        $this->assertEquals($permissionMethodList, $vars['permissionMethodList']);
        $this->assertEquals($permissionAuthList, $vars['permissionAuthList']);
        $this->assertEquals($useAdminSideBanner, $vars['useAdminSideBanner']);
    }
}
