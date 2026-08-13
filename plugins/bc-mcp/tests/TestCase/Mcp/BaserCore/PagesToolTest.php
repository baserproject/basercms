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

namespace BcMcp\Test\TestCase\Mcp\BaserCore;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\RootContentScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\BaserCore\PagesTool;
use BcMcp\Mcp\McpContext;
use BcMcp\Test\TestSuite\McpTestTrait;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PagesToolTest
 */
class PagesToolTest extends BcTestCase
{

    use ScenarioAwareTrait;
    use McpTestTrait;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        // 固定ページの保存は afterSave で検索インデックスなど複数のテーブルへ
        // 書き込むため、前のテストのデータが残ると一意制約に衝突する。
        // シナリオ読み込みの前に明示的に空にする。
        foreach([
            'sites', 'users', 'user_groups', 'users_user_groups',
            'contents', 'content_folders', 'pages', 'search_indexes', 'dblogs',
        ] as $table) {
            $this->truncateTable($table);
        }
        $this->loadFixtureScenario(InitAppScenario::class);
        // 親フォルダを省略した場合の配置先となるサイトルートを用意する
        $this->loadFixtureScenario(RootContentScenario::class, 1, 1, null, null, '/');
        // ファクトリで作成したノードは lft / rght が整合しないため、
        // TreeBehavior が辿れるようツリーを再構築する
        TableRegistry::getTableLocator()->get('BaserCore.Contents')->recover();
        // 検索インデックスの生成（BcSearchIndex の afterSave）が
        // 現在のサイト情報を参照するため、ログイン状態にしておく
        $this->loginAdmin($this->getRequest('/'));
        McpContext::setLoginUserId(1);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        McpContext::clear();
        parent::tearDown();
    }

    /**
     * test addPage で固定ページが登録できる
     *
     * 固定ページは pages テーブルと contents テーブルの複合構造であり、
     * 本文は pages.contents、タイトルや URL はコンテンツ情報に保存される
     */
    public function testAddPage()
    {
        [$result, $isError] = $this->callMcpTool('addPage', [
            'title' => '会社概要',
            'name' => 'about',
            'content' => '<p>会社概要のページです。</p>',
            'status' => 1,
        ]);

        $this->assertFalse($isError, 'ツールの実行に失敗しました。' . (is_string($result)? $result : json_encode($result, JSON_UNESCAPED_UNICODE)));
        $this->assertArrayHasKey('id', $result, json_encode($result, JSON_UNESCAPED_UNICODE));
        // 本文は pages.contents に保存される
        $this->assertEquals('<p>会社概要のページです。</p>', $result['contents']);
        // タイトルと URL はコンテンツ情報に保存される
        $this->assertEquals('会社概要', $result['content']['title']);
        $this->assertEquals('about', $result['content']['name']);
        // plugin と type はツール側で補われる
        $this->assertEquals('BaserCore', $result['content']['plugin']);
        $this->assertEquals('Page', $result['content']['type']);
        $this->assertTrue((bool)$result['content']['self_status']);
    }

    /**
     * test editPage で固定ページが編集できる
     */
    public function testEditPage()
    {
        [$added] = $this->callMcpTool('addPage', [
            'title' => '編集前',
            'name' => 'before-edit',
            'content' => '<p>編集前の本文</p>',
        ]);

        [$result, $isError] = $this->callMcpTool('editPage', [
            'id' => $added['id'],
            'title' => '編集後',
            'content' => '<p>編集後の本文</p>',
        ]);

        $this->assertFalse($isError, 'ツールの実行に失敗しました。' . (is_string($result)? $result : json_encode($result, JSON_UNESCAPED_UNICODE)));
        $this->assertEquals('編集後', $result['content']['title']);
        $this->assertEquals('<p>編集後の本文</p>', $result['contents']);
        // 指定しなかった項目は変更されない
        $this->assertEquals('before-edit', $result['content']['name']);
    }

    /**
     * test getPages と getPage で固定ページを取得できる
     */
    public function testGetPages()
    {
        [$added] = $this->callMcpTool('addPage', [
            'title' => '取得テスト',
            'name' => 'get-test',
            'content' => '<p>取得テストの本文</p>',
        ]);

        [$list, $listError] = $this->callMcpTool('getPages', ['limit' => 10]);
        $this->assertFalse($listError, is_string($list)? $list : json_encode($list, JSON_UNESCAPED_UNICODE));
        $this->assertNotEmpty($list);

        [$single, $singleError] = $this->callMcpTool('getPage', ['id' => $added['id']]);
        $this->assertFalse($singleError, is_string($single)? $single : json_encode($single, JSON_UNESCAPED_UNICODE));
        $this->assertEquals('取得テスト', $single['content']['title']);
        $this->assertEquals('<p>取得テストの本文</p>', $single['contents']);
    }

    /**
     * test getPages はキーワードで本文を検索できる
     */
    public function testGetPagesWithKeyword()
    {
        $this->callMcpTool('addPage', [
            'title' => 'キーワード対象',
            'name' => 'keyword-target',
            'content' => '<p>特別な検索語を含む本文</p>',
        ]);
        $this->callMcpTool('addPage', [
            'title' => 'キーワード対象外',
            'name' => 'keyword-other',
            'content' => '<p>関係のない本文</p>',
        ]);

        [$list, $isError] = $this->callMcpTool('getPages', ['keyword' => '特別な検索語']);

        $this->assertFalse($isError, is_string($list)? $list : json_encode($list, JSON_UNESCAPED_UNICODE));
        $this->assertCount(1, $list);
        $this->assertEquals('キーワード対象', $list[0]['content']['title']);
    }

    /**
     * test deletePage で固定ページが削除できる
     *
     * PagesService::delete() は完全削除であり、pages と contents の
     * レコードがいずれも消える（ゴミ箱にも残らない）
     */
    public function testDeletePage()
    {
        [$added] = $this->callMcpTool('addPage', [
            'title' => '削除対象',
            'name' => 'to-be-deleted',
            'content' => '<p>削除対象の本文</p>',
        ]);

        [$result, $isError] = $this->callMcpTool('deletePage', ['id' => $added['id']]);
        $this->assertFalse($isError, is_string($result)? $result : json_encode($result, JSON_UNESCAPED_UNICODE));
        $this->assertEquals('削除対象', $result['title']);

        // pages のレコードが消えている
        $this->assertEquals(0, TableRegistry::getTableLocator()->get('BaserCore.Pages')
            ->find()->where(['Pages.id' => $added['id']])->count());
        // 紐づく contents のレコードも消えている（ゴミ箱にも残らない）
        $this->assertEquals(0, TableRegistry::getTableLocator()->get('BaserCore.Contents')
            ->find()
            ->where(['Contents.entity_id' => $added['id'], 'Contents.type' => 'Page'])
            ->applyOptions(['withDeleted'])
            ->count());
        // 削除済みのため取得できない。
        // BaseMcpTool::executeWithErrorHandling() が例外を戻り値へ包むため、
        // MCP レベルの isError にはならず content にエラーメッセージが入る
        [$notFound] = $this->callMcpTool('getPage', ['id' => $added['id']]);
        $this->assertStringContainsString(
            'Record not found',
            $notFound['content'] ?? '',
            '削除したページが取得できてしまいました。' . json_encode($notFound, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * test 権限チェック用のURL
     */
    public function testGetPermissionUrl()
    {
        $this->assertEquals(
            ['POST' => '/baser-core/pages/add.json'],
            PagesTool::getPermissionUrl('addPage')
        );
        $this->assertEquals(
            ['POST' => '/baser-core/pages/edit/3.json'],
            PagesTool::getPermissionUrl('editPage', ['id' => 3])
        );
        $this->assertEquals(
            ['POST' => '/baser-core/pages/delete/3.json'],
            PagesTool::getPermissionUrl('deletePage', ['id' => 3])
        );
        $this->assertEquals(
            ['GET' => '/baser-core/pages/index.json'],
            PagesTool::getPermissionUrl('getPages')
        );
        $this->assertEquals(
            ['GET' => '/baser-core/pages/view/3.json'],
            PagesTool::getPermissionUrl('getPage', ['id' => 3])
        );
        // id が無い編集・削除・取得は権限チェックの対象にできない
        $this->assertFalse(PagesTool::getPermissionUrl('editPage'));
        $this->assertFalse(PagesTool::getPermissionUrl('unknownAction'));
    }

}
