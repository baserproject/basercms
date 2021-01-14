<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Controller.Component
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcCaptchaComponent', 'Controller/Component');
App::uses('Controller', 'Controller');

/**
 * 偽コントローラ
 *
 * @package Baser.Test.Case.Controller.Component
 */
class BcCaptchaTestController extends Controller
{

	public $components = ['BcCaptcha', 'Session'];

}

/**
 * BcCaptchaComponentのテスト
 */
class BcCaptchaComponentTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.BlogCategory',
		'baser.Default.BlogContent',
		'baser.Default.BlogComment',
		'baser.Default.BlogTag',
		'baser.Default.SearchIndex',
		'baser.Default.FeedDetail',
		'baser.Default.SiteConfig',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.Page',
		'baser.Default.Permission',
		'baser.Default.Plugin',
		'baser.Default.User',
	];

	public $components = ['BcCaptcha'];

	public function setUp()
	{
		parent::setUp();

		// コンポーネントと偽のテストコントローラをセットアップする
		$request = new CakeRequest();
		$response = $this->getMock('CakeResponse');
		$this->Controller = new BcCaptchaTestController($request, $response);

		$collection = new ComponentCollection();
		$collection->init($this->Controller);
		$this->BcCaptcha = new BcCaptchaComponent($collection);
		$this->BcCaptcha->request = $request;
		$this->BcCaptcha->response = $response;

		$this->Controller->Components->init($this->Controller);

		Router::reload();
		Router::connect('/:controller/:action/*');
	}

	public function tearDown()
	{
		session_unset();
		parent::tearDown();
		unset($this->Controller);
		unset($this->BcCaptcha);
	}

	/**
	 * キャプチャ画象を表示する
	 *
	 * @return void
	 */
	public function testRender()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 認証を行う
	 */
	public function testCheck()
	{

		// 初期化
		$this->BcCaptcha->startup($this->Controller);

		// 正常系
		$this->Controller->Session->write('captcha.0', '3KbC');
		$result = $this->BcCaptcha->check('えがしら');
		$this->assertTrue($result, 'キャプチャの認証が正しくありません');

		// 異常系
		$this->Controller->Session->write('captcha.0', '3KbC');
		$result = $this->BcCaptcha->check('あいうえお');
		$this->assertFalse($result, 'キャプチャの認証が正しくありません');

	}

	/**
	 * kcaptchaで定義されたアルファベットを $convert に定義された任意の文字列に変換する
	 */
	public function testConvert()
	{

		// 正常系
		$this->BcCaptcha->alphabet = 'wonderful';
		$this->BcCaptcha->convert = 'SrCbesMa';

		$result = $this->BcCaptcha->convert('dureonfw');
		$this->assertEquals('baserCMS', $result, 'kcaptchaで定義されたアルファベットを $convert に定義された任意の文字列に変換する処理が正しくありません');

		// 異常系
		$this->BcCaptcha->alphabet = 'hoge';
		$this->BcCaptcha->convert = 'SrCbesMa';

		$result = $this->BcCaptcha->convert('dureonfw');
		$this->assertEquals(false, $result, 'kcaptchaで定義されたアルファベットを $convert に定義された任意の文字列に変換する処理が正しくありません');

	}

	/**
	 * 文字列を１文字づつ分割して配列にする
	 */
	public function testStrSplit()
	{

		$result = $this->BcCaptcha->strSplit('aiueo');
		$expected = ['a', 'i', 'u', 'e', 'o'];
		$this->assertEquals($expected, $result, '文字列を１文字づつ分割して配列にする処理が正しくありません');

	}

}
