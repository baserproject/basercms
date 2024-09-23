<?php

namespace BaserCore\Test\TestCase\Command;

use BaserCore\Command\UpdateCommand;
use BaserCore\TestSuite\BcTestCase;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;

class UpdateCommandTest extends BcTestCase
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
        $this->UpdateCommand = new UpdateCommand();
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
     * test buildOptionParser
     */
    public function testBuildOptionParser()
    {
        $parser = $this->getMockBuilder(ConsoleOptionParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parser->expects($this->once())
            ->method('addOption')
            ->with('connection', [
                'help' => 'データベース接続名',
                'default' => 'default'
            ]);

        $rs = $this->execPrivateMethod($this->UpdateCommand, 'buildOptionParser', [$parser]);

        $this->assertInstanceOf(ConsoleOptionParser::class, $rs);

    }

    /**
     * test execute
     */
    public function testExecute()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
