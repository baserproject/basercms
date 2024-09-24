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
        $parser = new ConsoleOptionParser('update_command');
        $resultParser = $this->execPrivateMethod($this->UpdateCommand, 'buildOptionParser', [$parser]);

        $options = $resultParser->options();
        $this->assertArrayHasKey('connection', $options);

        $this->assertStringContainsString('データベース接続名', $options['connection']->help());
        $this->assertEquals('default', $options['connection']->defaultValue());
    }

    /**
     * test execute
     */
    public function testExecute()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
