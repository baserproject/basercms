<?php
/* SVN FILE: $Id$ */
/**
 * フィード読み込みAJAX
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
 * @package			baser.plugins.feed.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$baser->cacheHeader(MONTH,'js');
?>
document.write('<div id="feeds<?php echo $id; ?>"><?php echo $html->image('/feed/img/ajax-loader.gif',array('alt'=>'loading now...','style'=>'display:block;margin:auto')) ?></div>');

// 読込み成功時の処理
var successCallback = function (response)
{
	if(response == 'false'){
		$("#feeds<?php echo $id; ?>").html("");
	}else{	
		$("#feeds<?php echo $id; ?>").hide();
		$("#feeds<?php echo $id; ?>").html(response);
		$("#feeds<?php echo $id; ?>").slideDown(500);
	}
};
// 読込み失敗時の処理
var errorCallback = function (xml, status, e)
{
	$("#feeds<?php echo $id; ?>").html("");
};

//  リクエスト処理
$.ajax({
	type: 'GET',
	url:      '<?php echo Router::url('/feed/index/'.$id); ?>',
	success:  successCallback,
	error:    errorCallback
});