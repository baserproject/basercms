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
use BcMcp\Mcp\McpServer;
use BcMcp\Test\TestSuite\McpTestTrait;

/**
 * McpServerTest
 */
class McpServerTest extends BcTestCase
{

    use McpTestTrait;

    /**
     * tools/list を実行する
     *
     * @return array
     */
    private function listTools(): array
    {
        return $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
            'params' => ['_meta' => $this->modernMeta()],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'tools/list',
        ]);
    }

    /**
     * test tools/list に全プラグインのツールが並ぶ
     */
    public function testToolsListContainsAllTools()
    {
        $response = $this->listTools();

        $this->assertArrayNotHasKey('error', $response, json_encode($response, JSON_UNESCAPED_UNICODE));
        $names = array_column($response['result']['tools'], 'name');

        // BcBlog
        $this->assertContains('addBlogPost', $names);
        $this->assertContains('getBlogContents', $names);
        $this->assertContains('addBlogCategory', $names);
        $this->assertContains('addBlogTag', $names);
        // BcCustomContent
        $this->assertContains('addCustomTable', $names);
        $this->assertContains('addCustomContent', $names);
        $this->assertContains('addCustomField', $names);
        $this->assertContains('addCustomEntry', $names);
        $this->assertContains('addCustomLink', $names);
        // BaserCore（固定ページ）
        $this->assertContains('getPages', $names);
        $this->assertContains('getPage', $names);
        $this->assertContains('addPage', $names);
        $this->assertContains('editPage', $names);
        $this->assertContains('deletePage', $names);
        // BaserCore
        $this->assertContains('serverInfo', $names);
    }

    /**
     * test tools/list の結果にキャッシュヒントが付与される
     *
     * 2026-07-28 では ttlMs / cacheScope が必須項目であり、SDK が付与する
     */
    public function testToolsListHasCacheHints()
    {
        $response = $this->listTools();

        $this->assertArrayHasKey('ttlMs', $response['result']);
        $this->assertArrayHasKey('cacheScope', $response['result']);
    }

    /**
     * test 全 result に resultType が付与される
     */
    public function testResultTypeIsComplete()
    {
        $response = $this->listTools();

        $this->assertEquals('complete', $response['result']['resultType']);
    }

    /**
     * test loginUserId が inputSchema に公開されていない
     *
     * 公開すると AI クライアントが他ユーザーの ID を指定できてしまう
     */
    public function testLoginUserIdIsNotExposed()
    {
        $response = $this->listTools();

        foreach($response['result']['tools'] as $tool) {
            $properties = $tool['inputSchema']['properties'] ?? [];
            $this->assertArrayNotHasKey(
                'loginUserId',
                $properties,
                "ツール {$tool['name']} の inputSchema に loginUserId が公開されています"
            );
        }
    }

    /**
     * test serverInfo が提供するトランスポートは HTTP のみ
     *
     * 認証と権限を通らない stdio 経路は提供しない
     */
    public function testServerInfoReportsHttpOnly()
    {
        $result = (new McpServer())->serverInfo();

        $this->assertEquals(['http'], $result['available_transports']);
    }

}
