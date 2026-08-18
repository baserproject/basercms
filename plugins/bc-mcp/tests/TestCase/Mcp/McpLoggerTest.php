<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\McpLogger;

/**
 * McpLoggerTest
 */
class McpLoggerTest extends BcTestCase
{

    /**
     * ログファイルのパス
     * @var string
     */
    private string $logFile;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logFile = TMP . 'bc_mcp_logger_test.log';
        if (file_exists($this->logFile)) unlink($this->logFile);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        if (file_exists($this->logFile)) unlink($this->logFile);
        parent::tearDown();
    }

    /**
     * test log
     *
     * 例外のトレースまで記録される事を確認する
     */
    public function testLog()
    {
        $logger = new McpLogger($this->logFile);
        $logger->error('Tool execution failed.', [
            'tool' => 'addBlogPost',
            'exception' => new \Exception('Call to a member function getParam() on null')
        ]);

        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('Tool execution failed.', $log);
        $this->assertStringContainsString('(tool: addBlogPost)', $log);
        $this->assertStringContainsString('Call to a member function getParam() on null', $log);
        // トレースが記録されている事を確認
        $this->assertStringContainsString('#0 ', $log);
    }

    /**
     * test log with unrecorded level
     *
     * 記録対象外のログレベルは記録されない事を確認する
     */
    public function testLogWithUnrecordedLevel()
    {
        $logger = new McpLogger($this->logFile);
        $logger->debug('デバッグメッセージ');
        $this->assertFalse(file_exists($this->logFile));
    }

}
