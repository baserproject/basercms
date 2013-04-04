<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページ一覧　検索ボックス
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$pageType = array('1' => 'PC', '2' => 'モバイル', '3' => 'スマートフォン');
?>

<script type="text/javascript">
/**
 * 起動処理
 */
$(function() {
	
	$('input[name="data[Page][page_type]"]').click(pageTypeChengeHandler);
	$.baserAjaxDataList.resetSearchBox = function() {
		$("#PagePageType1").attr('checked', true);
		pageTypeChengeHandler();
	}
	
	pageTypeChengeHandler();
	
});
/**
 * ページタイプ変更時イベント
 */
function pageTypeChengeHandler() {
	
	var pageType = $('input[name="data[Page][page_type]"]:checked').val();

	$.ajax({
		type: "POST",
		url: $("#AjaxCategorySourceUrl").html()+'/'+pageType,
		beforeSend: function() {
			$("#PagePageCategoryId").attr('disabled', 'disabled');
		},
		success: function(result){
			if(result) {
				var categoryId = $("#PagePageCategoryId").val();
				$("#PagePageCategoryId option").remove();
				$("#PagePageCategoryId").append('<option value="">指定しない</option>');
				$("#PagePageCategoryId").append('<option value="noncat">カテゴリなし</option>');
				$("#PagePageCategoryId").append($(result).find('option'));
				$("#PagePageCategoryId").val(categoryId);
			}
		},
		complete: function() {
			$("#PagePageCategoryId").attr('disabled', false);
		}
	});
	
}
</script>

<div id="AjaxCategorySourceUrl" class="display-none"><?php $bcBaser->url(array('action' => 'ajax_category_source')) ?></div>

<?php echo $bcForm->create('Page', array('action' => 'index', 'url' => array('action' => 'index'))) ?>

<p>
	<span><?php echo $bcForm->label('Page.name', 'ページ名') ?> <?php echo $bcForm->input('Page.name', array('type' => 'text', 'size' => '30')) ?></span>
	<span><?php echo $bcForm->label('Page.status', '公開状態') ?> <?php echo $bcForm->input('Page.status', array('type' => 'select', 'options' => $bcText->booleanMarkList(), 'empty' => '指定なし')) ?></span>　
	<span><?php echo $bcForm->label('Page.author_id', '作成者') ?> <?php echo $bcForm->input('Page.author_id', array('type' => 'select', 'options' => $users, 'empty' => '指定なし')) ?></span>　

	<span style="white-space: nowrap">
<?php if($pageType): ?>
	<span><?php echo $bcForm->label('Page.page_type', 'タイプ') ?> <?php echo $bcForm->input('Page.page_type', array('type' => 'radio', 'options' => $pageType)) ?></span>　
<?php endif ?>

<?php if($pageCategories): ?>
	<span><?php echo $bcForm->label('Page.page_category_id', 'カテゴリー') ?> <?php echo $bcForm->input('Page.page_category_id', array('type' => 'select', 'options' => $pageCategories, 'escape' => false)) ?></span>
<?php endif ?>
	</span>
</p>

<div class="button">
	<?php $bcBaser->link($bcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $bcBaser->link($bcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>

<?php echo $bcForm->hidden('Page.open',array('value'=>true)) ?>
<?php echo $bcForm->end() ?>