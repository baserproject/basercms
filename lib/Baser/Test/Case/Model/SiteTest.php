<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Model
 * @since           baserCMS v 4.0.2
 * @license         https://basercms.net/license/index.html
 */
App::uses('Site', 'Model');

/**
 * Class SiteTest
 *
 * @property Site $Site
 * @package Baser.Test.Case.Model
 */
class SiteTest extends BaserTestCase
{

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Site',
		'baser.Default.ContentFolder',
		'baser.Default.Content',
		'baser.Default.User',
		'baser.Default.SiteConfig'
	];

	/**
	 * Set Up
	 */
	public function setUp()
	{
		parent::setUp();
		$this->Site = ClassRegistry::init('Site');
	}

	/**
	 * Tear Down
	 */
	public function tearDown()
	{
		unset($this->Site);
		parent::tearDown();
	}

	/**
	 * エイリアスのスラッシュをチェックする
	 *
	 * - 連続してスラッシュは入力できない
	 * - 先頭と末尾にスラッシュは入力できない
	 */
	public function testGetAliasSlashChecks()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 公開されている全てのサイトを取得する
	 */
	public function testGetPublishedAll()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * サイトリストを取得
	 *
	 * @param int $mainSiteId メインサイトID
	 * @param array $options
	 * @param array $expects
	 * @param string $message
	 * @dataProvider getSiteListDataProvider
	 */
	public function testGetSiteList($mainSiteId, $options, $expects, $message)
	{
		$result = $this->Site->getSiteList($mainSiteId, $options);
		$this->assertEquals($expects, $result, $message);
	}

	public function getSiteListDataProvider()
	{
		return [
			[null, [], [0 => 'パソコン', 1 => 'ケータイ', 2 => 'スマートフォン'], '全てのサイトリストの取得ができません。'],
			[0, [], [1 => 'ケータイ', 2 => 'スマートフォン'], 'メインサイトの指定ができません。'],
			[1, [], [], 'メインサイトの指定ができません。'],
			[null, ['excludeIds' => [0, 2]], [1 => 'ケータイ'], '除外指定ができません。'],
			[null, ['excludeIds' => 1], [0 => 'パソコン', 2 => 'スマートフォン'], '除外指定ができません。'],
			[null, ['excludeIds' => 0], [1 => 'ケータイ', 2 => 'スマートフォン'], '除外指定ができません。'],
			[null, ['includeIds' => [0, 2]], [0 => 'パソコン', 2 => 'スマートフォン'], 'ID指定ができません。'],
			[null, ['includeIds' => 1], [1 => 'ケータイ'], 'ID指定ができません。'],
		];
	}

	/**
	 * メインサイトのデータを取得する
	 */
	public function testGetRootMain()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コンテンツに関連したコンテンツをサイト情報と一緒に全て取得する
	 */
	public function testGetRelatedContents()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * メインサイトかどうか判定する
	 */
	public function testIsMain()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * サブサイトを取得する
	 */
	public function testChildren()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * After Save
	 */
	public function testAfterSave()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * After Delete
	 */
	public function testAfterDelete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * プレフィックスを取得する
	 */
	public function testGetPrefix()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * サイトのルートとなるコンテンツIDを取得する
	 */
	public function testGetRootContentId()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * URLよりサイトを取得する
	 */
	public function testFindByUrl()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * メインサイトを取得する
	 */
	public function testGetMain()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * After Find
	 */
	public function testAfterFind()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 選択可能なデバイスの一覧を取得する
	 */
	public function testGetSelectableDevices()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 選択可能が言語の一覧を取得する
	 */
	public function testGetSelectableLangs()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * testResetDevice
	 */
	public function testResetDevice()
	{
		$this->Site->resetDevice();
		$sites = $this->Site->find('all', ['recursive' => -1]);
		foreach($sites as $site) {
			$this->assertEquals($site['Site']['device'], '');
			$this->assertFalse($site['Site']['same_main_url']);
			$this->assertFalse($site['Site']['auto_redirect']);
			$this->assertFalse($site['Site']['auto_link']);
		}
	}

	/**
	 * testResetDevice
	 */
	public function testResetLang()
	{
		$this->Site->resetLang();
		$sites = $this->Site->find('all', ['recursive' => -1]);
		foreach($sites as $site) {
			$this->assertEquals($site['Site']['lang'], '');
			$this->assertFalse($site['Site']['same_main_url']);
			$this->assertTrue($site['Site']['auto_redirect']);
		}
	}

	/**
	 * Before Save
	 */
	public function testBeforeSave()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
