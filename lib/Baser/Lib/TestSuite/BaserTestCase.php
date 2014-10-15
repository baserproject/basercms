<?php
/**
 * BaserTestCase
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib.TestSuite
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

class BaserTestCase extends CakeTestCase {
	
/**
 * construct
 *
 * @return void
 */
	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		// =====================================================================
		// Router::url() を内部的に利用するテストを実施した場合、Baser/Config/routes.php 
		// が呼び出され、そこで利用されている PluginContent モデルを利用する事になる。
		// その際、fixture で接続先を test に切り替えた PluginContent を利用しないと
		// missing table となってい、原因がつかみにくい為、利用していない場合は強制的に
		// 利用する設定とした。
		// =====================================================================
		if(!isset($this->fixtures) || !in_array('baser.PluginContent', $this->fixtures)) {
			$this->fixtures[] = 'baser.PluginContent';
		}
		parent::__construct($name, $data, $dataName);
	}
	
/**
 * 指定されたURLに対応しRouterパース済のCakeRequestのインスタンスを返す
 *
 * @param string $url URL
 * @return CakeRequest
 */
	protected function _getRequest($url) {
		$request = new CakeRequest($url);
		Router::setRequestInfo($request);
		$params = Router::parse($request->url);
		$request->addParams($params);
		return $request;
	}

/**
 * ユーザーエージェント判定に利用される値をConfigureに設定
 * bootstrap.phpで行われている処理の代替
 *
 * @param string $key エージェントを表す文字列キー
 * @return void
 */
	protected function _setAgent($key) {
		$agent = Configure::read("BcAgent.{$key}");
		if(empty($agent)) {
			return;
		}
		Configure::write('BcRequest.agent', $key);
		Configure::write('BcRequest.agentPrefix', $agent['prefix']);
		Configure::write('BcRequest.agentAlias', $agent['alias']);
	}
	
}