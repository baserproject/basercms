<?php
/* SVN FILE: $Id$ */
/**
 * フィードBaserヘルパー
 * 
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views.helpers
 * @since			Baser v 0.1.0
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
			$mobile = (Configure::read('AgentPrefix.currentAgent') == 'mobile');
		}
		if($mobile){
			$url['prefix'] = Configure::read('AgentSettings.mobile.prefix');
		}
		echo $this->requestAction($url,array('pass'=>array($id)));

	}
	
}
?>