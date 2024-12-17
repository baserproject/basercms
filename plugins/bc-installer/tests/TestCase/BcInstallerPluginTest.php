<?php

namespace BcInstaller\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BcInstaller\BcInstallerPlugin;
use BcInstaller\Command\InstallCheckCommand;
use BcInstaller\Command\InstallCommand;
use BcInstaller\Service\Admin\InstallationsAdminServiceInterface;
use BcInstaller\Service\InstallationsServiceInterface;
use Cake\Console\CommandCollection;
use Cake\Core\Configure;
use Cake\Core\Container;
use Cake\Routing\Router;

class BcInstallerPluginTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcInstallerPlugin = new BcInstallerPlugin();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test services
     */
    public function test_services()
    {
        $container = new Container();
        $this->BcInstallerPlugin->services($container);
        $this->assertTrue($container->has(InstallationsServiceInterface::class));
        $this->assertTrue($container->has(InstallationsAdminServiceInterface::class));
    }

    /**
     * test routes
     */
    public function test_routes()
    {
        Configure::write('BcEnv.isInstalled', false);
        $routes = Router::createRouteBuilder('/install');
        $this->BcInstallerPlugin->routes($routes);
        $result = Router::parseRequest($this->getRequest('/install'));
        $this->assertEquals('Installations', $result['controller']);

        $routes = Router::createRouteBuilder('/');
        $this->BcInstallerPlugin->routes($routes);
        $result = Router::parseRequest($this->getRequest('/'));
        $this->assertEquals('Installations', $result['controller']);
        Configure::write('BcEnv.isInstalled', true);
    }

    /**
     * test commands
     */
    public function test_commands()
    {
        $commands = new CommandCollection();
        $result = $this->BcInstallerPlugin->console($commands);

        $this->assertEquals(InstallCommand::class, $result->get('install'));
        $this->assertEquals(InstallCheckCommand::class, $result->get('install check'));
    }
}
