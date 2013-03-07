<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログコンテンツ 一覧
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
		$.baserAjaxDataList.config.methods.del.confirm = '削除を行うと関連する記事やカテゴリは全て削除されてしまい元に戻す事はできません。\n本当に削除してもいいですか？';
		$.baserAjaxDataList.init();
		$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
	});
</script>

<div id="AlertMessage" class="message" style="display:none"></div>
<div id="AjaxBatchUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'blog_contents', 'action' => 'ajax_batch')) ?></div>
<div id="DataList"><?php $bcBaser->element('../blog_contents/admin/ajax_index', null, false, false) ?></div>