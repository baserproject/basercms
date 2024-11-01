<?php

namespace BaserCore\Test\TestCase\Command;

use BaserCore\TestSuite\BcTestCase;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;

class SetupInstallCommandTest extends BcTestCase
{
    /**
     * Trait
     */
    use ConsoleIntegrationTestTrait;

    /**
     * setUp
     *
     * @return void
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
     * test execute
     */
    public function testExecute()
    {
        //backup file
        copy(ROOT . DS . 'config' . DS . 'install.php', ROOT . DS . 'config' . DS . 'install.php.bak');
        //test
        $this->exec('setup install test');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('インストールの準備ができました。');

        //backup
        rename(ROOT . DS . 'config' . DS . 'install.php.bak', ROOT . DS . 'config' . DS . 'install.php');
    }
}
