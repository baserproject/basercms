<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model
 * @since			baserCMS v 4.1.6
 * @license			http://basercms.net/license/index.html
 */
App::uses('SearchIndex', 'Model');

/**
 * ThemeFileTest class
 * 
 * @package Baser.Test.Case.Model
 * @property SearchIndex $SearchIndex
 */
class SearchIndexTest extends BaserTestCase {

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

	public function setUp() {
		parent::setUp();
		$this->SearchIndex = ClassRegistry::init('SearchIndex');
	}

	public function tearDown() {
		unset($this->SearchIndex);
		parent::tearDown();
	}

/**
 * 検索インデックスを再構築する
 */
	public function testReconstruct() {
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

}
