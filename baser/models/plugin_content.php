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
	function beforeValidate(){

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
}
?>