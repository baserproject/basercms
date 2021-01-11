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
App::uses('Plugin', 'Model');

/**
 * Class PluginTest
 *
 * class NonAssosiationPlugin extends Plugin {
 *  public $name = 'Plugin';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 *
 * @package Baser.Test.Case.Model
 */
class PluginTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.Favorite',
		'baser.Default.Page',
		'baser.Default.Plugin',
		'baser.Default.User',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
	];

	public function setUp()
	{
		parent::setUp();
		$this->Plugin = ClassRegistry::init('Plugin');
	}

	public function tearDown()
	{
		unset($this->Plugin);
		parent::tearDown();
	}

	/**
	 * validate
	 */
	public function test必須チェック()
	{
		$this->Plugin->create([
			'Plugin' => [
				'title' => 'baser',
			]
		]);
		$this->assertFalse($this->Plugin->validates());
		$this->assertArrayHasKey('name', $this->Plugin->validationErrors);
		$this->assertEquals('プラグイン名は半角英数字、ハイフン、アンダースコアのみが利用可能です。', current($this->Plugin->validationErrors['name']));
	}

	public function test桁数チェック正常系()
	{
		$this->Plugin->create([
			'Plugin' => [
				'name' => '12345678901234567890123456789012345678901234567890',
				'title' => '12345678901234567890123456789012345678901234567890',
			]
		]);
		$this->assertTrue($this->Plugin->validates());
	}

	public function test桁数チェック異常系()
	{
		$this->Plugin->create([
			'Plugin' => [
				'name' => '123456789012345678901234567890123456789012345678901',
				'title' => '123456789012345678901234567890123456789012345678901',
			]
		]);
		$this->assertFalse($this->Plugin->validates());
		$this->assertArrayHasKey('name', $this->Plugin->validationErrors);
		$this->assertEquals('プラグイン名は50文字以内としてください。', current($this->Plugin->validationErrors['name']));
		$this->assertArrayHasKey('title', $this->Plugin->validationErrors);
		$this->assertEquals('プラグインタイトルは50文字以内とします。', current($this->Plugin->validationErrors['title']));
	}

	public function test半角英数チェック異常系()
	{
		$this->Plugin->create([
			'Plugin' => [
				'name' => '１２３ａｂｃ',
			]
		]);
		$this->assertFalse($this->Plugin->validates());
		$this->assertArrayHasKey('name', $this->Plugin->validationErrors);
		$this->assertEquals('プラグイン名は半角英数字、ハイフン、アンダースコアのみが利用可能です。', current($this->Plugin->validationErrors['name']));
	}

	public function test重複チェック異常系()
	{
		$this->Plugin->create([
			'Plugin' => [
				'name' => 'Blog',
			]
		]);
		$this->assertFalse($this->Plugin->validates());
		$this->assertArrayHasKey('name', $this->Plugin->validationErrors);
		$this->assertEquals('指定のプラグインは既に使用されています。', current($this->Plugin->validationErrors['name']));
	}


	/**
	 * データベースを初期化する
	 * 既存のテーブルは上書きしない
	 *
	 * @param string $dbConfigName データベース設定名
	 * @param string $pluginName プラグイン名
	 * @param bool $loadCsv CSVファイル読込するかどうか
	 * @param string $filterTable テーブル指定
	 * @param string $filterType 更新タイプ指定
	 * @return bool
	 */
	public function testInitDb()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * データベースをプラグインインストール前の状態に戻す
	 *
	 * @param string $plugin プラグイン名
	 * @return bool
	 */
	public function testResetDb()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * データベースの構造を変更する
	 *
	 * @param string $plugin プラグイン名
	 * @param string $dbConfigName データベース設定名
	 * @param string $filterTable テーブル指定
	 * @return bool
	 */
	public function testAlterDb()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


	/**
	 * 指定したフィールドに重複値があるかチェック
	 *
	 * @param string $fieldName チェックするフィールド名
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider hasDuplicateValueDataProvider
	 */
	public function testHasDuplicateValue($fieldName, $expected, $message = null)
	{
		$result = $this->Plugin->hasDuplicateValue($fieldName);
		$this->assertEquals($expected, $result, $message);
	}

	public function hasDuplicateValueDataProvider()
	{
		return [
			['name', false, 'Plugin.nameに重複はありません'],
			['version', true, 'Plugin.versionに重複はあります'],
			['status', true, 'Plugin.statusに重複はあります'],
		];
	}

	/**
	 * 優先順位を連番で振り直す
	 *
	 * @return bool
	 */
	public function testRearrangePriorities()
	{
		$result = $this->Plugin->rearrangePriorities();
		$expected = true;
		$this->assertEquals($expected, $result, '優先順位を連番で振り直すことができません');
	}

	/**
	 * 優先順位を変更する
	 *
	 * @param string|int $id 起点となるプラグインのID
	 * @param string|int $offset 変更する範囲の相対位置
	 * @param array $conditions find条件
	 * @return bool
	 */
	public function testChangePriority()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


	/**
	 * プラグインのディレクトリパスを取得
	 *
	 * @param string $pluginName プラグイン名
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider getDirectoryPathDataProvider
	 */
	public function testGetDirectoryPath($pluginName, $expected, $message = null)
	{
		if (!is_null($expected)) {
			$expected = BASER_PLUGINS . $expected;
		}
		$result = $this->Plugin->getDirectoryPath($pluginName);
		$this->assertEquals($expected, $result, $message);
	}

	public function getDirectoryPathDataProvider()
	{
		return [
			['Blog', 'Blog', 'プラグインのディレクトリパスを取得できません'],
			['Feed', 'Feed', 'プラグインのディレクトリパスを取得できません'],
			['hoge', null, '存在しないプラグインです'],
		];
	}

	/**
	 * プラグイン情報を取得する
	 *
	 * @param array $datas プラグインのデータ配列
	 * @param string $file プラグインファイルのパス
	 * @return array
	 */
	public function testGetPluginInfo()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * プラグイン管理のリンクを指定したユーザーのお気に入りに追加
	 *
	 * @param string $pluginName プラグイン名
	 * @param array $userId ユーザーID
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider addFavoriteAdminLinkDataProvider
	 */
	public function testAddFavoriteAdminLink($pluginName, $userId, $expected, $message = null)
	{
		$user = ['id' => $userId];

		$this->Plugin->addFavoriteAdminLink($pluginName, $user);

		if (!isset($this->Plugin->Favorite)) {
			return;
		}

		// 追加したお気に入りを取得
		$this->Plugin->Favorite->cacheQueries = false;
		$lastId = $this->Plugin->Favorite->getLastInsertID();
		$result = $this->Plugin->Favorite->find('all', [
				'conditions' => ['Favorite.id' => $lastId]
			]
		);

		$result = $result[0]['Favorite']['name'];

		$this->assertEquals($expected, $result, $message);
	}

	public function addFavoriteAdminLinkDataProvider()
	{
		return [
			['Blog', 1, 'ブログ管理', 'プラグイン管理のリンクを指定したユーザーのお気に入りに追加できません'],
			['Blog', 2, 'ブログ管理', 'プラグイン管理のリンクを指定したユーザーのお気に入りに追加できません'],
			['Mail', 1, 'メールフォーム 管理', 'プラグイン管理のリンクを指定したユーザーのお気に入りに追加できません'],
		];
	}
}
