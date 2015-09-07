<?php
/**
 * ページモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */
App::uses('Page', 'Model');

/**
 * PageTest class
 * 
 * @package Baser.Test.Case.Model
 */
class PageTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.BlogContent',
		'baser.Default.BlogCategory',
		'baser.Default.BlogPost',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogTag',
		'baser.Default.Content',
		'baser.Default.SiteConfig',
		'baser.Model.PageModel',
		'baser.Model.PageCategoryModel',
		'baser.Default.Permission',
		'baser.Default.Plugin',
		'baser.Default.PluginContent',
		'baser.Default.User',
	);

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
	public function setUp() {
		parent::setUp();
		$this->Page = ClassRegistry::init('Page');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Page);
		parent::tearDown();
	}

/**
 * _getPageFilePath を呼び出す
 * 
 * 次のテストで使います
 * testCreateAllPageTemplate()
 * testCreatePageTemplate()
 * testDelFile()
 * 
 * @param array $data ページデータ
 * - $data['Page']['name'], $data['Page']['page_category_id'] が必要
 * @return string
 */
	public function getPageFilePath($data) {

		// リフレクションで _getPageFilePath を呼び出す
		$reflec = new ReflectionMethod($this->Page, '_getPageFilePath');
		$reflec->setAccessible(true);
		$path = $reflec->invoke(new $this->Page(), $data);

		return $path;

	}

/**
 * validate
 */
	public function test必須チェック() {
		$this->Page->create(array(
			'Page' => array(
				'name' => '',
			)
		));
		$this->assertFalse($this->Page->validates());
		$this->assertArrayHasKey('name', $this->Page->validationErrors);
		$this->assertEquals('ページ名を入力してください。', current($this->Page->validationErrors['name']));
	}

	public function test桁数チェック正常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => '12345678901234567890123456789012345678901234567890',
				'title' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'description' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
			)
		));
		$this->assertTrue($this->Page->validates());
	}

	public function test桁数チェック異常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => '123456789012345678901234567890123456789012345678901',
				'title' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'description' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
			)
		));
		$this->assertFalse($this->Page->validates());
		$this->assertArrayHasKey('name', $this->Page->validationErrors);
		$this->assertEquals('ページ名は50文字以内で入力してください。', current($this->Page->validationErrors['name']));
		$this->assertArrayHasKey('title', $this->Page->validationErrors);
		$this->assertEquals('ページタイトルは255文字以内で入力してください。', current($this->Page->validationErrors['title']));
		$this->assertArrayHasKey('description', $this->Page->validationErrors);
		$this->assertEquals('説明文は255文字以内で入力してください。', current($this->Page->validationErrors['description']));
	}

	public function test既存ページチェック正常() {
		$this->Page->create(array(
			'Page' => array(
				'name' => 'test',
				'page_category_id' => '1',
			)
		));
		$this->assertTrue($this->Page->validates());
	}

	public function test既存ページチェック異常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => 'index',
				'page_category_id' => '1',
			)
		));
		$this->assertFalse($this->Page->validates());
		$this->assertArrayHasKey('name', $this->Page->validationErrors);
		$this->assertEquals('指定したページは既に存在します。ファイル名、またはカテゴリを変更してください。', current($this->Page->validationErrors['name']));
	}

	public function testPHP構文チェック正常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => 'test',
				'contents' => '<?php echo "正しい"; ?>',
			)
		));
		$this->assertTrue($this->Page->validates());
	}

	public function testPHP構文チェック異常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => 'test',
				'contents' => '<?php ??>',
			)
		));
		$this->assertFalse($this->Page->validates());
		$this->assertArrayHasKey('contents', $this->Page->validationErrors);
		$this->assertEquals("PHPの構文エラーです： \nPHP Parse error:  syntax error, unexpected '?' in - on line 1 \nErrors parsing -", current($this->Page->validationErrors['contents']));
	}


/**
 * フォームの初期値を設定する
 * 
 * @return	array	初期値データ
 */
	public function testGetDefaultValue() {

		$expected = array('Page' => array(
				'author_id' => 1,
				'sort' => 17,
				'status' => false,
			)
		);
		$result = $this->Page->getDefaultValue();
		$this->assertEquals($expected, $result, 'フォームの初期値を設定するデータが正しくありません');
	
		//$_SESSION['Auth']['User']が存在する場合
		$_SESSION['Auth']['User'] = array(
			'id' => 2,
		);
		$expected = array('Page' => array(
				'author_id' => 2,
				'sort' => 17,
				'status' => false,
			)
		);
		$result = $this->Page->getDefaultValue();
		$this->assertEquals($expected, $result, 'フォームの初期値を設定するデータが正しくありません');

	}

/**
 * beforeSave
 *
 * @param array $options
 * @return boolean
 */
	public function testBeforeSave() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


/**
 * URLよりモバイルやスマートフォン等のプレフィックスを取り除く
 * 
 * @param string $url 変換対象のURL
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider removeAgentPrefixFromUrlDataProvider
 */
	public function testRemoveAgentPrefixFromUrl($url, $expected, $message = null) {
		$result = $this->Page->removeAgentPrefixFromUrl($url);
		$this->assertEquals($expected, $result, $message);
	}

	public function removeAgentPrefixFromUrlDataProvider() {
		return array(
			array('index', 'index', 'URLよりモバイルやスマートフォン等のプレフィックスを取り除くことができません'),
			array('/mobile/index', '/index', 'URLよりモバイルやスマートフォン等のプレフィックスを取り除くことができません'),
			array('/smartphone/index', '/index', 'URLよりモバイルやスマートフォン等のプレフィックスを取り除くことができません'),
			array('/m/index', '/m/index', 'URLよりモバイルやスマートフォン等のプレフィックスを取り除くことができません'),
			array('/company/index', '/company/index', 'URLよりモバイルやスマートフォン等のプレフィックスを取り除くことができません'),
		);
	}

/**
 * 最終登録IDを取得する
 */
	public function testGetInsertID() {
		$this->Page->save(array(
			'Page' => array(
				'name' => 'hoge',
				'title' => 'hoge',
				'url' => '/hoge',
				'description' => 'hoge',
				'status' => 1,
				'page_category_id' => null,
			)
		));
		$result = $this->Page->getInsertID();
		$this->assertEquals(16, $result, '正しく最終登録IDを取得できません');
	}

/**
 * ページテンプレートファイルが開けるかチェックする
 * 
 * @param array $name ページ名
 * @param array $categoryId ページカテゴリーID
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider checkOpenPageFileDataProvider
 */
	public function testCheckOpenPageFile($name, $categoryId, $expected, $message = null) {
		$data = array(
			'Page' => array(
				'name' => $name,
				'page_category_id' => $categoryId,
			)
		);
		$result = $this->Page->checkOpenPageFile($data);
		$this->assertEquals($expected, $result, $message);
	}

	public function checkOpenPageFileDataProvider() {
		return array(
			array('index', null, true, 'ページテンプレートファイルが開けるか正しくチェックできません'),
			array('company', null, true, 'ページテンプレートファイルが開けるか正しくチェックできません'),
			array('index', 1, true, 'ページテンプレートファイルが開けるか正しくチェックできません'),
			array('index', 2, true, 'ページテンプレートファイルが開けるか正しくチェックできません'),
			array('hoge', null, true, 'ページテンプレートファイルが開けるか正しくチェックできません'),
			array(null, 99, true, 'ページテンプレートファイルが開けるか正しくチェックできません'),
			array('index', 99, true, 'ページテンプレートファイルが開けるか正しくチェックできません'),
			array('hoge', 99, true, 'ページテンプレートファイルが開けるか正しくチェックできません'),
		);
	}

/**
 * afterSave
 * 
 * @param boolean $created
 * @param array $options
 * @return boolean
 */
	public function testAfterSave() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

/**
 * 関連ページに反映する
 * 
 * @param string $type
 * @param array $data
 * @return boolean
 */
	public function testRefrect() {
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
	public function testCreateContent($id, $name, $categoryId, $title, $url, $description, $publish_begin, $publish_end, $status, $message = null) {
		$data = array(
			'Page' => array(
				'id' => $id,
				'name' => $name,
				'page_category_id' => $categoryId,
				'title' => $title,
				'url' => $url,
				'description' => $description,
				'publish_begin' => $publish_begin,
				'publish_end' => $publish_end,
				'status' => $status,
			)
		);

		$expected = array('Content' => array(
				'model_id' => $id,
				'type' => 'ページ',
				'category' => '',
				'title' => $title,
				'detail' => ' ',
// 				'detail' => ' 

// <section class="mainHeadline">
// <h2>シングルページデザインで<br />
// <span class="fcGreen">見やすくカッコいい</span>WEBサイトへ！</h2>
// </section>
// <!-- /mainHeadline -->

// <div class="mainWidth" id="information">
// <section class="news1">
// <h2>NEWS RELEASE</h2>
// 	<ul class="post-list">
// 																		<li class="clearfix post-1 first">
// 				<span class="date">2015.08.10</span><br />
// 				<span class="title"><a href="/news/archives/2">新商品を販売を開始しました。</a></span>
// 			</li>
// 																		<li class="clearfix post-2 last">
// 				<span class="date">2015.08.10</span><br />
// 				<span class="title"><a href="/news/archives/1">ホームページをオープンしました</a></span>
// 			</li>
// 			</ul>
// 	</section>

// <section class="news2">
// <h2>BaserCMS NEWS</h2>
// <script type="text/javascript" src="/feed/ajax/1.js"></script></section>
// </div><!-- /information -->',
				'url' => $url,
				'status' => $status,
			)
		);
		$result = $this->Page->createContent($data);
		$this->assertEquals($expected, $result, $message);
	}


	public function createContentDataProvider() {
		return array(
			array(1, 'index', null, 'index', '/index', '', null, null, true, '検索用データを正しく生成できません'),
			array(1, 'index', null, 'タイトル', '/index', '', null, null, true, '検索用データを正しく生成できません'),
		);
	}


/**
 * beforeDelete
 *
 * @param $cascade
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider beforeDeleteDataProvider
 */
	public function testBeforeDelete($id, $expected, $message = null) {
		
		// 削除したファイルを再生するため内容を取得
		$Page = $this->Page->find('first', array(
			'conditions' => array('Page.id' => $id),
			'fields' => array('Page.url'),
			'recursive' => -1,
			)
		);
		$path = getViewPath() . 'Pages' . $Page['Page']['url'] . '.php';
		$File = new File($path);  
		$Content = $File->read();

		// 削除実行
		$this->Page->delete($id);

		// 元のファイルを再生成
		$File->write($Content);
		$File->close();

		// Contentも削除されているかチェック
		$this->Content = ClassRegistry::init('Content');
		$exists = $this->Content->exists($id - 1);
		$this->assertFalse($exists, $message);
		unset($this->Content);

	}

	public function beforeDeleteDataProvider() {
		return array(
			array(3, 'fasdfd', 'PageモデルのbeforeDeleteが機能していません'),
		);
	}

/**
 * DBデータを元にページテンプレートを全て生成する
 */
	public function testCreateAllPageTemplate() {

		$this->Page->createAllPageTemplate();

		// ファイルが生成されているか確認
		$result = true;
		$pages = $this->Page->find('all', array('recursive' => -1));
		foreach ($pages as $page) {
			$data = array(
				'Page' => array(
					'name' => $page['Page']['name'],
					'page_category_id' => $page['Page']['page_category_id'],
				)
			);
			$path = $this->getPageFilePath($data);

			if (!file_exists($path)) {
				$result = false;
			}

			// デフォルトのPage情報にあわせて独自に追加したファイルを削除
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
	public function testCreatePageTemplate($name, $categoryId, $expected, $message = null) {

		$data = array(
			'Page' => array(
				'name' => $name,
				'page_category_id' => $categoryId,
			)
		);
		$path = $this->getPageFilePath($data);

		// ファイル生成
		$this->Page->createPageTemplate($data);

		// trueなら生成されている
		$result = file_exists($path);
		
		// 生成されているファイル削除
		@unlink($path);

		$this->assertEquals($expected, $result, $message);
	}

	public function createPageTemplateDataProvider() {
		return array(
			array('hoge.php', null, true, 'ページテンプレートを生成できません'),
			array('hoge.php', 1, true, 'ページテンプレートを生成できません'),
			array('hoge.php', 2, true, 'ページテンプレートを生成できません'),
		);
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
	public function testDelFile($name, $categoryId, $expected, $message = null) {

		$data = array(
			'Page' => array(
				'name' => $name,
				'page_category_id' => $categoryId,
			)
		);

		$path = $this->getPageFilePath($data);

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

	public function delFileDataProvider() {
		return array(
			array('index', null, true, 'ページファイルを削除できません'),
			array('index', 1, true, 'ページファイルを削除できません'),
			array('index', 2, true, 'ページファイルを削除できません'),
		);
	}

/**
 * ページのURLを取得する
 * 
 * @param array $name ページ名
 * @param array $categoryId ページカテゴリーID
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getPageUrlDataProvider
 */
	public function testGetPageUrl($name, $categoryId, $expected, $message = null) {
		$data = array(
			'Page' => array(
				'name' => $name,
				'page_category_id' => $categoryId,
			)
		);
		$result = $this->Page->getPageUrl($data);
		$this->assertEquals($expected, $result, $message);
	}

	public function getPageUrlDataProvider() {
		return array(
			array('index', null, '/index', 'ページのURLを取得できません'),
			array('index', 1, '/mobile/index', 'ページのURLを取得できません'),
			array('index', 2, '/smartphone/index', 'ページのURLを取得できません'),
			array('hoge', 2, '/smartphone/hoge', 'ページのURLを取得できません'),
			array('hoge', 3, '/smartphone/garaphone/hoge', 'ページのURLを取得できません'),
			array('hoge', 4, '/smartphone/garaphone/garaphone2/hoge', 'ページのURLを取得できません'),
		);
	}

/**
 * 固定ページのURLを表示用のURLに変換する
 * 
 * 《変換例》
 * /mobile/index → /m/
 * 
 * @param string $url 変換対象のURL
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider convertViewUrlDataProvider
 */
	public function testConvertViewUrl($url, $expected, $message = null) {
		$result = $this->Page->convertViewUrl($url);
		$this->assertEquals($expected, $result, $message);
	}

	public function convertViewUrlDataProvider() {
		return array(
			array('/index', '/', '固定ページのURLを表示用のURLに変換できません'),
			array('/service', '/service', '固定ページのURLを表示用のURLに変換できません'),
			array('/mobile/index', '/m/', '固定ページのURLを表示用のURLに変換できません'),
			array('/smartphone/index', '/s/', '固定ページのURLを表示用のURLに変換できません'),
			array('/smartphone/sitemap', '/s/sitemap', '固定ページのURLを表示用のURLに変換できません'),
		);
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
	public function testAddBaserPageTag($id, $contents, $title, $description, $code, $expected, $message = null) {
		$result = $this->Page->addBaserPageTag($id, $contents, $title, $description, $code);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function addBaserPageTagDataProvider() {
		return array(
			array(1, 'contentdayo', 'titledayo', 'descriptiondayo', 'codedayo',
						"<!-- BaserPageTagBegin -->.*setTitle\('titledayo'\).*setDescription\('descriptiondayo'\).*setPageEditLink\(1\).*codedayo.*contentdayo",
						'本文にbaserが管理するタグを追加できません'),
		);
	}

/**
 * コントロールソースを取得する
 * 
 * MEMO: $optionのテストについては、UserTest, PageCategoryTestでテスト済み
 * 
 * @param string $field フィールド名
 * @param array $options
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getControlSourceDataProvider
 */
	public function testGetControlSource($field, $expected, $message = null) {
		$result = $this->Page->getControlSource($field);
		$this->assertEquals($expected, $result, $message);
	}

	public function getControlSourceDataProvider() {
		return array(
			array('page_category_id', array(5 => 'タブレット'), 'コントロールソースを取得できません'),
			array('author_id', array(1 => 'basertest', 2 => 'basertest2'), 'コントロールソースを取得できません'),
		);
	}

/**
 * キャッシュ時間を取得する
 * 
 * @param string $url
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getCacheTimeDataProvider
 */
	public function testGetCacheTime($url, $expected, $message = null) {
		$result = $this->Page->getCacheTime($url);
		$this->assertEquals($expected, $result, $message);
	}

	public function getCacheTimeDataProvider() {
		return array(
			array('/index', '+5 min', 'キャッシュ時間を取得できません'),
			array('/service', '+5 min', 'キャッシュ時間を取得できません'),
			array('/hidden_status', '+5 min', 'キャッシュ時間を取得できません'),
			array('/company', '+5 min', 'キャッシュ時間を取得できません'),
		);
	}

/**
 * 公開チェックを行う
 * 
 * @param string $url
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider checkPublishDataProvider
 */
	public function testCheckPublish($url, $expected, $message = null) {
		$result = $this->Page->checkPublish($url);
		$this->assertEquals($expected, $result, $message);
	}

	public function checkPublishDataProvider() {
		return array(
			array('/index', true, '公開チェックが正しくありません'),
			array('/service', true, '公開チェックが正しくありません'),
			array('/hidden_status', false, '公開チェックが正しくありません'),
			array('/hidden_publish_end', false, '公開チェックが正しくありません'),
		);
	}

/**
 * 公開済の conditions を取得
 */
	public function testGetConditionAllowPublish() {
		$result = $this->Page->getConditionAllowPublish();
		$now = date('Y-m-d H:i:s');
		$expected = array(
			'Page.status' => true,
			0 => array(
				'or' => array(
					array('Page.publish_begin <=' => $now),
					array('Page.publish_begin' => null),
					array('Page.publish_begin' => '0000-00-00 00:00:00'),
				),
			),
			1 => array(
				'or' => array(
					array('Page.publish_end >=' => $now),
					array('Page.publish_end' => null),
					array('Page.publish_end' => '0000-00-00 00:00:00'),
				),
			),
		);
		$this->assertEquals($expected, $result, '公開済を取得するための conditions を取得できません');
	}



/**
 * ページファイルを登録する
 * ※ 再帰処理
 *
 * @param string $targetPath
 * @param string $parentCategoryId
 * @return array 処理結果 all / success
 */
	public function testEntryPageFiles() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 関連ページの存在チェック
 * 存在する場合は、ページIDを返す
 *
 * @param string $type エージェントタイプ
 * @param array $data ページデータ
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider agentExistsDataProvider
 */
	public function agentExists($type, $data, $expected, $message = null) {
		$result = $this->Page->agentExists($type, $data);
		$this->assertEquals($result, $expects);
	}

	public function agentExistsDataProvider() {
		return array(
			array('/service', true),
			array('/service.html', true),
			array('/servce.css', false),
			array('/', true),
			array('/hoge', false)
		);
	}

/**
 * 固定ページとして管理されているURLかチェックする
 * 
 * @param string $url URL
 * @param bool $expects true Or false
 * @return void
 * @dataProvider isPageUrlDataProvider
 */
	public function testIsPageUrl($url, $expects) {
		$result = $this->Page->isPageUrl($url);
		$this->assertEquals($result, $expects);
	}

	public function isPageUrlDataProvider() {
		return array(
			array('/service', true),
			array('/service.html', true),
			array('/servce.css', false),
			array('/', true),
			array('/hoge', false)
		);
	}


/**
 * delete
 *
 * @param mixed $id ID of record to delete
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider deleteDataProvider
 */
	public function testDelete($id, $expected, $message = null) {

		// 削除したファイルを再生するため内容を取得
		$Page = $this->Page->find('first', array(
			'conditions' => array('Page.id' => $id),
			'fields' => array('Page.url'),
			'recursive' => -1,
			)
		);
		$path = getViewPath() . 'Pages' . $Page['Page']['url'] . '.php';
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

	public function deleteDataProvider() {
		return array(
			array(1, false, 'ページデータを削除できません'),
		);
	}

/**
 * ページデータをコピーする
 * 
 * @param int $id ページID
 * @param array $data コピーしたいデータ
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider copyDataProvider
 */
	public function testCopy($id, $data, $expected, $message = null) {

		$data = array('Page' => $data);

		$result = $this->Page->copy($id, $data);

		// コピーしたファイル存在チェック
		$path = getViewPath() . 'Pages' . $result['Page']['url'] . '.php';
		$this->assertFileExists($path, $message);
		@unlink($path);

		// DBに書き込まれているかチェック
		$exists = $this->Page->exists($result['Page']['id']);
		$this->assertTrue($exists);

	}

	public function copyDataProvider() {
		return array(
			array(1, array(), array(), 'ページデータをコピーできません'),
			array(null,
						array('name' => 'hoge','title' => 'hoge','page_category_id' => null,'description' => 'hoge'),
						array('Page' => array()),
						'ページデータをコピーできません'),
		);
	}

/**
 * 連携チェック
 * 
 * @param string $agentPrefix
 * @param string $url
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider isLinkedDataProvider
 */
	public function testIsLinked($agentPrefix, $url, $expected, $message = null) {

		Configure::write('BcApp', array(
			'mobile' => true,
			'smartphone' => true,
			)
		);

		$result = $this->Page->isLinked($agentPrefix, $url);
		$this->assertEquals($expected, $result, $message);
	}

	public function isLinkedDataProvider() {
		return array(
			array('mobile', '/mobile/index', '0', '連携チェックが正しくありません'),
			array('smartphone', '/smartphone', '0', '連携チェックが正しくありません'),
		);
	}

/**
 * treeList
 * 
 * @param int $categoryId ページカテゴリーID
 * @param string $expectedChildPageCategory 期待するページカテゴリー
 * @param array $expectedPageIds 期待するページID
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider treeListDataProvider
 */
	public function testTreeList($categoryId, $expectedChildPageCategory, $expectedPageIds, $message = null) {
		$result = $this->Page->treeList($categoryId);

		// 子カテゴリを代入
		$resultChildPageCategory = '';
		if (isset($result['pageCategories'][0]['PageCategory']['name'])) {
			$resultChildPageCategory = $result['pageCategories'][0]['PageCategory']['name'];
		}

		// 関連ページのIDを代入
		$resultPageIds = array();
		foreach ($result['pages'] as $key => $value) {
			$resultPageIds[] = $value['Page']['id'];
		}

		$this->assertEquals($expectedChildPageCategory, $resultChildPageCategory, $message);
		$this->assertEquals($expectedPageIds, $resultPageIds, $message);
	}

	public function treeListDataProvider() {
		return array(
			array(1, '', array(5, 11), 'ページカテゴリーに関連したデータを取得できません'),
			array(2, 'garaphone', array(6, 7, 8, 9, 10), 'ページカテゴリーに関連したデータを取得できません'),
			array(3, 'garaphone2', array(12), 'ページカテゴリーに関連したデータを取得できません'),
			array(4, '', array(13), 'ページカテゴリーに関連したデータを取得できません'),
		);
	}

/**
 * PHP構文チェック
 * 成功時
 *
 * @param string $code PHPのコード
 * @return void
 * @dataProvider phpValidSyntaxDataProvider
 */
	public function testPhpValidSyntax($code) {
		$this->assertTrue($this->Page->phpValidSyntax(array('contents' => $code)));
	}

	public function phpValidSyntaxDataProvider() {
		return array(
			array('<?php $this->BcBaser->setTitle(\'test\');'),
		);
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
	public function testPhpValidSyntaxWithInvalid($line, $code) {
		$this->assertContains("on line {$line}", $this->Page->phpValidSyntax(array('contents' => $code)));
	}

	public function phpValidSyntaxWithInvalidDataProvider() {
		return array(
			array(1, '<?php echo \'test'),
			array(2, '<?php echo \'test\';' . PHP_EOL . 'echo \'hoge')
		);
	}

}
