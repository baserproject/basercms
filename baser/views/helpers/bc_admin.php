<?php
/* SVN FILE: $Id$ */
/**
 * BcAdminヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			cake
 * @subpackage		baser.app.view.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * BcAdminヘルパー
 *
 * @package cake
 * @subpackage baser.app.views.helpers
 */
class BcAdminHelper extends AppHelper {
/**
 * View
 * 
 * @var View
 * @access protected
 */
	var $_view = null;
/**
 * コンストラクタ
 *
 * @return void
 * @access public
 */
	function __construct() {
		$this->_view =& ClassRegistry::getObject('view');
	}
/**
 * 管理システムグローバルメニューの利用可否確認
 * 
 * @return boolean
 * @access public
 */
	function isAdminGlobalmenuUsed() {
		
		if(!BC_INSTALLED) {
			return false;
		}
		if(Configure::read('BcRequest.isUpdater')) {
			return false;
		}
		if(empty($this->params['admin']) || empty($this->_view->viewVars['user'])) {
			return false;
		}
		$UserGroup = ClassRegistry::getObject('UserGroup');
		return $UserGroup->isAdminGlobalmenuUsed($this->_view->viewVars['user']['user_group_id']);
		
	}
/**
 * ログインユーザーがシステム管理者かチェックする
 * 
 * @return boolean 
 */
	function isSystemAdmin () {
		
		if(empty($this->params['admin']) || empty($this->_view->viewVars['user'])) {
			return false;
		}
		if($this->_view->viewVars['user']['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
			return true;
		}
		return false;
		
	}
}