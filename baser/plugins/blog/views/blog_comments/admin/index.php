<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事コメント 一覧
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$bcBaser->js(array(
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
<?php if(!empty($this->params['pass'][1])): ?>
<div id="AjaxBatchUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'blog_comments', 'action' => 'ajax_batch', $blogContent['BlogContent']['id'], $this->params['pass'][1])) ?></div>
<?php else: ?>
<div id="AjaxBatchUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'blog_comments', 'action' => 'ajax_batch', $blogContent['BlogContent']['id'], 0)) ?></div>
<?php endif ?>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="DataList"><?php $bcBaser->element('blog_comments/index_list') ?></div>