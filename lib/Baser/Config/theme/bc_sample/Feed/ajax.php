<?php
/**
 * フィード読込Javascript
 *
 * Javascriptファイルとして読み込む
 * （例）<?php $this->BcBaser->js('/feed/ajax/1') ?>
 */
header("Content-type: text/javascript charset=UTF-8");
$this->BcBaser->cacheHeader(MONTH, 'js');
?>


document.write('<div id="feeds<?php echo $id; ?>"><?php echo $this->html->image('admin/ajax-loader.gif', array('alt' => 'loading now...', 'style' => 'display:block;margin:auto')) ?></div>');

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
	url:      '<?php echo Router::url(array('plugin' => 'feed', 'controller' => 'feed', 'action' => 'index', $id)); ?>',
	cache: false,
	success:  successCallback,
	error:    errorCallback
});

<?php exit() ?>