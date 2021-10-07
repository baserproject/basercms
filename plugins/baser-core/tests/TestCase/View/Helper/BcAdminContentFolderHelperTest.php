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
use BaserCore\View\Helper\BcAdminContentFolderHelper;

/**
 * Class BcAdminContentFolderHelperTest
 *
 *
 * @package BaserCore\Test\TestCase\View\Helper
 */
class BcAdminContentFolderHelperTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * BcAdminContentFolderHelper
     * @var BcAdminContentFolderHelper
     */

    public $BcAdminContentFolder;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Permissions',
    ];

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminContentFolder = new BcAdminContentFolderHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcAdminContentFolder);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertTrue(isset($this->BcAdminContentFolder->ContentFolderService));
    }
}
