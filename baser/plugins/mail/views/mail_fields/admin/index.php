<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メールフィールド 一覧
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
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
	$.baserAjaxDataList.config.methods.copy.result = null;
	$.baserAjaxDataList.init();
	$.baserAjaxSortTable.init({ url: $("#AjaxSorttableUrl").html()});
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
});
</script>

<div id="AjaxBatchUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'mail_fields', 'action' => 'ajax_batch', $mailContent['MailContent']['id'])) ?></div>
<div id="AjaxSorttableUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'mail_fields', 'action' => 'ajax_update_sort', $mailContent['MailContent']['id'])) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="DataList"><?php $bcBaser->element('mail_fields/index_list') ?></div>
