<?php
/* SVN FILE: $Id$ */
/**
 * フィードBaserヘルパー
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
 * @package			baser.plugins.feed.views.helpers
 *
 */
class FeedBaserHelper extends AppHelper {
/**
 * フィード出力
 * @param	int		$id
 * @param	mixid	$mobile '' / boolean
 */
	function feed ($id, $mobile='') {
		
		$url = array('plugin'=>'feed','controller'=>'feed','action'=>'index');
		if($mobile===''){
			$mobile = Configure::read('Mobile.on');
		}
		if($mobile){
			$url['prefix'] = 'mobile';
		}
		echo $this->requestAction($url,array('pass'=>array($id)));

	}
	
}
?>