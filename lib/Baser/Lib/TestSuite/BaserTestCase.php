<?php
/**
 * BaserTestCase
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib.TestSuite
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

class BaserTestCase extends CakeTestCase {

/**
 * construct
 *
 * @param string $name
 * @param array $data
 * @param string $dataName
 * @return void
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		// =====================================================================
		// Router::url() を内部的に利用するテストを実施した場合、Baser/Config/routes.php
		// が呼び出され、そこで利用されている PluginContent / Page モデルを利用する事になる。
		// その際、fixture で接続先を test に切り替えた PluginContent / Page を利用しないと
		// missing table となってい、原因がつかみにくい為、利用していない場合は強制的に
		// 利用する設定とした。
		// =====================================================================
		if (!isset($this->fixtures) || !in_array('baser.Default.PluginContent', $this->fixtures)) {
			$this->fixtures[] = 'baser.Default.PluginContent';
		}
		if (!isset($this->fixtures) || !in_array('baser.Default.Page', $this->fixtures)) {
			$this->fixtures[] = 'baser.Default.Page';
		}
		parent::__construct($name, $data, $dataName);
	}
	
/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		Configure::write('debug', 1);
		Configure::write('App.baseUrl', '');
		// ブラウザと、コンソールでCakeRequestの内容が違うので一旦トップページとして初期化する
		$this->_getRequest('/');
	}
	
/**
 * 指定されたURLに対応しRouterパース済のCakeRequestのインスタンスを返す
 *
 * @param string $url URL
 * @return CakeRequest
 */
	protected function _getRequest($url) {
		Router::reload();
		$request = new CakeRequest($url);
		// コンソールからのテストの場合、requestのパラメーターが想定外のものとなってしまうので調整
		if(isConsole()) {
			$baseUrl = Configure::read('App.baseUrl');
			if($request->url === false) {
				$request->here = $baseUrl . '/';
			} elseif(preg_match('/^' . preg_quote($request->webroot, DS) . '/', $request->here)) {
				$request->here = $baseUrl . '/' . preg_replace('/^' . preg_quote($request->webroot, DS) . '/', '', $request->here);
			}
			if($baseUrl) {
				if(preg_match('/^\//', $baseUrl)) {
					$request->base = $baseUrl;
				} else {
					$request->base = '/' . $baseUrl;
				}
				$request->webroot = $baseUrl;
			} else {
				$request->base = '';
				$request->webroot = '/';
			}
		}
		
		Router::setRequestInfo($request);
		$params = Router::parse($request->url);
		$request->addParams($params);
		return $request;
	}

/**
 * ユーザーエージェント判定に利用される値をConfigureに設定
 * bootstrap.phpで行われている処理の代替
 *
 * @param string $prefix エージェントのプレフィックス
 * @return void
 */
	protected function _setAgent($prefix) {
		$agent = Configure::read("BcAgent.{$prefix}");
		if (empty($agent)) {
			return;
		}
		Configure::write('BcRequest.agent', $prefix);
		Configure::write('BcRequest.agentPrefix', $agent['prefix']);
		Configure::write('BcRequest.agentAlias', $agent['alias']);
	}

/**
 * ユーザーエージェント設定
 * 
 * @param string $agent エージェントのタイプ
 * @param boolean $enabled true:有効 / false:無効
 */
	protected function _setAgentSetting($agentType, $enabled) {
		Configure::write('BcApp.' . $agentType, $enabled);
	}
	
/**
 * エージェント判定に利用される値を消去する
 *
 * @return void
 */
	protected function _unsetAgent() {
		Configure::delete('BcRequest.agent');
		Configure::delete('BcRequest.agentPrefix');
		Configure::delete('BcRequest.agentAlias');
	}
/**
 * エージェントごとの固定ページの連動の判定に利用される値をConfigureに設定
 *
 * @param string $prefix エージェントのプレフィックス
 * @return void
 */
	protected function _setAgentLink($prefix) {
		$agent = Configure::read("BcAgent.{$prefix}");
		if (empty($agent)) {
			return;
		}
		Configure::write("BcSite.linked_pages_{$prefix}", '1');
	}

/**
 * エージェントの連携の判定を全てOFFにする
 *
 * @return void
 */
	protected function _unsetAgentLinks() {
		$prefixes = array('smartphone', 'mobile');
		foreach ($prefixes as $prefix) {
			Configure::write("BcSite.linked_pages_{$prefix}", '0');
		}
	}
}