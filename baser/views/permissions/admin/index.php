<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アクセス制限設定一覧
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$bcBaser->js('sorttable',false);
$bcBaser->js(array(
	'admin/jquery.baser_ajax_data_list', 
	'admin/jquery.baser_ajax_batch', 
	'admin/jquery.baser_ajax_sort_table', 
	'admin/baser_ajax_data_list_config',
	'admin/baser_ajax_batch_config'
));
?>


<script type="text/javascript">
$(function(){
	$("#PermissionsSearchBody").show();
	$.baserAjaxDataList.init();
	$.baserAjaxSortTable.init({ url: $("#AjaxSorttableUrl").html()});
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
});
</script>


<div id="AjaxBatchUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'permissions', 'action' => 'ajax_batch')) ?></div>
<div id="AjaxSorttableUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'permissions', 'action' => 'ajax_update_sort', $this->params['pass'][0])) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>	
<div id="DataList"><?php $bcBaser->element('permissions/index_list') ?></div>