/* SVN FILE: $Id$ */
/**
 * 共通スタートアップ処理
 * 
 * Javascript / jQuery
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$(document).ready(function(){
	// 角丸クラスの登録
	if($('.corner5').corner) $('.corner5').corner("5px");
	if($('.corner10').corner) $('.corner10').corner("10px");
});