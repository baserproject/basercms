<?php
/* SVN FILE: $Id$ */
/**
 * フィードBaserヘルパー
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * フィードBaserヘルパー
 *
 * @package baser.plugins.feed.views.helpers
 *
 */
class FeedBaserHelper extends AppHelper {
/**
 * フィード出力
 * 
 * @param int $id
 * @param mixid $mobile '' / boolean
 * @return void
 * @access public
 */
	function feed ($id, $mobile='') {
		
		$url = array('plugin'=>'feed','controller'=>'feed','action'=>'index');
		if($mobile===''){
			$mobile = (Configure::read('BcRequest.agent') == 'mobile');
		}
		if($mobile){
			$url['prefix'] = Configure::read('BcAgent.mobile.prefix');
		}
		echo $this->requestAction($url,array('pass'=>array($id)));

	}
	
}
