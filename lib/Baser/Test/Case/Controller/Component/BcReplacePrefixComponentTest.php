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

App::uses('BcReplacePrefixComponent', 'Controller/Component');
App::uses('Controller', 'Controller');

/**
 * Class BcReplacePrefixTestController
 *
 * @package Baser.Test.Case.Controller.Component
 */
class BcReplacePrefixTestController extends Controller
{

	public $components = ['BcReplacePrefix'];

	public $plugin = ['Mail', 'admin'];

}

/**
 * BcReplacePrefixComponentTest
 *
 * @package Baser.Test.Case.Controller.Component
 * @property BcReplacePrefixTestController $Controller
 * @property BcReplacePrefixComponent $BcReplacePrefix
 */
class BcReplacePrefixComponentTest extends BaserTestCase
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

	public $components = ['BcReplacePrefix'];

	public function setUp()
	{
		parent::setUp();

		// コンポーネントと偽のテストコントローラをセットアップする
		$request = new CakeRequest();
		$response = $this->getMock('CakeResponse');
		$this->Controller = new BcReplacePrefixTestController($request, $response);

		$collection = new ComponentCollection();
		$collection->init($this->Controller);
		$this->BcReplacePrefix = new BcReplacePrefixComponent($collection);
		$this->BcReplacePrefix->request = $request;
		$this->BcReplacePrefix->response = $response;

		$this->Controller->Components->init($this->Controller);

		Router::reload();
		Router::connect('/:controller/:action/*');
	}

	public function tearDown()
	{
		parent::tearDown();
		unset($this->Controller);
		unset($this->BcReplacePrefix);
	}

	/**
	 * プレフィックスの置き換えを許可するアクションを設定する
	 *
	 * $this->Replace->allow('action', 'action',...);
	 *
	 * @param string $action
	 * @param string $action
	 * @param string ... etc.
	 * @return void
	 */
	public function testAllow()
	{
		$this->BcReplacePrefix->allowedPureActions = ['a' => 'hoge1', 'b' => 'hoge2'];
		$this->BcReplacePrefix->allow(['a' => 'hoge3', 'c' => 'hoge4']);

		$result = $this->BcReplacePrefix->allowedPureActions;
		$expected = ['a' => 'hoge3', 'b' => 'hoge2', 'c' => 'hoge4'];
		$this->assertEquals($expected, $result, 'プレフィックスの置き換えを許可するアクションを設定が正しくありません');
	}

	/**
	 * startup
	 *
	 * @param string $pre actionのprefix
	 * @param string $action action名
	 * @param string $methods $Controller->methods の値
	 * @param boolean $view viewファイルの作成を行うか
	 * @param array $expected 期待値
	 * @dataProvider startupDataProvider
	 */
	public function testStartup($pre, $action, $methods, $view, $expected)
	{

		Configure::write('BcAuthPrefix', array_merge(Configure::read('BcAuthPrefix'), [
			'pre' => ['alias' => 'pre'],
			'front' => []
		]));

		// 初期化
		$this->Controller->params['prefix'] = $pre;
		$this->Controller->action = $action;
		$this->Controller->methods = [$methods];

		$this->BcReplacePrefix->allowedPureActions = ['action'];

		if ($view) {
			$this->Controller->name = 'Test';
			$FolderPath = ROOT . '/app/webroot/Test' . DS . $pre . DS;
			$filename = 'action.ctp';
			$Folder = new Folder();
			$Folder->create($FolderPath);
			touch($FolderPath . $filename);
		}

		// Initializes
		$this->BcReplacePrefix->initialize($this->Controller);

		// 実行
		$this->BcReplacePrefix->startup($this->Controller);

		if ($view) {
			$Folder->delete(ROOT . '/app/webroot/Test');
		}

		$this->assertEquals($expected[0], $this->Controller->action, 'startupが正しく動作していません');
		$this->assertEquals($expected[1], $this->Controller->layoutPath, 'startupが正しく動作していません');
		$this->assertEquals($expected[2], $this->Controller->subDir, 'startupが正しく動作していません');
	}

	public function startupDataProvider()
	{
		return [
			['pre', 'pre_action', 'admin_action', false, ['admin_action', 'admin', 'admin']],
			[null, 'pre_action', 'admin_action', false, ['pre_action', null, null]],
			['pre', null, 'admin_action', false, [null, null, null]],
			['pre', 'pre_action', null, false, ['pre_action', null, null]],
			['pre', 'pre_action', 'admin_action', true, ['admin_action', 'pre', 'pre']],
			[null, 'action', 'admin_action', true, ['admin_action', null, null]],
			['dummy', 'action', 'admin_action', true, ['action', null, null]],
		];
	}

	/**
	 * beforeRender
	 */
	public function testBeforeRender()
	{
		$this->Controller->request->params['prefix'] = 'front';
		$this->BcReplacePrefix->beforeRender($this->Controller);
		$result = $this->Controller->request->params['prefix'];
		$this->assertEmpty($result, 'beforeRenderが正しく動作していません');
	}

	/**
	 * Return all possible paths to find view files in order
	 */
	public function testGetViewPaths()
	{

		$this->Controller->theme = 'hoge-theme';
		$this->Controller->plugin = 'Mail';

		$result = $this->BcReplacePrefix->getViewPaths($this->Controller);
		$expected = [
			ROOT . '/app/webroot/theme/hoge-theme/',
			ROOT . '/lib/Baser/Plugin/Mail/View/',
			ROOT . '/app/webroot/',
			ROOT . '/app/View/',
			ROOT . '/lib/Baser/View/',
		];
		$this->assertEquals($expected, $result, 'Viewのパスを正しく取得できません');

	}

	public function testInitialize()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
