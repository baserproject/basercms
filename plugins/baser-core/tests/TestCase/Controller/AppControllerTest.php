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

use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\AppController;
use ReflectionClass;

/**
 * BaserCore\Controller\AppController Test Case
 */
class AppControllerTest extends BcTestCase
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
        $this->AppController = new AppController($this->getRequest());
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
     * Test construct
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->AppController->BcMessage);
        $this->assertNotEmpty($this->AppController->Security);
        $this->assertNotEmpty($this->AppController->Paginator);
    }

    /**
     * Test beforeRender
     */
    public function testBeforeRender()
    {
        $this->AppController->beforeRender(new Event('beforeRender'));
        $this->assertEquals('BaserCore.App', $this->AppController->viewBuilder()->getClassName());
        $this->assertEquals('BcFront', $this->AppController->viewBuilder()->getTheme());
    }

    /**
     * Test setTitle method
     *
     * @return void
     */
    public function testSetTitle()
    {
        $template = 'test';
        $this->AppController->setTitle($template);
        $viewBuilder = new ReflectionClass($this->AppController->viewBuilder());
        $vars = $viewBuilder->getProperty('_vars');
        $vars->setAccessible(true);
        $actual = $vars->getValue($this->AppController->viewBuilder())['title'];
        $this->assertEquals($template, $actual);
    }

}
