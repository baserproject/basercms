<?php

namespace BaserCore\Test\TestCase\Command;

use BaserCore\Command\SetupTestCommand;
use BaserCore\TestSuite\BcTestCase;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

class SetupTestCommandTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->command = new SetupTestCommand();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testExecute()
    {
        $args = new Arguments([], [], []);
        $io = new ConsoleIo();
        $this->command->execute($args, $io);
        $this->assertEquals('true', env('DEBUG'));
        $this->assertEquals('true' ,env('USE_CORE_API'));
        $this->assertEquals('true' ,env('USE_CORE_ADMIN_API'));
    }
}
