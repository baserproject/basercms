<?php
/* SVN FILE: $Id$ */
/**
 * プラグインコンテンツモデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
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
 * @package			baser.models
 */
class PluginContent extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'PluginContent';
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
	var $useDbConfig = 'baser';
/**
 * beforeValidate
 *
 * @return	void
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(array('rule' => 'alphaNumeric',
						'message' => '>> コンテンツ名は半角英数字のみ入力して下さい'),
				array('rule' => array('isUnique'),
						'message' => '>> 入力されたコンテンツ名は既に使用されています。'));
		$this->validate['content_id'] = array(array('rule' => VALID_NOT_EMPTY,
						'message' => ">> コンテンツIDを入力して下さい"));
		$this->validate['plugin'] = array(array('rule' => 'alphaNumeric',
						'message' => '>> プラグイン名は半角英数字のみ入力して下さい'),
				array('rule' => VALID_NOT_EMPTY,
						'message' => '>> プラグイン名を入力して下さい'));
		return true;

	}
/**
 * プラグイン名の書き換え
 *
 * DBに登録したデータを元にURLのプラグイン名部分を書き換える。
 * 一つのプラグインで二つのコンテンツを設置した場合に利用する。
 * あらかじめ、plugin_contentsテーブルに、URLに使う名前とコンテンツを特定する。
 * プラグインごとの一意のキー[content_id]を保存しておく。
 *
 * content_idをコントローラーで取得するには、$plugins_controllerのcontentIdプロパティを利用する。
 * Router::connectの引数として値を与えると、$html->linkなどで、
 * Routerを利用する際にマッチしなくなりURLがデフォルトのプラグイン名となるので注意
 */
	function addRoute($url) {

		if(!$url) {
			return false;
		}

		$mobilePrefix = Configure::read('Mobile.prefix');
		$mobileOn = Configure::read('Mobile.on');
		$pluginContents = $this->find('all',array('fields'=>array('name','plugin')));
		if(!$pluginContents) {
			return false;
		}

		$url = preg_replace('/^\//','',$url);
		if(strpos($url, '/') !== false) {
			$_path = split('/',$url);
		}else {
			$_path[0] = $url;
			$_path[1] = '';
		}

		foreach($pluginContents as $pluginContent ) {
			if($pluginContent['PluginContent']['name']) {
				if(!$mobileOn && $_path[0] == $pluginContent['PluginContent']['name']) {
					Router::connect('/'.$pluginContent['PluginContent']['name'].'/:action/*',array('plugin'=>$pluginContent['PluginContent']['plugin'],'controller'=>$pluginContent['PluginContent']['plugin']));
				}elseif($mobileOn && $_path[0]==$pluginContent['PluginContent']['name']) {
					Router::connect('/'.$mobilePrefix.'/'.$pluginContent['PluginContent']['name'].'/:action/*',array('prefix' => 'mobile','plugin'=>$pluginContent['PluginContent']['plugin'],'controller'=>$pluginContent['PluginContent']['plugin']));
				}
			}
		}

	}
}
?>