<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] フィード読み込みAJAX
 */
header("Content-type: text/javascript charset=UTF-8");
$this->BcBaser->cacheHeader(MONTH, 'js');
$site = BcSite::findCurrent();
?>
document.write('
<div
	id="feeds<?php echo $id; ?>"><?php echo $this->html->image('admin/ajax-loader.gif', ['alt' => 'loading now...', 'style' => 'display:block;margin:auto']) ?></div>');

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
url:      '<?php echo $this->BcBaser->getUrl([$site->device => true, 'plugin' => 'feed', 'controller' => 'feed', 'action' => 'index', $id]); ?>',
cache: false,
success:  successCallback,
error:    errorCallback
});

<?php exit() ?>
