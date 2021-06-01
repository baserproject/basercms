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

namespace BaserCore\Test\TestCase\Utility;

use App\Application;
use BaserCore\Plugin;
use BaserCore\Service\UserManageServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Container;

/**
 * Class BcContainerTraitTest
 * @package BaserCore\Test\TestCase\Utility
 */
class BcContainerTraitTest extends BcTestCase
{

   /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getService
     */
    public function testGetService()
    {
        $app = new Application(ROOT . '/config');
        $app->getContainer();
        $bcContainerTrait = new class { use BcContainerTrait; };
        $this->assertEquals('BaserCore\Service\UserManageService', get_class($bcContainerTrait->getService(UserManageServiceInterface::class)));
    }

}
