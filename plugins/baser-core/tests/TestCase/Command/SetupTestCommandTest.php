<?php

namespace BaserCore\Test\TestCase\Command;

use BaserCore\Command\SetupTestCommand;
use BaserCore\TestSuite\BcTestCase;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;

class SetupTestCommandTest extends BcTestCase
{

    /**
     * Trait
     */
    use ConsoleIntegrationTestTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testExecute()
    {
        $this->exec('setup test');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('ユニットテストの準備ができました。');
        $this->assertEquals('true', env('DEBUG'));
        $this->assertEquals('true' ,env('USE_CORE_API'));
        $this->assertEquals('true' ,env('USE_CORE_ADMIN_API'));
    }
}
