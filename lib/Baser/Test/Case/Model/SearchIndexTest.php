<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Model
 * @since           baserCMS v 4.1.6
 * @license         https://basercms.net/license/index.html
 */
App::uses('SearchIndex', 'Model');

/**
 * Class ThemeFileTest
 *
 * @package Baser.Test.Case.Model
 * @property SearchIndex $SearchIndex
 */
class SearchIndexTest extends BaserTestCase
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

	public function setUp()
	{
		parent::setUp();
		$this->SearchIndex = ClassRegistry::init('SearchIndex');
	}

	public function tearDown()
	{
		unset($this->SearchIndex);
		parent::tearDown();
	}

	/**
	 * 検索インデックスを再構築する
	 */
	public function testReconstruct()
	{
		Configure::write('BcAuthPrefix.admin.previewRedirect', '');
		$_SERVER['REQUEST_URI'] = '/';
		$this->_loginAdmin();

		// ===========================================
		// 全ページ再構築
		// ===========================================
		$this->SearchIndex->deleteAll(['1=1']);
		$this->SearchIndex->reconstruct();
		$result = $this->SearchIndex->find('count');
		$this->assertEquals(15, $result);

		// ===========================================
		// 指定ディレクトリ配下再構築
		// ===========================================
		/* @var Page $pageModel */
		/* @var ContentFolder $contentFolderModel */
		$pageModel = ClassRegistry::init('Page');
		$contentFolderModel = ClassRegistry::init('ContentFolder');
		$pageModel->clear();
		$contentFolderModel->clear();
		// ディレクトリを追加
		$contentFolder = $contentFolderModel->save(['Content' => [
			'parent_id' => 1,
			'title' => 'test',
			'site_id' => 0
		], 'ContentFolder' => []]);
		// ディレクトリを公開
		$contentFolder['Content']['self_status'] = true;
		$contentFolderModel->save($contentFolder);
		// ページを追加
		$page = $pageModel->save(['Content' => [
			'parent_id' => $contentFolder['Content']['id'],
			'title' => 'test2',
			'site_id' => 0
		]]);
		// 検索インデックス更新なしでページを公開
		$pageModel->searchIndexSaving = false;
		$page['Content']['self_status'] = true;
		$pageModel->save($page);
		$pageModel->searchIndexSaving = true;
		// 指定フォルダ配下の検索インデックスを再構築
		$this->SearchIndex->reconstruct($contentFolder['Content']['id']);
		// 対象のページが公開になっている事を確認
		/* @var \SearchIndex $searchIndexModel */
		$searchIndexModel = ClassRegistry::init('SearchIndex');
		$searchIndex = $searchIndexModel->find('first', ['conditions' => ['id' => 8]]);
		$this->assertTrue($searchIndex['SearchIndex']['status']);
	}

	/**
	 * 公開状態を取得する
	 *
	 * @dataProvider allowPublishDataProvider
	 */
	public function testAllowPublish($publish_begin, $publish_end, $status, $expected)
	{
		$data['publish_begin'] = $publish_begin;
		$data['publish_end'] = $publish_end;
		$data['status'] = $status;
		$this->assertEquals($this->SearchIndex->allowPublish($data), $expected);
	}

	public function allowPublishDataProvider()
	{
		return [
			['0000-00-00 00:00:00', '0000-00-00 00:00:00', false, false],
			['0000-00-00 00:00:00', '0000-00-00 00:00:00', true, true],
			['0000-00-00 00:00:00', date('Y-m-d H:i:s'), true, false],
			['0000-00-00 00:00:00', date('Y-m-d H:i:s', strtotime("+1 hour")), true, true],
			[date('Y-m-d H:i:s'), '0000-00-00 00:00:00', true, true],
			[date('Y-m-d H:i:s', strtotime("+1 hour")), '0000-00-00 00:00:00', true, false],
			[date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), true, false]
		];
	}

}
