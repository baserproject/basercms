<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Model
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('ContentFolder', 'Model');

/**
 * Class ContentFolderTest
 *
 * @package Baser.Test.Case.Model
 * @property ContentFolder $ContentFolder
 */
class ContentFolderTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.ContentFolder',
		'baser.Default.Page',
		'baser.Default.SearchIndex',
		'baser.Default.SiteConfig',
		'baser.Default.BlogPost',
		'baser.Default.BlogCategory',
		'baser.Default.BlogContent',
		'baser.Default.BlogTag',
		'baser.Default.BlogPostsBlogTag',
		'plugin.mail.Default/MailContent',
		'plugin.mail.Default/MailContent',
		'plugin.feed.Default/FeedConfig',
		'baser.Default.FeedDetail',
	];

	/**
	 * set up
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		$this->ContentFolder = ClassRegistry::init('ContentFolder');
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown()
	{
		unset($this->ContentFolder);
		parent::tearDown();
	}

	/**
	 * Implemented Events
	 */
	public function testImplementedEvents()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * Before Move
	 */
	public function testBeforeMove()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * After Move
	 */
	public function testAfterMove()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * Before Save
	 */
	public function testBeforeSave()
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
	 * 保存前のURLをセットする
	 */
	public function testSetBeforeUrl()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 固定ページテンプレートを移動する
	 */
	public function testMovePageTemplates()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * サイトルートフォルダを保存
	 */
	public function testSaveSiteRoot()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * フォルダのテンプレートリストを取得する
	 */
	public function testGetFolderTemplateList()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 親のテンプレートを取得する
	 */
	public function testGetParentTemplate()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 検索インデックスを再構築する
	 */
	public function testReconstructSearchIndices()
	{
		Configure::write('BcAuthPrefix.admin.previewRedirect', '');
		$_SERVER['REQUEST_URI'] = '/';
		$this->_loginAdmin();

		$pageModel = ClassRegistry::init('Page');
		// ディレクトリを追加
		$contentFolder = $this->ContentFolder->save(['Content' => [
			'parent_id' => 1,
			'title' => 'test',
			'site_id' => 0
		]]);
		// ディレクトリを公開
		$contentFolder['Content']['self_status'] = true;
		$this->ContentFolder->save($contentFolder);
		// ページを追加
		$page = $pageModel->save(['Content' => [
			'parent_id' => $contentFolder['Content']['id'],
			'title' => 'test2',
			'site_id' => 0
		]]);
		// ページを公開
		$page['Content']['self_status'] = true;
		$pageModel->save($page);

		// ディレクトリを非公開
		$contentFolder['Content']['self_status'] = false;
		$this->ContentFolder->save($contentFolder, ['reconstructSearchIndices' => true]);
		// 対象のページが非公開になっている事を確認
		/* @var \SearchIndex $searchIndexModel */
		$searchIndexModel = ClassRegistry::init('SearchIndex');
		$searchIndex = $searchIndexModel->find('first', ['conditions' => ['id' => 8]]);
		$this->assertFalse($searchIndex['SearchIndex']['status']);
	}

}
