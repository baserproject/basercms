<?php
/* SVN FILE: $Id$ */
/**
 * インストール用シェルスクリプト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.vendors.shells
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Vendor', 'BcApp');
App::import('Component','BcManager');
/**
 * インストール用シェルスクリプト
 * 
 * @package baser.vendors.shells
 */
class BcManagerShell extends BcAppShell {
/**
 * startup 
 */
	function startup() {
		
		parent::startup();
		$this->BcManager = new BcManagerComponent($this);
		
	}
/**
 * reset 
 */
	function reset() {
		
		$dbConfig = getDbConfig();
		if(!$this->BcManager->reset($dbConfig)) {
			$this->err("baserCMSのリセットに失敗しました。ログファイルを確認してください。");
		}
		
	}
	
}