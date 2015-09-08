<?php
/**
 * BcContentsManagerBehaviorのテスト
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
class BcContentsManagerTest extends BaserTestCase {

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
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Page = ClassRegistry::init('Page');
		$this->Content = ClassRegistry::init('Content');
		$this->SiteConfig = ClassRegistry::init('SiteConfig');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Page);
		unset($this->Content);
		unset($this->SiteConfig);
		parent::tearDown();
	}

/**
 * コンテンツデータを登録する
 * 
 * @param Model $model
 * @param array $data
 * @return boolean
 * @access public
 */
	public function testSaveContent() {

		// コンテンツデータを登録
		$data = array(
			'Content' => array(
				'model_id' => 5,
				'title' => 'Mobile-index',
				'detail' => "Mobile\r\n-Index",
				'url' => '/mobile/index',
				'category' => 'mobile',
				'status' => 1,
				'type' => 'モバイル',
			),
		);
		$this->Page->saveContent($data);

		// 結果とexpectedを照合
		$result = $this->Content->find('first', array(
				'conditions' => array('model_id' => 5),
			)
		);
		$result['Content']['created'] = null;
		$result['Content']['modified'] = null;

		$expected = array(
			'Content' => array(
				'model_id' => '5',
				'title' => 'Mobile-index',
				'detail' => 'Mobile-Index',
				'url' => '/mobile/index',
				'category' => 'mobile',
				'status' => true,
				'id' => '7',
				'type' => 'モバイル',
				'model' => 'Page',
				'priority' => '0.5',
				'created' => null,
				'modified' => null
			),
		);
		$this->assertEquals($expected, $result, 'コンテンツデータを正しく登録できません');


		// ————————————————————————————————————————————————————————————————
		// updateContentMeta()のテスト, content_categoriesが更新されているか確認
		// ————————————————————————————————————————————————————————————————
		
		$result = $this->SiteConfig->find('all');

		// content_categories の更新チェック
		$content_categories = $result[16]['SiteConfig']['value'];
		$expected = 'YToxOntzOjY6Im1vYmlsZSI7czo2OiJtb2JpbGUiO30='; // serialize(array('mobile' => 'mobile'))
		$this->assertEquals($expected, $content_categories, 'content_categories が正しく更新されていません');

		// content_types の更新チェック
		$content_types = $result[17]['SiteConfig']['value'];
		$expected = 'YTo0OntzOjk6IuODluODreOCsCI7czo5OiLjg5bjg63jgrAiO3M6OToi44Oa44O844K4IjtzOjk6IuODmuODvOOCuCI7czo5OiLjg6Hjg7zjg6siO3M6OToi44Oh44O844OrIjtzOjEyOiLjg6Ljg5DjgqTjg6siO3M6MTI6IuODouODkOOCpOODqyI7fQ=='; // serialize(types)
		$this->assertEquals($expected, $content_types, 'content_types が正しく更新されていません');

	}

/**
 * コンテンツデータを削除する
 * 
 * @param Model $model
 * @param string $url 
 */
	public function testDeleteContent() {
		$this->Page->deleteContent(2);
		$exists = $this->Content->exists(1);
		$this->assertFalse($exists, 'コンテンツデータを削除できません');
	}


/**
 * コンテンツメタ情報を更新する
 *
 * testSaveContent()で、動作確認のテスト済み
 *
 * @param string $contentCategory
 * @return boolean
 * @access public
 */
	public function testUpdateContentMeta() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
