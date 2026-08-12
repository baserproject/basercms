<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.7
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Command;

use BaserCore\TestSuite\BcTestCase;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use BcMcp\Command\McpServerCommand;

/**
 * BcMcp\Command\McpServerCommand Test Case
 *
 * @uses \BcMcp\Command\McpServerCommand
 */
class McpServerCommandTest extends BcTestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     */
    public function testBuildOptionParser()
    {
        $command = new McpServerCommand();
        $parser = $command->getOptionParser();

        $options = $parser->options();
        $this->assertArrayHasKey('connection', $options);
        $this->assertEquals('default', $options['connection']->defaultValue());

        // HTTP 経由の利用は /bc-mcp エンドポイントが担うため、
        // トランスポートやポートの選択肢は持たない
        $this->assertArrayNotHasKey('transport', $options);
        $this->assertArrayNotHasKey('host', $options);
        $this->assertArrayNotHasKey('port', $options);
    }

    /**
     * Test execute method help
     *
     * @return void
     */
    public function testExecuteHelp()
    {
        $command = new McpServerCommand();
        $parser = $command->getOptionParser();

        $this->assertStringContainsString('baserCMS MCP サーバーを標準入出力で起動します', $parser->getDescription());
    }
}
