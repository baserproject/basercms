<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Mail.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] メールフィールド 一覧
 */
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/jquery.baser_ajax_batch',
	'admin/libs/jquery.baser_ajax_sort_table',
	'admin/libs/baser_ajax_data_list_config',
	'admin/libs/baser_ajax_batch_config'
]);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
	'url' => ['action' => 'add', $this->request->params['pass'][0]],
	'title' => __d('baser', '新規フィールド追加'),
]);
?>


<script type="text/javascript">
	$(function () {
		$.baserAjaxDataList.config.methods.copy.result = null;
		$.baserAjaxDataList.init();
		$.baserAjaxSortTable.init({url: $("#AjaxSorttableUrl").html()});
		$.baserAjaxBatch.init({url: $("#AjaxBatchUrl").html()});
	});
</script>

<div id="AjaxBatchUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'mail_fields', 'action' => 'ajax_batch', $mailContent['MailContent']['id']]) ?></div>
<div id="AjaxSorttableUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'mail_fields', 'action' => 'ajax_update_sort', $mailContent['MailContent']['id']]) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>

<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('mail_fields/index_list') ?></div>
