<?php

/**
 * [ADMIN] 検索インデックス一覧　Ajax
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(function(){
	$(".priority").change(function() {
		var id = this.id.replace('ContentPriority', '');
		var priority = $(this).val();
		$.bcToken.check(function(){
			var data = {
				'data[Content][id]':id,
				'data[Content][priority]': priority,
				'data[_Token][key]': $.bcToken.key
			};
			$.ajax({
				type: "POST",
				url: $("#AjaxChangePriorityUrl").html()+'/'+id,
				data: data,
				beforeSend: function() {
					$("#flashMessage").slideUp();
					$("#PriorityAjaxLoader"+id).show();
				},
				success: function(result){
					if(!result) {
						$("#flashMessage").html('処理中にエラーが発生しました。');
						$("#flashMessage").slideDown();
					}
				},
				error: function() {
					$("#flashMessage").html('処理中にエラーが発生しました。');
					$("#flashMessage").slideDown();
				},
				complete: function() {
					$("#PriorityAjaxLoader"+id).hide();
				}
			});
		});
	});
});
</script>


<?php $this->BcBaser->element('contents/index_list') ?>
