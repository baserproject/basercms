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

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use BaserCore\Controller\Admin\BcAdminAppController;

/**
 * BaserCore\Controller\BcAdminAppController Test Case
 */
class BcAdminAppControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->BcAdminApp = new BcAdminAppController();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminApp);
        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcAdminApp->BcMessage);
        $this->assertNotEmpty($this->BcAdminApp->Authentication);
        $this->assertNotEmpty($this->BcAdminApp->Paginator);
    }

    /**
     * Test setViewConditions method
     *
     * @return void
     */
    public function testSetViewConditions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test loadViewConditions method
     *
     * @return void
     */
    public function testLoadViewConditions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test saveViewConditions method
     *
     * @return void
     */
    public function testSaveViewConditions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test beforeRender method
     *
     * @return void
     */
    public function testBeforeRender()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test setTitle method
     *
     * @return void
     */
    public function testSetTitle()
    {
        $ref = new \ReflectionClass($this->BcAdminApp);
        $method = $ref->getMethod('setTitle');
        $method->setAccessible(true);

        $template = 'test';
        $method->invokeArgs($this->BcAdminApp, [$template]);

        $viewBuilder = new \ReflectionClass($this->BcAdminApp->viewBuilder());
        $vars = $viewBuilder->getProperty('_vars');
        $vars->setAccessible(true);
        $actual = $vars->getValue($this->BcAdminApp->viewBuilder())['title'];
        $this->assertEquals($template, $actual);
    }

    /**
     * Test setSearch method
     *
     * @return void
     */
    public function testSetSearch()
    {
        $ref = new \ReflectionClass($this->BcAdminApp);
        $method = $ref->getMethod('setSearch');
        $method->setAccessible(true);

        $template = 'test';
        $method->invokeArgs($this->BcAdminApp, [$template]);

        $viewBuilder = new \ReflectionClass($this->BcAdminApp->viewBuilder());
        $vars = $viewBuilder->getProperty('_vars');
        $vars->setAccessible(true);
        $actual = $vars->getValue($this->BcAdminApp->viewBuilder())['search'];
        $this->assertEquals($template, $actual);
    }

    /**
     * Test setHelp method
     *
     * @return void
     */
    public function testSetHelp()
    {
        $ref = new \ReflectionClass($this->BcAdminApp);
        $method = $ref->getMethod('setHelp');
        $method->setAccessible(true);

        $template = 'test';
        $method->invokeArgs($this->BcAdminApp, [$template]);

        $viewBuilder = new \ReflectionClass($this->BcAdminApp->viewBuilder());
        $vars = $viewBuilder->getProperty('_vars');
        $vars->setAccessible(true);
        $actual = $vars->getValue($this->BcAdminApp->viewBuilder())['help'];
        $this->assertEquals($template, $actual);
    }
}
