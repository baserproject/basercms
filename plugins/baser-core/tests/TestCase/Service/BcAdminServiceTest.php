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

use BaserCore\Service\BcAdminService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcAdminServiceTest
 * @property BcAdminService $BcAdmin
 */
class BcAdminServiceTest extends BcTestCase
{

    /**
     * @var BcAdminService|null
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
        $this->BcAdmin = new BcAdminService();
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

}
