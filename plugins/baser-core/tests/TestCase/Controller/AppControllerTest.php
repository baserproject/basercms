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

use BaserCore\Service\SiteConfigServiceInterface;
use BaserCore\Utility\BcContainer;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\AppController;
use ReflectionClass;

/**
 * BaserCore\Controller\AppController Test Case
 * @property AppController $AppController
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
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups'
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

    /**
     * test redirectIfIsNotSameSite
     */
    public function testRedirectIfIsNotSameSite()
    {
        $this->getRequest('https://localhost/index');
        $this->_response = $this->AppController->redirectIfIsNotSameSite();
        $this->assertNull($this->_response);
        $this->getRequest('http://localhost/index');
        $this->_response = $this->AppController->redirectIfIsNotSameSite();
        $this->assertRedirect('https://localhost/index');
        $this->AppController->setRequest($this->getRequest('https://localhost/baser/admin'));
        $this->_response = $this->AppController->redirectIfIsNotSameSite();
        $this->assertNull($this->_response);
    }

    /**
     * test redirectIfIsRequireMaintenance
     */
    public function testRedirectIfIsRequireMaintenance()
    {
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        $siteConfig = BcContainer::get()->get(SiteConfigServiceInterface::class);
        $siteConfig->setValue('maintenance', true);
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        Configure::write('debug', false);
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertRedirect('/maintenance');
        $this->AppController->setRequest($this->getRequest('/', [], 'GET', ['ajax' => true]));
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        $this->AppController->setRequest($this->getRequest('http://localhost/baser/admin'));
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        $this->loginAdmin($this->getRequest());
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        Configure::write('debug', true);
    }

}
