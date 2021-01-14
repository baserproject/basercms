<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] エディタテンプレートー一覧
 */
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/baser_ajax_data_list_config',
]);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
	'url' => ['action' => 'add'],
	'title' => __d('baser', '新規追加'),
]);
?>


<script type="text/javascript">
	$(function () {
		$.baserAjaxDataList.init();
	});
</script>

<div id="AjaxBatchUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'editor_templates', 'action' => 'ajax_batch']) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>
<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('editor_templates/index_list') ?></div>
