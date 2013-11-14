<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページ一覧
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->js(array(
	'admin/jquery.baser_ajax_data_list', 
	'admin/baser_ajax_data_list_config',
	'admin/jquery.baser_ajax_sort_table', 
	'admin/jquery.baser_ajax_batch',
	'admin/baser_ajax_batch_config'
), false);
?>

<script type="text/javascript">
$(function(){
	$.baserAjaxDataList.init();
	$.baserAjaxSortTable.init({ url: $("#AjaxSorttableUrl").html()});
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
});
</script>

<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(array('controller' => 'pages', 'action' => 'ajax_batch')) ?></div>
<div id="AjaxSorttableUrl" style="display:none"><?php $this->BcBaser->url(array('controller' => 'pages', 'action' => 'ajax_update_sort')) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>

<?php $this->BcBaser->element('pages/index_view_setting') ?>

<div id="DataList"><?php $this->BcBaser->element('pages/index_list') ?></div>

