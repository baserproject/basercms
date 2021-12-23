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

namespace BaserCore\Test\TestCase\Controller;

use BaserCore\Controller\BcErrorController;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;

/**
 * BcErrorControllerTest Test Case
 */
class BcErrorControllerTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Sites',
    ];

    /**
     * Trait
     */
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcErrorController = new BcErrorController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcErrorController->RequestHandler);
    }

    /**
     * Test beforeRender
     */
    public function testBeforeRender()
    {
        $this->BcErrorController->beforeRender(new Event('beforeRender'));
        $this->assertEquals('BcFront', $this->BcErrorController->viewBuilder()->getTheme());
    }

}
