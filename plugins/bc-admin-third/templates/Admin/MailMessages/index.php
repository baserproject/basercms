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
 * [ADMIN] 受信メール一覧
 */
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/jquery.baser_ajax_batch',
	'admin/libs/baser_ajax_data_list_config',
	'admin/libs/baser_ajax_batch_config'
]);
?>


<script type="text/javascript">
	$(function () {
		$.baserAjaxDataList.init();
		$.baserAjaxBatch.init({url: $("#AjaxBatchUrl").html()});
	});
</script>

<div class="panel-box bca-panel-box" id="FunctionBox">
	<?php echo $this->BcForm->create('Function', ['type' => 'get', 'url' => ['controller' => 'mail_fields', 'action' => 'download_csv', $this->params['pass'][0]]]) ?>
	<?php echo $this->BcForm->input('Function.encoding', ['type' => 'radio', 'options' => ['UTF-8' => 'UTF-8', 'SJIS-win' => 'SJIS'], 'value' => 'UTF-8']) ?>
	&nbsp;&nbsp;
	<?php echo $this->BcForm->submit(__d('baser', 'CSVダウンロード'), ['div' => false, 'class' => 'button-small']) ?>
	<?php echo $this->BcForm->end() ?>
</div>

<div id="AjaxBatchUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'mail_messages', 'action' => 'ajax_batch', $this->params['pass'][0]]) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>
<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('mail_messages/index_list') ?></div>
