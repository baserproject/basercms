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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcMcp\Mcp\McpServer;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use PhpMcp\Schema\Request\CallToolRequest;
use PhpMcp\Server\Dispatcher;
use PhpMcp\Server\Session\SubscriptionManager;

/**
 * McpServerToolCallTest
 *
 * MCPサーバーを別プロセスで起動する事なく、JSON-RPC の tools/call と同じ経路
 * （スキーマ検証 → 引数マッピング → ツール実行）をプロセス内で実行するテスト
 */
class McpServerToolCallTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $server = (new McpServer())->getServer();
        $configuration = $server->getConfiguration();
        $this->dispatcher = new Dispatcher(
            $configuration,
            $server->getRegistry(),
            new SubscriptionManager($configuration->logger)
        );
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->dispatcher);
        parent::tearDown();
    }

    /**
     * tools/call を実行する
     *
     * @param string $name ツール名
     * @param array $arguments 引数
     * @return array [デコード済みの戻り値, エラーかどうか]
     */
    private function callTool(string $name, array $arguments): array
    {
        $result = $this->dispatcher->handleToolCall(
            new CallToolRequest('test-' . $name, $name, $arguments)
        );
        $text = $result->content[0]->text ?? '';
        return [json_decode($text, true) ?? $text, $result->isError];
    }

    /**
     * test tools/call addBlogPost
     *
     * 本番環境にて `Call to a member function getParam() on null` が発生した
     * リクエストと同じ引数で、ブログ記事が登録できる事を確認する
     */
    public function testCallToolAddBlogPost()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(BlogContentScenario::class,
            1, // id
            1, // siteId
            null, // parentId
            'news', // name
            '/news/' // url
        );

        [$result, $isError] = $this->callTool('addBlogPost', [
            'title' => 'BcMcpについて',
            'name' => 'about-bcmcp',
            'status' => 0,
            'content' => '<p>BcMcpは、baserCMSを外部のAIエージェントから直接操作できるようにするMCP（Model Context Protocol）サーバーです。ブログ記事やカテゴリ、タグの管理はもちろん、カスタムテーブル・カスタムコンテンツ・カスタムエントリー・カスタムリンクといったbaserCMSの柔軟な拡張機能まで、AIアシスタント経由で読み書きできます。</p>',
            'detail' => $this->getDetail(),
            'loginUserId' => 1,
        ]);

        // ツール実行時に例外が発生していない事を確認
        $this->assertFalse($isError, 'ツールの実行に失敗しました。' . (is_string($result)? $result : json_encode($result, JSON_UNESCAPED_UNICODE)));
        // ブログ記事が登録されている事を確認
        $this->assertArrayHasKey('id', $result, 'ブログ記事の登録に失敗しました。' . json_encode($result, JSON_UNESCAPED_UNICODE));
        $this->assertEquals('BcMcpについて', $result['title']);
        $this->assertEquals('about-bcmcp', $result['name']);
        $this->assertEquals(1, $result['blog_content_id']);
        $this->assertEquals(1, $result['user_id']);
        $this->assertFalse($result['status']);
    }

    /**
     * 本番環境で送信された記事詳細を取得する
     *
     * @return string
     */
    private function getDetail(): string
    {
        return <<<EOF
<h2>BcMcpとは</h2>
<p>BcMcpは、baserCMSをAIエージェントから直接操作するためのMCP（Model Context Protocol）サーバーです。MCPは、AIアシスタントと外部システムを標準化された方法でつなぐプロトコルであり、BcMcpはこの仕組みを使ってbaserCMSのAPIをAIエージェント向けに公開しています。</p>
<p>これにより、ChatやAIエージェントとの対話の中で「ブログ記事を書いて」「このカテゴリを追加して」といった指示を出すだけで、baserCMSサイトの更新が完結するようになります。</p>

<h2>BcMcpでできること</h2>
<p>BcMcpは、baserCMSが持つ主要な機能をひととおりカバーしています。</p>
<ul>
<li><strong>ブログ管理</strong>：ブログコンテンツの作成・取得、記事の追加・編集・削除、カテゴリの管理、タグの管理</li>
<li><strong>カスタムコンテンツ管理</strong>：カスタムテーブルと紐づくカスタムコンテンツの作成・編集・削除</li>
<li><strong>カスタムエントリー管理</strong>：カスタムテーブルに登録されたデータ（エントリー）の一覧取得・追加・編集・削除</li>
<li><strong>カスタムフィールド管理</strong>：カスタムエントリーの入力項目定義の作成・編集・削除</li>
<li><strong>カスタムテーブル管理</strong>：カスタムフィールドを組み合わせたテーブル自体の作成・編集・削除</li>
<li><strong>カスタムリンク管理</strong>：サイト内の任意のリンク項目の作成・編集・削除</li>
</ul>
<p>つまり、記事の投稿だけでなく、baserCMSの汎用データベース機能（カスタムテーブル）を使った独自コンテンツの管理まで、AIエージェント経由でひととおり行えるようになっています。</p>

<h2>なぜBcMcpを作ったのか</h2>
<p>baserCMSは2010年の誕生以来、オープンソースのCMSとして進化を続けてきました。近年のAIエージェントの普及を受けて、baserCMSを「人が管理画面を操作するCMS」から一歩進めて、「AIエージェントが自律的に運用できるCMS（Agentic CMS）」として位置づけ直す取り組みの一環がBcMcpです。</p>
<p>管理画面にログインして手作業で更新する代わりに、AIエージェントに指示を出すだけでサイト更新が完結する。BcMcpはそのための土台となるインターフェースです。</p>

<h2>活用イメージ</h2>
<p>ClaudeのようなAIアシスタントにBcMcpを接続すると、たとえば次のようなことが会話ベースで行えるようになります。</p>
<ul>
<li>新しいブログ記事の下書きを作ってもらい、そのまま下書き状態でサイトに登録する</li>
<li>既存記事の内容を要約・修正してもらい、そのまま更新する</li>
<li>お知らせやFAQなど、カスタムテーブルで管理しているデータをまとめて追加・更新する</li>
<li>サイト内の各種リンク項目を整理・更新する</li>
</ul>
<p>これにより、コンテンツ更新のたびに管理画面を開いて手作業を行う必要がなくなり、AIとの対話の延長でサイト運用が進むようになります。</p>

<h2>まとめ</h2>
<p>BcMcpは、baserCMSをAIエージェントから直接操作できるようにするMCPサーバーです。ブログ管理からカスタムテーブルを使った独自コンテンツ管理まで幅広くカバーしており、baserCMSをAIネイティブに運用していくための重要なピースとなっています。</p>
EOF;
    }

}
