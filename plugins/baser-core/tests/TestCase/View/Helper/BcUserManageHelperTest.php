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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcUserManageHelper;

/**
 * Class BcUserManageHelperTest
 *
 * 基本的に UserManageService のラッパークラスのため、ラップしたメソッドのテストは書かない
 *
 * @package BaserCore\Test\TestCase\View\Helper
 */
class BcUserManageHelperTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * BcUserManageHelper
     * @var BcUserManageHelper
     */

    public $BcUserManage;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcUserManage = new BcUserManageHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcUserManage);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertTrue(isset($this->BcUserManage->UserManage));
    }

}
