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
use BaserCore\View\Helper\BcAdminUserHelper;

/**
 * Class BcAdminUserHelperTest
 *
 * 基本的に UserManageService のラッパークラスのため、ラップしたメソッドのテストは書かない
 *
 * @package BaserCore\Test\TestCase\View\Helper
 */
class BcAdminUserHelperTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * BcAdminUserHelper
     * @var BcAdminUserHelper
     */

    public $BcAdminUser;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminUser = new BcAdminUserHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcAdminUser);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertTrue(isset($this->BcAdminUser->UserManage));
    }

}
