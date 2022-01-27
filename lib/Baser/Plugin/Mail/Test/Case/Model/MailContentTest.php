<?php

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Test.Case.Model
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('MailContent', 'Mail.Model');

/**
 * Class MailContentTest
 *
 * @property MailContent $MailContent
 */
class MailContentTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.SiteConfig',
		'baser.Default.SearchIndex',
		'baser.Default.Site',
		'baser.Default.Content',
		'baser.Default.User',
		'plugin.mail.Default/MailMessage',
		'plugin.mail.Default/MailConfig',
		'plugin.mail.Default/MailContent',
		'plugin.mail.Default/MailField',
	];

	public function setUp()
	{
		$this->MailContent = ClassRegistry::init('Mail.MailContent');
		parent::setUp();
	}

	public function tearDown()
	{
		unset($this->MailContent);
		parent::tearDown();
	}

	/**
	 * validate
	 */
	public function test正常チェック()
	{
		$this->MailContent->create([
			'MailContent' => [
				'name' => '0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789',
				'sender_name' => '01234567890123456789012345678901234567890123456789',
				'subject_user' => '01234567890123456789012345678901234567890123456789',
				'subject_admin' => '01234567890123456789012345678901234567890123456789',
				'layout_template' => '01234567890123456789',
				'form_template' => '01234567890123456789',
				'mail_template' => '01234567890123456789',
				'redirect_url' => 'https://basercms.net/',
				'sender_1' => 'test1@example.co.jp',
				'sender_2' => 'test2@example.co.jp',
				'ssl_on' => ''
			]
		]);

		$this->assertTrue($this->MailContent->validates());
		$this->assertEmpty($this->MailContent->validationErrors);
	}

	public function test空白チェック()
	{
		$this->MailContent->create([
			'MailContent' => [
				'name' => '',
				'title' => '',
				'sender_name' => '',
				'subject_user' => '',
				'subject_admin' => '',
				'layout_template' => '',
				'form_template' => '',
				'mail_template' => '',
				'redirect_url' => '',
				'sender_1' => '',
				'sender_2' => '',
				'ssl_on' => ''
			]
		]);
		$this->assertFalse($this->MailContent->validates());
		$expected = [
			'subject_user' => ['自動返信メール件名[ユーザー宛]を入力してください。'],
			'subject_admin' => ['自動送信メール件名[管理者宛]を入力してください。'],
			'form_template' => ['メールフォームテンプレート名は半角のみで入力してください。'],
			'mail_template' => ['送信メールテンプレートは半角のみで入力してください。']
		];
		$this->assertEquals($expected, $this->MailContent->validationErrors);
	}

	public function test桁数チェック()
	{
		$this->MailContent->create([
			'MailContent' => [
				'sender_name' => '01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789001234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890@example.co.jp',
				'subject_user' => '01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789001234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890@example.co.jp',
				'subject_admin' => '01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789001234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890@example.co.jp',
				'form_template' => '012345678901234567890123456789012345678901234567890',
				'mail_template' => '012345678901234567890123456789012345678901234567890',
				'redirect_url' => 'http://01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789001234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890.co.jp',
				'sender_1' => '01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789001234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890@example.co.jp',
				'sender_2' => '01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789001234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890@example.co.jp',
				'ssl_on' => ''
			]
		]);
		$this->assertFalse($this->MailContent->validates());

		$expected = [
			'sender_name' => ['送信先名は255文字以内で入力してください。'],
			'subject_user' => ['自動返信メール件名[ユーザー宛]は255文字以内で入力してください。'],
			'subject_admin' => ['自動返信メール件名[管理者宛]は255文字以内で入力してください。'],
			'form_template' => ['フォームテンプレート名は20文字以内で入力してください。'],
			'mail_template' => ['メールテンプレート名は20文字以内で入力してください。'],
			'redirect_url' => ['リダイレクトURLは255文字以内で入力してください。'],
		];
		$this->assertEquals($expected, $this->MailContent->validationErrors);
	}

	public function test半角英数チェック()
	{
		$this->MailContent->create([
			'MailContent' => [
				'form_template' => '１２３ａｂｃ',
				'mail_template' => '１２３ａｂｃ',
				'ssl_on' => ''
			]
		]);
		$this->assertFalse($this->MailContent->validates());

		$expected = [
			'form_template' => ['メールフォームテンプレート名は半角のみで入力してください。'],
			'mail_template' => ['送信メールテンプレートは半角のみで入力してください。']
		];
		$this->assertEquals($expected, $this->MailContent->validationErrors);
	}

	public function test形式チェック()
	{
		$this->MailContent->create([
			'MailContent' => [
				'redirect_url' => 'hoge',
				'sender_1' => 'hoge',
				'sender_2' => 'hoge',
				'ssl_on' => ''
			]
		]);
		$this->assertFalse($this->MailContent->validates());

		$expected = [
			'sender_1' => ['送信先メールアドレスの形式が不正です。'],
			'sender_2' => ['送信先メールアドレスの形式が不正です。']
		];
		$this->assertEquals($expected, $this->MailContent->validationErrors);
	}

	public function testSSLチェック正常系()
	{
		$this->MailContent->create([
			'MailContent' => [
				'ssl_on' => ['on'],
			]
		]);
		Configure::write('BcEnv.sslUrl', 'on');
		$this->assertTrue($this->MailContent->validates());
		$this->assertEmpty($this->MailContent->validationErrors);
	}

	public function testSSLチェック異常系()
	{
		$this->MailContent->create([
			'MailContent' => [
				'ssl_on' => ['on'],
			]
		]);
		Configure::write('BcEnv.sslUrl', '');
		$this->assertFalse($this->MailContent->validates());
		$this->assertContains('SSL通信を利用するには、システム設定で、事前にSSL通信用のWebサイトURLを指定してください。', $this->MailContent->validationErrors['ssl_on']);
	}

	/**
	 * SSL用のURLが設定されているかチェックする
	 */
	public function testCheckSslUrl()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 英数チェック
	 */
	public function testAlphaNumeric()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * フォームの初期値を取得する
	 */
	public function testGetDefaultValue()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * afterSave
	 *
	 * @param boolean $exclude_search
	 * @dataProvider afterSaveDataProvider
	 */
	public function testAfterSave($exclude_search)
	{
		// 初期化
		$data = ['MailContent' => [
			'description' => 'hogeDescription',
		],
			'Content' => [
				'name' => 'hogeName',
				'title' => 'hogeTitle',
				'self_status' => true,
				'status' => true,
				'exclude_search' => $exclude_search,
				'parent_id' => 1,
				'site_id' => 0
			]
		];

		// データ保存
		$this->MailContent->save($data);

		// Contentチェック
		$this->SearchIndex = ClassRegistry::init('SearchIndex');
		$result = $this->SearchIndex->find('first', [
			'conditions' => ['title' => 'hogeTitle']
		]);
		if (!$exclude_search) {
			$this->assertNotEmpty($result, 'SearchIndexにデータを追加できません');
		} else {
			$this->assertEmpty($result, 'SearchIndexのデータを削除できません');
		}
	}

	public function afterSaveDataProvider()
	{
		return [
			[false],
			[true],
		];
	}

	/**
	 * beforeDelete
	 */
	public function testBeforeDelete()
	{

		// 削除実行
		$this->MailContent->data = ['MailContent' => ['name' => 'contact']];
		$this->MailContent->delete(1);

		$contents = $this->MailContent->find('all');
		$this->MailField = ClassRegistry::init('MailField');
		$fields = $this->MailField->find('all');

		// Mail関連チェック
		$this->assertEmpty($contents, 'メールコンテンツデータを削除できません');
		$this->assertEmpty($fields, '関連したメールフィールドデータを削除できません');

		// SearchIndexチェック
		$this->SearchIndex = ClassRegistry::init('SearchIndex');
		$result = $this->SearchIndex->find('all', [
			'conditions' => ['type' => 'メール', 'model_id' => 1]
		]);
		$this->assertEmpty($result, '関連したSearchIndexを削除できません');
	}

	/**
	 * 検索用データを生成する
	 *
	 * @param int $id 入力するidの値
	 * @param int $mailContentId MailContentインスタンスに設定するid
	 * @param int $expected idの期待値
	 * @param string $message テスト失敗時に表示するメッセージ
	 * @dataProvider createContentDataProvider
	 */
	public function createSearchIndex($id, $mailContentId, $expected, $message)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		// 初期化
		$data = ['MailContent' => [
			'id' => $id,
			'title' => 'タイトル',
			'description' => '説明',
			'name' => '名前',
			'status' => 'ステータス',
		]];
		$this->MailContent->id = $mailContentId;

		$result = $this->MailContent->createContent($data);
		$expected = [
			'SearchIndex' => [
				'type' => 'メール',
				'model_id' => $expected,
				'category' => '',
				'title' => 'タイトル',
				'detail' => '説明',
				'url' => '/名前/index',
				'status' => 'ステータス'
			]];
		$this->assertEquals($expected, $result, $message);

	}

	/**
	 * 検索用データを生成する
	 */
	public function testCreateSearchIndex()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function createContentDataProvider()
	{
		return [
			[1, 2, 1, '検索用データを正しく生成できません'],
			[null, 11, 11, '検索用データを正しく生成できません'],
		];
	}

	/**
	 * メールコンテンツデータをコピーする
	 *
	 * @param int $id
	 * @param int $newParentId 新しい親コンテンツID
	 * @param string $newTitle 新しいタイトル
	 * @param int $newAuthorId 新しい作成者ID
	 * @param int $newSiteId 新しいサイトID
	 * @param array $expected 期待値
	 * @dataProvider copyDataProvider
	 */
	public function testCopy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId)
	{

		$db = $this->MailContent->getDataSource();
		switch($db->config['datasource']) {
			case 'Database/BcSqlite' :
				$this->markTestIncomplete('このテストは、まだ実装されていません。');
				$command = '.schema';
			default :
		}

		$result = $this->MailContent->copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId);

		if (!is_null($id)) {
			$this->assertRegExp('/hogeName/', $result['Content']['title'], 'メールコンテンツデータをコピーできません');
			// メールフィールドもコピーされているか
			$this->MailField = ClassRegistry::init('MailField');
			$field = $this->MailField->find('first',
				['conditions' => ['id' => 19]
				]);

			$this->assertEquals(2, $field['MailField']['mail_content_id'], 'メールフィールドデータをコピーできません');
		}
	}

	public function copyDataProvider()
	{
		return [
			[1, 1, 'hogeName', 1, 0]
		];
	}

	/**
	 * フォームが公開中かどうかチェックする
	 */
	public function testIsAccepting()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 公開済の conditions を取得
	 */
	public function testGetConditionAllowAccepting()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 公開されたコンテンツを取得する
	 */
	public function testFindAccepting()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
