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

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Middleware\BcAdminMiddleware;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcAdminMiddlewareTest
 * @property BcAdminMiddleware $BcAdminMiddleware
 */
class BcAdminMiddlewareTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminMiddleware = new BcAdminMiddleware();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminMiddleware);
        parent::tearDown();
    }

    /**
     * Process
     */
    public function testProcess(): void
    {
        $request = $this->getRequest('/baser/admin/?site_id=3');
        $request = $this->execPrivateMethod($this->BcAdminMiddleware, 'setCurrentSite', [$request]);
        $this->assertEquals(3, $request->getAttribute('currentSite')->id);
    }

}
