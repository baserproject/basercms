<?php
/* SVN FILE: $Id$ */
/**
 * BcAdminヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
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
 * 管理システムグローバルメニューの利用可否確認
 * 
 * @return boolean
 * @access public
 */
	public function isAdminGlobalmenuUsed() {
		
		if(!BC_INSTALLED) {
			return false;
		}
		if(Configure::read('BcRequest.isUpdater')) {
			return false;
		}
		$user = $this->_View->getVar('user');
		if(empty($this->request->params['admin']) || !$user) {
			return false;
		}
		$UserGroup = ClassRegistry::getObject('UserGroup');
		return $UserGroup->isAdminGlobalmenuUsed($user['user_group_id']);
		
	}
	
}