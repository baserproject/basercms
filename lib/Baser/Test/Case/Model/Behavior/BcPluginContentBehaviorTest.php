<?php
/**
 * プラグインコンテンツビヘイビアのテスト
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
class BcPluginContentBehaviorTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.BlogContent',
		'baser.Default.PluginContent',
		'baser.Default.Content',
		'baser.Default.SiteConfig',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BlogContent = ClassRegistry::init('BlogContent');
		$this->PluginContent = ClassRegistry::init('PluginContent');
		$this->BcPluginContentBehavior = ClassRegistry::init('BcPluginContentBehavior');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BlogContent);
		unset($this->PluginContent);
		unset($this->BcPluginContentBehavior);
		parent::tearDown();
	}

/**
 * beforeSave
 *
 * @param string $name 登録するブログコンテンツ名
 * @param string $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider beforeSaveDataProvider
 */
	public function testBeforeSave($name, $expected, $message = null) {
		$result = $this->BlogContent->save(array(
			'BlogContent' => array(
				'name' => $name,
				'exclude_search' => true,
				)
			)
		, false);

		$result = is_array($result);

		// 登録に失敗したかどうか
		$this->assertEquals($expected, $result, $message);

	}

	public function beforeSaveDataProvider() {
		return array(
			array('news', false, 'beforeSaveが重複エラーに対応していません'),
			array('hoge', true, 'beforeSaveが重複エラーに対応していません'),
		);
	}

/**
 * afterSave
 *
 * @param	Model	$model
 * @param Model $created
 * @return	void
 * @access	public
 */
	public function testAfterSave() {
		$result = $this->BlogContent->save(array(
			'BlogContent' => array(
				'name' => 'hoge',
				'exclude_search' => true,
				)
			)
		, false);
		
		// 登録したブログコンテンツIDとプラグインコンテンツのcontent_idが一致するか
		$BlogInsertId = $result['BlogContent']['id'];
		
		$PluginLastInsertId = $this->PluginContent->getLastInsertId();
		$PluginLastInsert = $this->PluginContent->find('first', array(
				'conditions' => array('id' => $PluginLastInsertId),
			)
		);
		$contentId = $PluginLastInsert['PluginContent']['content_id'];

		$this->assertEquals($BlogInsertId, $contentId, '登録されたBlog.idとPluginContent.content_idが一致しません');
	}



/**
 * beforeDelete
 *
 * @param	Model	$model
 * @return	void
 * @access	public
 */
	public function testBeforeDelete() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$this->BlogContent->delete(1);
	}

/**
 * プラグイン名を取得する
 * モデル名から文字列「Content」を除外した「プラグイン名」を取得
 *
 * @param string $modelName モデル名
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getPluginNameDataProvider
 */
	public function testGetPluginName($modelName, $expected, $message = null) {
		$result = $this->BcPluginContentBehavior->getPluginName($modelName);
		$this->assertEquals($expected, $result, $message);
	}

	public function getPluginNameDataProvider() {
		return array(
			array('BlogContent', 'blog', 'プラグイン名を正しく取得できません'),
			array('MailContent', 'mail', 'プラグイン名を正しく取得できません'),
			array('hogeContent', 'hoge', 'プラグイン名を正しく取得できません'),
			array('hoge', 'hoge', 'プラグイン名を正しく取得できません'),
		);
	}




}
