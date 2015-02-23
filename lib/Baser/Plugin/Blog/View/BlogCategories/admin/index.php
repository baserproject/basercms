<?php
/**
 * [ADMIN] ブログカテゴリ 一覧
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
$allowOwners = array();
if (isset($user['user_group_id'])) {
	$allowOwners = array('', $user['user_group_id']);
}
$this->BcBaser->js(array(
	'admin/jquery.baser_ajax_data_list',
	'admin/jquery.baser_ajax_batch',
	'admin/baser_ajax_data_list_config',
	'admin/baser_ajax_batch_config'
));
?>


<script type="text/javascript">
$(function(){
		/**
		 * 削除
		 */
		$.baserAjaxDataList.config.methods.del = {
			button: '.btn-delete',
			confirm: 'このデータを本当に削除してもいいですか？\nこのカテゴリに関連する記事は、どのカテゴリにも関連しない状態として残ります。',
			result: function(row, result) {
				var config = $.baserAjaxDataList.config;
			if(result) {
					var rowClass = row.attr('class');
					var currentRowClassies = rowClass.split(' ');
					currentRowClassies = currentRowClassies.reverse();
					var currentRowGroupClass;
				$(currentRowClassies).each(function(){
					if(this.match(/row-group/)) {
							currentRowGroupClass = this;
							return false;
						}
					});

				$('.'+currentRowGroupClass).fadeOut(300, function(){
					$('.'+currentRowGroupClass).remove();
					if($(config.dataList+" tbody td").length) {
							$.baserAjaxDataList.initList();
						$(config.dataList+" tbody tr").removeClass('even odd');
							$.yuga.stripe();
						} else {
							var ajax = 'ajax=1';
						if( document.location.href.indexOf('?') == -1 ) {
								ajax = '?' + ajax;
							} else {
								ajax = '&' + ajax;
							}
							$.baserAjaxDataList.load(document.location.href + ajax);
						}
					});

				} else {
					$(config.alertBox).html('削除に失敗しました。');
					$(config.alertBox).fadeIn(500);
				}
			}
		};
		$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
	});
</script>

<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(array('controller' => 'blog_categories', 'action' => 'ajax_batch', $this->request->pass[0])) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>
<div id="DataList"><?php $this->BcBaser->element('blog_categories/index_list') ?></div>
