<?php

namespace BcInstaller\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BcInstaller\BcInstallerPlugin;
use BcInstaller\Command\InstallCheckCommand;
use BcInstaller\Command\InstallCommand;
use BcInstaller\Service\Admin\InstallationsAdminServiceInterface;
use BcInstaller\Service\InstallationsServiceInterface;
use Cake\Console\CommandCollection;
use Cake\Core\Container;

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
        $this->markTestIncomplete('Not implemented yet.');
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
