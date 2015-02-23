<?php
/**
 * [ADMIN] ページ一覧　検索ボックス
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
$pageType = array('1' => 'PC', '2' => 'モバイル', '3' => 'スマートフォン');
?>

<script type="text/javascript">
/**
 * 起動処理
 */
$(function() {
	
	/*$('input[name="data[ViewSetting][page_type]"]').click(pageTypeChengeHandler);
	if($.baserAjaxDataList) {
		$.baserAjaxDataList.resetSearchBox = function() {
			$("#PagePageType1").attr('checked', true);
			pageTypeChengeHandler();
		};
	}
	
	pageTypeChengeHandler();*/
	
});
/**
 * ページタイプ変更時イベント
 */
function pageTypeChengeHandler() {
	
	var pageType = $('input[name="data[ViewSetting][page_type]"]:checked').val();

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

<div id="AjaxCategorySourceUrl" class="display-none"><?php $this->BcBaser->url(array('action' => 'ajax_category_source')) ?></div>

<?php echo $this->BcForm->create('Page', array('action' => 'index', 'url' => array('action' => 'index'))) ?>

<p>
	<span><?php echo $this->BcForm->label('Page.name', 'ページ名') ?> <?php echo $this->BcForm->input('Page.name', array('type' => 'text', 'size' => '30')) ?></span>
	<span><?php echo $this->BcForm->label('Page.status', '公開状態') ?> <?php echo $this->BcForm->input('Page.status', array('type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => '指定なし')) ?></span>　
	<span><?php echo $this->BcForm->label('Page.author_id', '作成者') ?> <?php echo $this->BcForm->input('Page.author_id', array('type' => 'select', 'options' => $users, 'empty' => '指定なし')) ?></span>　

	<span style="white-space: nowrap">
		<?php if ($pageCategories): ?>
			<span><?php echo $this->BcForm->label('Page.page_category_id', 'カテゴリー') ?> <?php echo $this->BcForm->input('Page.page_category_id', array('type' => 'select', 'options' => $pageCategories, 'escape' => false)) ?></span>
		<?php endif ?>
	</span>
</p>

<div class="button">
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>

<?php echo $this->BcForm->hidden('Page.open', array('value' => true)) ?>
<?php echo $this->BcForm->end() ?>
