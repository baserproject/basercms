<?php
/* SVN FILE: $Id$ */
/**
 * プラグインコンテンツモデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * メニューモデル
 *
 * @package baser.models
 */
class PluginContent extends AppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'PluginContent';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('Cache');
/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'baser';
/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('alphaNumericPlus'),
					'message'	=> 'コンテンツ名は半角英数字、ハイフン、アンダースコアのみで入力してください。'),
			array(	'rule'		=> array('isUnique'),
					'on'		=> 'create',
					'message'	=>	'入力されたコンテンツ名は既に使用されています。'),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'コンテンツ名は50文字以内で入力してください。')
		),
		'content_id' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'コンテンツIDを入力してください。',
					'on'		=> 'update')
		),
		'plugin' => array(
			array(	'rule'		=> array('alphaNumericPlus'),
					'message'	=> 'プラグイン名は半角英数字、ハイフン、アンダースコアのみで入力してください。'),
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'プラグイン名を入力してください。'),
			array(	'rule'		=> array('maxLength', 20),
					'message'	=> 'プラグイン名は20文字以内で入力してください。')
		)
	);
/**
 * プラグイン名の書き換え
 * DBに登録したデータを元にURLのプラグイン名部分を書き換える。
 * 一つのプラグインで二つのコンテンツを設置した場合に利用する。
 * あらかじめ、plugin_contentsテーブルに、URLに使う名前とコンテンツを特定する。
 * プラグインごとの一意のキー[content_id]を保存しておく。
 *
 * content_idをコントローラーで取得するには、$plugins_controllerのcontentIdプロパティを利用する。
 * Router::connectの引数として値を与えると、$html->linkなどで、
 * Routerを利用する際にマッチしなくなりURLがデフォルトのプラグイン名となるので注意
 * 
 * @param$url
 * @return boolean
 * @access public
 */
	function addRoute($url) {

		if(!$url) {
			return false;
		}

		$agentAlias = Configure::read('AgentPrefix.currentAlias');
		$agentPrefix = Configure::read('AgentPrefix.currentAgent');
		$agentOn = Configure::read('AgentPrefix.on');
		
		$url = preg_replace('/^\//','',$url);
		if(strpos($url, '/') !== false) {
			list($name) = split('/',$url);
		}else {
			$name = $url;
		}
		
		$pluginContent = $this->find('first', array(
			'fields' => array('name', 'plugin'),
			'conditions'=> array('PluginContent.name' => $name)
		));
		if(!$pluginContent) {
			return false;
		}

		if(!$agentOn) {
			Router::connect(
					'/'.$pluginContent['PluginContent']['name'].'/:action/*',
					array(
						'plugin'	=> $pluginContent['PluginContent']['plugin'],
						'controller'=> $pluginContent['PluginContent']['plugin']
			));
		}else {
			Router::connect('/'.$agentAlias.'/'.$pluginContent['PluginContent']['name'].'/:action/*',
					array(
						'prefix'	=> $agentPrefix,
						'plugin'	=> $pluginContent['PluginContent']['plugin'],
						'controller'=> $pluginContent['PluginContent']['plugin']
			));
		}
		return true;

	}
}
?>