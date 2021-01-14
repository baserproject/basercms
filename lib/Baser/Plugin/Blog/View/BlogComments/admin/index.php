<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事コメント 一覧
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
<?php if (!empty($this->params['pass'][1])): ?>
	<div id="AjaxBatchUrl"
		 style="display:none"><?php $this->BcBaser->url(['controller' => 'blog_comments', 'action' => 'ajax_batch', $blogContent['BlogContent']['id'], $this->params['pass'][1]]) ?></div>
<?php else: ?>
	<div id="AjaxBatchUrl"
		 style="display:none"><?php $this->BcBaser->url(['controller' => 'blog_comments', 'action' => 'ajax_batch', $blogContent['BlogContent']['id'], 0]) ?></div>
<?php endif ?>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>
<div id="DataList"><?php $this->BcBaser->element('blog_comments/index_list') ?></div>
