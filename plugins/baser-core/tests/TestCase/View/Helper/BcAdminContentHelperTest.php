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
use BaserCore\View\Helper\BcAdminContentHelper;

/**
 * Class BcAdminContentHelperTest
 *
 *
 * @package BaserCore\Test\TestCase\View\Helper
 */
class BcAdminContentHelperTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * BcAdminContentHelper
     * @var BcAdminContentHelper
     */

    public $BcAdminContent;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminContent = new BcAdminContentHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcAdminContent);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertTrue(isset($this->BcAdminContent->ContentService));
    }

    public function testGetType()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetAuthors()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
