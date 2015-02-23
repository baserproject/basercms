<?php
/**
 * [ADMIN] ブログ記事コメント 一覧
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */


$this->BcBaser->js(array(
	'admin/jquery.baser_ajax_data_list',
	'admin/jquery.baser_ajax_batch',
	'admin/baser_ajax_data_list_config',
	'admin/baser_ajax_batch_config'
));
?>


<script type="text/javascript">
$(function(){
		$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
	});
</script>
<?php if (!empty($this->params['pass'][1])): ?>
	<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(array('controller' => 'blog_comments', 'action' => 'ajax_batch', $blogContent['BlogContent']['id'], $this->params['pass'][1])) ?></div>
	<?php else: ?>
	<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(array('controller' => 'blog_comments', 'action' => 'ajax_batch', $blogContent['BlogContent']['id'], 0)) ?></div>
<?php endif ?>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>
<div id="DataList"><?php $this->BcBaser->element('blog_comments/index_list') ?></div>