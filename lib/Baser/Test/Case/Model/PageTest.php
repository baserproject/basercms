<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Model
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */
App::uses('Page', 'Model');

/**
 * Class PageTest
 *
 * @package Baser.Test.Case.Model
 * @property Page $Page
 */
class PageTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Model.Content.ContentStatusCheck',
		'baser.Default.BlogContent',
		'baser.Default.BlogCategory',
		'baser.Default.BlogPost',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogTag',
		'baser.Default.SearchIndex',
		'baser.Default.SiteConfig',
		'baser.Model.Page.PagePageModel',
		'baser.Default.Permission',
		'baser.Default.Plugin',
		'baser.Default.User',
		'baser.Default.Site',
		'baser.Default.Content',
		'baser.Default.ContentFolder',
		'baser.Default.UserGroup',
		'baser.Default.Favorite'
	];

	/**
	 * Page
	 *
	 * @var Page
	 */
	public $Page = null;

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		$this->Page = ClassRegistry::init('Page');
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown()
	{
		unset($this->Page);
		parent::tearDown();
	}

	public function test既存ページチェック正常()
	{
		$this->Page->create([
			'Page' => [
				'name' => 'test',
				'page_category_id' => '1',
			]
		]);
		$this->assertTrue($this->Page->validates());
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
		$allowedPhpOtherThanAdmins = Configure::read('BcApp.allowedPhpOtherThanAdmins');
		Configure::write('BcApp.allowedPhpOtherThanAdmins', false);
		$this->Page->create([
			'Page' => [
				'name' => 'test',
				'contents' => $check,
			]
		]);
		$this->assertEquals($expected, $this->Page->validates());
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
		$this->Page->create([
			'Page' => [
				'name' => 'test',
				'contents' => '<?php ??>',
			]
		]);
		$this->assertFalse($this->Page->validates());
		$this->assertArrayHasKey('contents', $this->Page->validationErrors);
		$this->assertEquals("PHPの構文エラーです： \nPHP Parse error:  syntax error, unexpected '?' in - on line 1 \nErrors parsing -", current($this->Page->validationErrors['contents']));
	}

	/**
	 * beforeSave
	 *
	 * @param array $options
	 * @return boolean
	 */
	public function testBeforeSave()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 最終登録IDを取得する
	 */
	public function testGetInsertID()
	{
		$this->Page->save([
			'Page' => [
				'name' => 'hoge',
				'title' => 'hoge',
				'url' => '/hoge',
				'description' => 'hoge',
				'status' => 1,
				'page_category_id' => null,
			]
		]);
		$result = $this->Page->getInsertID();
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
		$data = [
			'Content' => [
				'name' => $name,
				'parent_id' => $parentId,
				'site_id' => 0
			]
		];
		$result = $this->Page->checkOpenPageFile($data);
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
	 */
	public function testAfterSave()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

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
	 *
	 * @param int $name ページID
	 * @param string $name ページ名
	 * @param id $categoryId ページカテゴリーID
	 * @param string $title ページタイトル
	 * @param string $url ページURL
	 * @param string $description ページ概要
	 * @param date $publish_begin 公開開始日時
	 * @param date $publish_end 公開終了日時
	 * @param date $detail 期待するページdescription
	 * @param int $status 公開状態
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider createContentDataProvider
	 */
	public function testCreateSearchIndex($id, $name, $categoryId, $title, $url, $description, $publish_begin, $publish_end, $status, $message = null)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$data = [
			'Page' => [
				'id' => $id,
				'name' => $name,
				'page_category_id' => $categoryId,
				'title' => $title,
				'url' => $url,
				'description' => $description,
				'publish_begin' => $publish_begin,
				'publish_end' => $publish_end,
				'status' => $status,
			]
		];

		$expected = ['Content' => [
			'model_id' => $id,
			'type' => 'ページ',
			'category' => '',
			'title' => $title,
			'detail' => '',
			'url' => $url,
			'status' => $status,
		]
		];
		$result = $this->Page->createContent($data);
		$this->assertEquals($expected, $result, $message);
	}


	public function createContentDataProvider()
	{
		return [
			[1, 'index', null, 'index', '/index', '', null, null, true, '検索用データを正しく生成できません'],
			[1, 'index', null, 'タイトル', '/index', '', null, null, true, '検索用データを正しく生成できません'],
		];
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
		// 削除したファイルを再生するため内容を取得
		$page = $this->Page->find('first', [
				'conditions' => ['Page.id' => $id],
				'recursive' => 0,
			]
		);
		$path = APP . 'View' . DS . 'Pages' . $page['Content']['url'] . '.php';
		$File = new File($path);
		$content = $File->read();

		// 削除実行
		$this->Page->delete($id);

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
		$this->Page->createAllPageTemplate();

		// ファイルが生成されているか確認
		$result = true;
		$pages = $this->Page->find('all', ['conditions' => ['Content.status' => true], 'recursive' => 0]);
		foreach($pages as $page) {
			$path = $this->Page->getPageFilePath($page);
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
		$path = $this->Page->getPageFilePath($data);

		// ファイル生成
		$this->Page->createPageTemplate($data);

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

		$path = $this->Page->getPageFilePath($data);

		$File = new File($path);

		// 存在するファイルパスか
		if ($File->exists()) {

			// 削除したファイルを再生成するため内容取得
			$tmp_content = $File->read();

			// ファイル削除
			$this->Page->delFile($data);

			// trueなら削除済み
			$result = !file_exists($path);

			// 削除したファイルを再生成
			$File->write($tmp_content);

		} else {
			$result = $this->Page->delFile($data);

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
	 * 本文にbaserが管理するタグを追加する
	 *
	 * @param string $id ID
	 * @param string $contents 本文
	 * @param string $title タイトル
	 * @param string $description 説明文
	 * @param string $code コード
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider addBaserPageTagDataProvider
	 */
	public function testAddBaserPageTag($id, $contents, $title, $description, $code, $expected, $message = null)
	{
		$result = $this->Page->addBaserPageTag($id, $contents, $title, $description, $code);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function addBaserPageTagDataProvider()
	{
		return [
			[1, 'contentdayo', 'titledayo', 'descriptiondayo', 'codedayo',
				"<!-- BaserPageTagBegin -->.*setTitle\('titledayo'\).*setDescription\('descriptiondayo'\).*setPageEditLink\(1\).*codedayo.*contentdayo",
				'本文にbaserが管理するタグを追加できません'],
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
		$result = $this->Page->getControlSource($field);
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
		$result = $this->Page->isPageUrl($url);
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

		// 削除したファイルを再生するため内容を取得
		$Page = $this->Page->find('first', [
				'conditions' => ['Page.id' => $id],
				'fields' => ['Content.url'],
				'recursive' => 0
			]
		);
		$path = APP . 'View' . DS . 'Pages' . $Page['Content']['url'] . '.php';
		$File = new File($path);
		$Content = $File->read();

		// 削除実行
		$this->Page->delete($id);
		$this->assertFileNotExists($path, $message);

		// 元のファイルを再生成
		$File->write($Content);
		$File->close();

		// 削除できているか確認用にデータ取得
		$result = $this->Page->exists($id);
		$this->assertEquals($expected, $result, $message);
	}

	public function deleteDataProvider()
	{
		return [
			[1, false, 'ページデータを削除できません'],
		];
	}

	/**
	 * ページデータをコピーする
	 *
	 * @param int $id ページID
	 * @param int $newParentId 新しい親コンテンツID
	 * @param string $newTitle 新しいタイトル
	 * @param int $newAuthorId 新しい作成者ID
	 * @param int $newSiteId 新しいサイトID
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider copyDataProvider
	 */
	public function testCopy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId, $message = null)
	{
		$this->_loginAdmin();
		$result = $this->Page->copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId);

		// コピーしたファイル存在チェック
		$path = APP . 'View' . DS . 'Pages' . $result['Content']['url'] . '.php';
		$this->assertFileExists($path, $message);
		@unlink($path);

		// DBに書き込まれているかチェック
		$exists = $this->Page->exists($result['Page']['id']);
		$this->assertTrue($exists);
	}

	public function copyDataProvider()
	{
		return [
			[1, 1, 'hoge1', 1, 0, 'ページデータをコピーできません'],
			[3, 1, 'hoge', 1, 0, 'ページデータをコピーできません']
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
		$this->assertTrue($this->Page->phpValidSyntax(['contents' => $code]));
	}

	public function phpValidSyntaxDataProvider()
	{
		return [
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
		$this->assertContains("on line {$line}", $this->Page->phpValidSyntax(['contents' => $code]));
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
	 * 固定ページテンプレートリストを取得する
	 *
	 * @param int $contetnId
	 * @param mixed $theme
	 * @param $expected
	 * @dataProvider getPageTemplateListDataProvider
	 */
	public function testGetPageTemplateList($contetnId, $theme, $expected)
	{
		$templates = BASER_THEMES . 'bc_sample' . DS . 'Pages' . DS . 'templates' . DS . 'hoge.php';
		touch($templates);
		$result = $this->Page->getPageTemplateList($contetnId, $theme);
		$this->assertEquals($expected, $result);
		unlink($templates);
	}

	public function getPageTemplateListDataProvider()
	{
		return [
			[1, 'nada-icons', ['default' => 'default']],
			[2, 'nada-icons', ['' => '親フォルダの設定に従う（default）']],
			[2, ['nada-icons', 'bc_sample'], ['' => '親フォルダの設定に従う（default）', 'hoge' => 'hoge']]
		];
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
		$this->loadFixtures('ContentStatusCheck');
		$result = (bool)$this->Page->findByUrl($url, $publish);
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
		BcSite::flash();
		$this->assertEquals($expects, $this->Page->getContentFolderPath($id));
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
