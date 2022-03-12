<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;

use ArrayObject;
use Cake\Validation\Validator;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PagesTable Test
 *
 * @package Baser.Test.Case.Model
 * @property Page $Page
 */
class PagesTableTest extends BcTestCase
{

    public $fixtures = [
        // 'baser.Model.Content.ContentStatusCheck',
        // 'plugin.BaserCore.BlogContents',
        // 'plugin.BaserCore.BlogCategorys',
        // 'plugin.BaserCore.BlogPosts',
        // 'plugin.BaserCore.BlogPostsBlogTags',
        // 'plugin.BaserCore.BlogTags',
        // 'plugin.BaserCore.SearchIndexs',
        'plugin.BaserCore.SiteConfigs',
        // 'baser.Model.Page.PagePageModel',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.SearchIndexes',
        // 'plugin.BaserCore.Favorites'
    ];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Pages') ? [] : ['className' => 'BaserCore\Model\Table\PagesTable'];
        $this->Pages = $this->getTableLocator()->get('Pages', $config);
        $this->SearchIndexes = $this->getTableLocator()->get('SearchIndexes');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Pages, $this->SearchIndexes);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertTrue($this->Pages->hasBehavior('BcContents'));
        $this->assertTrue($this->Pages->hasBehavior('BcSearchIndexManager'));
        $this->assertTrue($this->Pages->hasBehavior('Timestamp'));
    }

    /**
     * testValidationDefault
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $validator = $this->Pages->validationDefault(new Validator());
        $fields = [];
        foreach($validator->getIterator() as $key => $value) {
            $fields[] = $key;
        }
        $this->assertEquals(['id','contents', 'draft'], $fields);
    }

    public function test既存ページチェック正常()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Pages->create([
            'Page' => [
                'name' => 'test',
                'page_category_id' => '1',
            ]
        ]);
        $this->assertTrue($this->Pages->validates());
    }

    /**
     * スクリプトがが埋め込まれているかチェックする
     * - 管理グループの場合は無条件に true を返却
     * - 管理グループ以外の場合に許可されている場合は無条件に true を返却
     * @param array $check
     * @param bool $expected
     * @dataProvider cotainsScriptRegularDataProvider
     */
    public function testCotainsScriptRegular($check, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $allowedPhpOtherThanAdmins = Configure::read('BcApp.allowedPhpOtherThanAdmins');
        Configure::write('BcApp.allowedPhpOtherThanAdmins', false);
        $this->Pages->create([
            'Page' => [
                'name' => 'test',
                'contents' => $check,
            ]
        ]);
        $this->assertEquals($expected, $this->Pages->validates());
        Configure::write('BcApp.allowedPhpOtherThanAdmins', $allowedPhpOtherThanAdmins);
    }

    public function cotainsScriptRegularDataProvider()
    {
        return [
            ['<?php echo "正しい"; ?>', false],
            ['<?PHP echo "正しい"; ?>', false],
            ['<script></script>', false],
            ['<a onclick="alert(\'test\')>"', false],
            ['<img onMouseOver="">', false],
            ['<a href="javascript:alert(\'test\')">', false],
            ['<a href=\'javascript:alert("test")\'>', false],
            ['<a href="https://basercms.net">baserCMS<\/a>', true]
        ];
    }

    public function testCotainsScriptIrregular()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Pages->create([
            'Page' => [
                'name' => 'test',
                'contents' => '<?php ??>',
            ]
        ]);
        $this->assertFalse($this->Pages->validates());
        $this->assertArrayHasKey('contents', $this->Pages->validationErrors);
        $this->assertEquals("PHPの構文エラーです： \nPHP Parse error:  syntax error, unexpected '?' in - on line 1 \nErrors parsing -", current($this->Pages->validationErrors['contents']));
    }

    /**
     * 最終登録IDを取得する
     */
    public function testGetInsertID()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Pages->save([
            'Page' => [
                'name' => 'hoge',
                'title' => 'hoge',
                'url' => '/hoge',
                'description' => 'hoge',
                'status' => 1,
                'page_category_id' => null,
            ]
        ]);
        $result = $this->Pages->getInsertID();
        $this->assertEquals(16, $result, '正しく最終登録IDを取得できません');
    }

    /**
     * ページテンプレートファイルが開けるかチェックする
     *
     * @param array $name ページ名
     * @param array $parentId 親コンテンツID
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider checkOpenPageFileDataProvider
     */
    public function testCheckOpenPageFile($name, $parentId, $expected, $message = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $data = [
            'Content' => [
                'name' => $name,
                'parent_id' => $parentId,
                'site_id' => 1
            ]
        ];
        $result = $this->Pages->checkOpenPageFile($data);
        $this->assertEquals($expected, $result, $message);
    }

    public function checkOpenPageFileDataProvider()
    {
        return [
            ['index', null, false, 'ページテンプレートファイルが開けるか正しくチェックできません'],
            ['company', 1, true, 'ページテンプレートファイルが開けるか正しくチェックできません'],
            ['index', 1, true, 'ページテンプレートファイルが開けるか正しくチェックできません'],
            ['index', 2, true, 'ページテンプレートファイルが開けるか正しくチェックできません'],
            ['hoge', null, false, 'ページテンプレートファイルが開けるか正しくチェックできません'],
            [null, 99, false, 'ページテンプレートファイルが開けるか正しくチェックできません'],
            ['index', 99, false, 'ページテンプレートファイルが開けるか正しくチェックできません'],
            ['hoge', 99, false, 'ページテンプレートファイルが開けるか正しくチェックできません'],
        ];
    }

    /**
     * afterSave
     *
     * @param boolean $created
     * @param array $options
     * @return boolean
     * @dataProvider afterSaveDataProvider
     */
    public function testAfterSave($exclude_search, $exist)
    {
        $page = $this->Pages->find()->contain(['Contents' => ['Sites']])->first();
        if ($exclude_search) {

            $page->content->exclude_search = $exclude_search;
            $id = $page->id;
            $this->assertEquals(false, $this->SearchIndexes->findByModelId($id)->isEmpty());
        } else {
            $id = $page->id = 100; // 存在しない新規のIDを入れた場合
        }
        $this->Pages->dispatchEvent('Model.afterSave', [$page, new ArrayObject()]);
        $this->assertEquals($exist, $this->SearchIndexes->findByModelId($id)->isEmpty());
    }
    public function afterSaveDataProvider()
    {
        return [
            // exclude_searchがある場合削除されているかを確認
            [1, true],
            // exclude_searchがなく、なおかつ新規の場合索引が作成されて存在するかをテスト
            [0, false],
        ];
    }

    /**
     * 関連ページに反映する
     *
     * @param string $type
     * @param array $data
     * @return boolean
     */
    public function testRefrect()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


    /**
     * 検索用データを生成する
     */
    public function testCreateSearchIndex()
    {
        $page = $this->Pages->find()->contain(['Contents' => ['Sites']])->first();
        $expected = [
            'model_id' => $page->id,
            'type' => 'ページ',
            'content_id' => $page->content->id,
            'title' => $page->content->title,
            'detail' => $page->content->description . ' ' . $page->contents,
            'url' => $page->content->url,
            'status' => $page->content->status,
            'site_id' => $page->content->site_id,
            'publish_begin' => $page->content->publish_begin ?? '',
            'publish_end' => $page->content->publish_end ?? '',
        ];
        $result = $this->Pages->createSearchIndex($page);
        $this->assertEquals($expected, $result, '検索用データを正しく生成できません');
    }


    /**
     * beforeDelete
     *
     * @param $cascade
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider beforeDeleteDataProvider
     */
    public function testBeforeDelete($id, $message = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 削除したファイルを再生するため内容を取得
        $page = $this->Pages->find('first', [
                'conditions' => ['Page.id' => $id],
                'recursive' => 0,
            ]
        );
        $path = APP . 'View' . DS . 'Pages' . $page['Content']['url'] . '.php';
        $File = new File($path);
        $content = $File->read();

        // 削除実行
        $this->Pages->delete($id);

        // 元のファイルを再生成
        $File->write($content);
        $File->close();

        // Contentも削除されているかチェック
        $this->Content = ClassRegistry::init('Content');
        $exists = $this->Content->exists($page['Content']['id']);
        $this->assertFalse($exists, $message);
        unset($this->Content);

    }

    public function beforeDeleteDataProvider()
    {
        return [
            [2, 'PageモデルのbeforeDeleteが機能していません'],
        ];
    }

    /**
     * DBデータを元にページテンプレートを全て生成する
     */
    public function testCreateAllPageTemplate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Pages->createAllPageTemplate();

        // ファイルが生成されているか確認
        $result = true;
        $pages = $this->Pages->find('all', ['conditions' => ['Content.status' => true], 'recursive' => 0]);
        foreach($pages as $page) {
            $path = $this->Pages->getPageFilePath($page);
            if (!file_exists($path)) {
                $result = false;
            }
            // フィクスチャ：Default.PageのPage情報にあわせて独自に追加したファイルを削除
            if ($page['Page']['id'] > 12) {
                @unlink($path);
            }
        }
        $this->assertEquals(true, $result, 'DBデータを元にページテンプレートを全て生成できません');
    }


    /**
     * ページテンプレートを生成する
     *
     * @param array $name ページ名
     * @param array $categoryId ページカテゴリーID
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider createPageTemplateDataProvider
     */
    public function testCreatePageTemplate($name, $categoryId, $expected, $message = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $data = [
            'Page' => [
                'contents' => '',
            ],
            'Content' => [
                'name' => $name,
                'parent_id' => $categoryId,
                'site_id' => 0,
                'title' => ''
            ]
        ];
        $path = $this->Pages->getPageFilePath($data);

        // ファイル生成
        $this->Pages->createPageTemplate($data);

        // trueなら生成されている
        $result = file_exists($path);

        // 生成されているファイル削除
        @unlink($path);

        $this->assertEquals($expected, $result, $message);
    }

    public function createPageTemplateDataProvider()
    {
        return [
            ['hoge.php', null, false, 'ページテンプレートを生成できません'],
            ['hoge.php', 1, true, 'ページテンプレートを生成できません'],
            ['hoge.php', 2, true, 'ページテンプレートを生成できません'],
        ];
    }

    /**
     * ページファイルのパスを取得する
     */
    public function testGetPageFilePath()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ページファイルを削除する
     *
     * @param array $name ページ名
     * @param array $categoryId ページカテゴリーID
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider delFileDataProvider
     */
    public function testDelFile($name, $categoryId, $expected, $message = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $data = [
            'Page' => [
                'contents' => '',
            ],
            'Content' => [
                'name' => $name,
                'parent_id' => $categoryId,
                'site_id' => 0,
                'title' => ''
            ]
        ];

        $path = $this->Pages->getPageFilePath($data);

        $File = new File($path);

        // 存在するファイルパスか
        if ($File->exists()) {

            // 削除したファイルを再生成するため内容取得
            $tmp_content = $File->read();

            // ファイル削除
            $this->Pages->delFile($data);

            // trueなら削除済み
            $result = !file_exists($path);

            // 削除したファイルを再生成
            $File->write($tmp_content);

        } else {
            $result = $this->Pages->delFile($data);

        }

        $File->close();

        $this->assertEquals($expected, $result, $message);
    }

    public function delFileDataProvider()
    {
        return [
            ['index', null, true, 'ページファイルを削除できません'],
            ['index', 1, true, 'ページファイルを削除できません'],
            ['index', 2, true, 'ページファイルを削除できません'],
        ];
    }


    /**
     * コントロールソースを取得する
     *
     * MEMO: $optionのテストについては、UserTest でテスト済み
     *
     * @param string $field フィールド名
     * @param array $options
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getControlSourceDataProvider
     */
    public function testGetControlSource($field, $expected, $message = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->Pages->getControlSource($field);
        $this->assertEquals($expected, $result, $message);
    }

    public function getControlSourceDataProvider()
    {
        return [
            ['author_id', [1 => 'basertest', 2 => 'basertest2'], 'コントロールソースを取得できません'],
        ];
    }

    /**
     * ページファイルを登録する
     * ※ 再帰処理
     *
     * @param string $targetPath
     * @param string $parentCategoryId
     * @return array 処理結果 all / success
     */
    public function testEntryPageFiles()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 固定ページとして管理されているURLかチェックする
     *
     * @param string $url URL
     * @param bool $expects true Or false
     * @return void
     * @dataProvider isPageUrlDataProvider
     */
    public function testIsPageUrl($url, $expects)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->Pages->isPageUrl($url);
        $this->assertEquals($result, $expects);
    }

    public function isPageUrlDataProvider()
    {
        return [
            ['/service', true],
            ['/service.html', true],
            ['/servce.css', false],
            ['/', true],
            ['/hoge', false]
        ];
    }


    /**
     * delete
     *
     * @param mixed $id ID of record to delete
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider deleteDataProvider
     */
    public function testDelete($id, $expected, $message = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 削除したファイルを再生するため内容を取得
        $Page = $this->Pages->find('first', [
                'conditions' => ['Page.id' => $id],
                'fields' => ['Content.url'],
                'recursive' => 0
            ]
        );
        $path = APP . 'View' . DS . 'Pages' . $Page['Content']['url'] . '.php';
        $File = new File($path);
        $Content = $File->read();

        // 削除実行
        $this->Pages->delete($id);
        $this->assertFileNotExists($path, $message);

        // 元のファイルを再生成
        $File->write($Content);
        $File->close();

        // 削除できているか確認用にデータ取得
        $result = $this->Pages->exists($id);
        $this->assertEquals($expected, $result, $message);
    }

    public function deleteDataProvider()
    {
        return [
            [1, false, 'ページデータを削除できません'],
        ];
    }

    /**
     * PHP構文チェック
     * 成功時
     *
     * @param string $code PHPのコード
     * @return void
     * @dataProvider phpValidSyntaxDataProvider
     */
    public function testPhpValidSyntax($code)
    {
        $this->assertTrue($this->Pages->phpValidSyntax($code));
    }

    public function phpValidSyntaxDataProvider()
    {
        return [
            [''],
            ['<?php $this->BcBaser->setTitle(\'test\');'],
        ];
    }

    /**
     * PHP構文チェック
     * 失敗時
     *
     * @param string $line エラーが起こる行
     * @param string $code PHPコード
     * @return void
     * @dataProvider phpValidSyntaxWithInvalidDataProvider
     */
    public function testPhpValidSyntaxWithInvalid($line, $code)
    {
        $this->assertStringContainsString("on line {$line}", $this->Pages->phpValidSyntax($code));
    }

    public function phpValidSyntaxWithInvalidDataProvider()
    {
        return [
            [1, '<?php echo \'test'],
            [2, '<?php echo \'test\';' . PHP_EOL . 'echo \'hoge']
        ];
    }

    public function testGetParentPageTemplate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * URLからページを取得する
     *
     * @param string $url
     * @param string $publish
     * @param bool $expected
     * @dataProvider findByUrlDataProvider
     */
    public function testFindByUrl($url, $publish, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->loadFixtures('ContentStatusCheck');
        $result = (bool)$this->Pages->findByUrl($url, $publish);
        $this->assertEquals($expected, $result);
    }

    public function findByUrlDataProvider()
    {
        return [
            ['/about', true, true],
            ['/service', true, false],
            ['/service', false, true],
            ['/hoge', false, false],
        ];
    }

    /**
     * コンテンツフォルダのパスを取得する
     *
     * @param $id
     * @param $expects
     * @dataProvider getContentFolderPathDataProvider
     */
    public function testGetContentFolderPath($id, $expects)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->assertEquals($expects, $this->Pages->getContentFolderPath($id));
    }

    public function getContentFolderPathDataProvider()
    {
        return [
            [1, APP . 'View/Pages/'],
            [2, APP . 'View/Pages/mobile/'],
            [3, APP . 'View/Pages/smartphone/'],
            [4, false],
        ];
    }

}
