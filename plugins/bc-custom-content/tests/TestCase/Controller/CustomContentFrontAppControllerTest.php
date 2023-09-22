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

namespace BcCustomContent\Test\TestCase\Controller;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Controller\CustomContentFrontAppController;
use Cake\Event\Event;
use Cake\Http\ServerRequest;

/**
 * CustomContentFrontAppControllerTest
 */
class CustomContentFrontAppControllerTest extends BcTestCase
{
    /**
     * Test subject
     *
     * @var CustomContentFrontAppController
     */
    public $CustomContentFrontAppController;

    /**
     * Test subject
     *
     * @var ServerRequest
     */
    public $request;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentFrontAppController = new CustomContentFrontAppController($this->getRequest());
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->CustomContentFrontAppController, $this->request);
    }

    /**
     * test beforeRender
     */
    public function test_beforeRender()
    {
        $this->CustomContentFrontAppController->beforeRender(new Event('beforeRender'));
        $this->assertEquals('BcCustomContent.CustomContentFrontApp', $this->CustomContentFrontAppController->viewBuilder()->getClassName());
    }
}
