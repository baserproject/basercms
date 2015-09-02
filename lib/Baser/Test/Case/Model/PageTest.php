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
		'baser.Default.Content',
		'baser.Default.SiteConfig',
		'baser.Default.Page',
		// 'baser.Default.PageCategory',
		'baser.Model.PageCategoryModel',
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
	}

/**
 * beforeSave
 *
 * @param array $options
 * @return boolean
 */
	public function beforeSave($options = array()) {

	}


/**
 * URLよりモバイルやスマートフォン等のプレフィックスを取り除く
 * 
 * @param string $url 変換対象のURL
 * @return string $url 変換後のURL
 */
	public function removeAgentPrefixFromUrl($url) {

	}

/**
 * 最終登録IDを取得する
 *
 * @return	int
 */
	public function getInsertID() {
	}


/**
 * ページテンプレートファイルが開けるかチェックする
 * 
 * @param	array	$data	ページデータ
 * @return	boolean
 */
	public function checkOpenPageFile($data) {

	}


/**
 * afterSave
 * 
 * @param boolean $created
 * @param array $options
 * @return boolean
 */
	public function afterSave($created, $options = array()) {

	}

/**
 * 関連ページに反映する
 * 
 * @param string $type
 * @param array $data
 * @return boolean
 */
	public function refrect($type, $data) {

	}


/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 */
	public function createContent($data) {

	}

/**
 * beforeDelete
 *
 * @param $cascade
 * @return boolean
 */
	public function beforeDelete($cascade = true) {
	}

/**
 * DBデータを元にページテンプレートを全て生成する
 * 
 * @return boolean
 */
	public function createAllPageTemplate() {

	}


/**
 * ページテンプレートを生成する
 * 
 * @param array $data ページデータ
 * @return boolean
 */
	public function createPageTemplate($data) {

	}

/**
 * ページファイルのディレクトリを取得する
 * 
 * @param array $data
 * @return string
 */
	protected function _getPageFilePath($data) {

	}

/**
 * ページファイルを削除する
 * 
 * @param array $data 削除対象となるレコードデータ
 * @return boolean
 */
	public function delFile($data) {

	}

/**
 * ページのURLを取得する
 * 
 * @param array $data
 * @return string
 */
	public function getPageUrl($data) {

	}
	
/**
 * 固定ページのURLを表示用のURLに変換する
 * 
 * 《変換例》
 * /mobile/index → /m/
 * 
 * @param string $url 変換対象のURL
 * @return string 表示の用のURL
 */
	public function convertViewUrl($url) {

	}


/**
 * 本文にbaserが管理するタグを追加する
 * 
 * @param string $id ID
 * @param string $contents 本文
 * @param string $title タイトル
 * @param string $description 説明文
 * @param string $code コード
 * @return string 本文の先頭にbaserCMSが管理するタグを付加したデータ
 */
	public function addBaserPageTag($id, $contents, $title, $description, $code) {

	}

/**
 * ページ存在チェック
 *
 * @param string チェック対象文字列
 * @return boolean
 */
	public function pageExists($check) {

	}

/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @param array $options
 * @return mixed $controlSource コントロールソース
 */
	public function getControlSource($field, $options = array()) {

	}

/**
 * キャッシュ時間を取得する
 * 
 * @param string $url
 * @return mixed int or false
 */
	public function getCacheTime($url) {

	}

/**
 * 公開チェックを行う
 * 
 * @param string $url
 * @return boolean
 */
	public function checkPublish($url) {

	}


/**
 * 公開済の conditions を取得
 *
 * @return array
 */
	public function getConditionAllowPublish() {

	}

/**
 * ページファイルを登録する
 * ※ 再帰処理
 *
 * @param string $targetPath
 * @param string $parentCategoryId
 * @return array 処理結果 all / success
 */
	public function entryPageFiles($targetPath, $parentCategoryId = '') {

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
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$result = $this->Page->delete($id);

		// 削除できているか確認用にデータ取得
		$Page = $this->Page->find('all', array(
			'conditions' => array('Page.id' => $id),
			'fields' => array('Page.id'),
			'recursive' => -1,
			)
		);
		var_dump($Page);

		$this->assertEquals($expected, $result, $message);
	}

	public function deleteDataProvider() {
		return array(
			array(1, 'dasdas', 'ページデータをコピーできません'),
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
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$result = $this->Page->copy($id, $data);
		$this->assertEquals($expected, $result, $message);
	}

	public function copyDataProvider() {
		return array(
			array(1, array(), 'dasdas', 'ページデータをコピーできません'),
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
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$result = $this->Page->isLinked($agentPrefix, $url);
		$this->assertEquals($expected, $result, $message);
	}

	public function isLinkedDataProvider() {
		return array(
			array('mobile', '/mobile/index', 'dasdas', '関連するページデータのURLを正しく更新できません'),
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
			array(4, '', array(), 'ページカテゴリーに関連したデータを取得できません'),
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
