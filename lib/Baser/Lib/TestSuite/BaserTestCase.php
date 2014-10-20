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
 * @param string $name
 * @param array $data
 * @param string $dataName
 * @return void
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		// =====================================================================
		// Router::url() を内部的に利用するテストを実施した場合、Baser/Config/routes.php
		// が呼び出され、そこで利用されている PluginContent モデルを利用する事になる。
		// その際、fixture で接続先を test に切り替えた PluginContent を利用しないと
		// missing table となってい、原因がつかみにくい為、利用していない場合は強制的に
		// 利用する設定とした。
		// =====================================================================
		if (!isset($this->fixtures) || !in_array('baser.PluginContent', $this->fixtures)) {
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