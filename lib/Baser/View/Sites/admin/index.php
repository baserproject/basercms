<?php
/**
 * サブサイト一覧
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2016, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->js(array(
	'admin/jquery.baser_ajax_data_list',
	'admin/baser_ajax_data_list_config',
	), false);
?>


<script type="text/javascript">
$(function(){
	$.baserAjaxDataList.init();
});
</script>


<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>

<div id="DataList"><?php $this->BcBaser->element('sites/index_list') ?></div>