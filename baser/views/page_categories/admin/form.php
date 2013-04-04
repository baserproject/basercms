<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページカテゴリー フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$pageType = array('1' => 'PC', '2' => 'モバイル', '3' => 'スマートフォン');
$owners = $bcForm->getControlSource('PageCategory.owner_id');
?>

<script type="text/javascript">
/**
 * 起動処理
 */
$(function() {
	
	$('input[name="data[PageCategory][page_category_type]"]').click(pageTypeChengeHandler);
	pageTypeChengeHandler();
	
});
/**
 * ページタイプ変更時イベント
 */
function pageTypeChengeHandler() {
	
	var pageType = $('input[name="data[PageCategory][page_category_type]"]:checked').val();
	var options = {
		"data[Option][excludeParentId]": $("#PageCategoryId").val()
	};
	if(pageType == undefined) {
		pageType = 1;
	}
	$("#PageCategoryLayoutTemplate").attr('disabled', 'disabled');
	$("#PageCategoryContentTemplate").attr('disabled', 'disabled');
	$.getJSON($("#AjaxControlSources").html(), {type:pageType}, function(result){
		if(!result) {
			return;
		}
		var layoutTemplate = $("#PageCategoryLayoutTemplate").val();
		var contentTemplate = $("#PageCategoryContentTemplate").val();
		$("#PageCategoryLayoutTemplate option").remove();
		$("#PageCategoryContentTemplate option").remove();
		for(var key in result['layout']) {
			$("#PageCategoryLayoutTemplate").append($("<option/>").val(key).html(result['layout'][key]));
		}
		for(var key in result['content']) {
			$("#PageCategoryContentTemplate").append($("<option/>").val(key).html(result['content'][key]));
		}
		$("#PageCategoryLayoutTemplate").val(layoutTemplate);
		$("#PageCategoryContentTemplate").val(contentTemplate);
		$("#PageCategoryLayoutTemplate").attr('disabled', false);
		$("#PageCategoryContentTemplate").attr('disabled', false);
	});
	
	$.ajax({
		type: "POST",
		url: $("#AjaxCategorySourceUrl").html()+'/'+pageType,
		data: options,
		beforeSend: function() {
			$("#PageCategoryParentId").attr('disabled', 'disabled');
		},
		success: function(result){
			if(result) {
				var categoryId = $("#PageCategoryParentId").val();
				$("#PageCategoryParentId option").remove();
				$("#PageCategoryParentId").append('<option value="">指定しない</option>');
				$("#PageCategoryParentId").append($(result).find('option'));
				$("#PageCategoryParentId").val(categoryId);
			}
		},
		complete: function() {
			$("#PageCategoryParentId").attr('disabled', false);
		}
	});
	
}
</script>

<div id="AjaxCategorySourceUrl" class="display-none"><?php $bcBaser->url(array('controller' => 'pages', 'action' => 'ajax_category_source')) ?></div>
<div id="AjaxControlSources" class="display-none"><?php $bcBaser->url(array('controller' => 'page_categories', 'action' => 'ajax_control_sources')) ?></div>

<?php if($this->action == 'admin_edit' && $indexPage): ?>
<div class="em-box align-left">1
	<?php if($indexPage['status']): ?>
	<strong>このカテゴリのURL：<?php $bcBaser->link($bcBaser->getUri('/' . $indexPage['url']), '/' . $indexPage['url'], array('target' => '_blank')) ?></strong>
	<?php else: ?>
	<strong>このカテゴリのURL：<?php echo $bcBaser->getUri('/' . $indexPage['url']) ?></strong>
	<?php endif ?>
</div>
<?php endif ?>


<?php echo $bcForm->create('PageCategory') ?>
<?php if(!$pageType): ?>
	<?php echo $bcForm->input('PageCategory.page_category_type', array('type' => 'hidden')) ?>
<?php endif ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('PageCategory.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('PageCategory.id') ?>
				<?php echo $bcForm->input('PageCategory.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php endif; ?>
<?php if($pageType): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('PageCategory.page_category_type', 'タイプ') ?></th>
			<td class="col-input">
	<?php if($pageType): ?>
				<?php echo $bcForm->input('PageCategory.page_category_type', array('type' => 'radio', 'options' => $pageType)) ?>　
	<?php endif ?>
			</td>
		</tr>
<?php endif ?>
<?php if($parents): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('PageCategory.parent_id', '親カテゴリ') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('PageCategory.parent_id', array(
						'type'		=> 'select', 
						'options'	=> $parents,
						'escape'	=> false)) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('PageCategory.parent_id') ?>
				<div class="helptext">
					<ul>
						<li>カテゴリの下の階層にカテゴリを作成するには親カテゴリを選択します。</li>
					</ul>
				</div>
			</td>
		</tr>
<?php else: ?>
		<?php echo $bcForm->input('PageCategory.parent_id', array('type' => 'hidden')) ?>
<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('PageCategory.name', 'ページカテゴリー名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('PageCategory.name', array('type' => 'text', 'size' => 40, 'maxlength' => 50)) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('PageCategory.name') ?>
				<div class="helptext">
					<ul>
						<li>ページカテゴリー名はURLで利用します。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('PageCategory.title', 'ページカテゴリータイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('PageCategory.title', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('PageCategory.title') ?>
				<div class="helptext">
					<ul>
						<li>ページカテゴリータイトルはTitleタグとして出力されます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('PageCategory.contents_navi', 'コンテンツナビ') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('PageCategory.contents_navi', array(
						'type'		=> 'radio',
						'options'	=> $bcText->booleanDolist('利用'),
						'legend'	=> false,
						'separator'		=> '&nbsp;&nbsp;')) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('PageCategory.parent_id') ?>
				<div class="helptext">
					<ul>
						<li>同カテゴリ内のページ間ナビゲーション（コンテンツナビ）を利用するには「利用する」を選択します。</li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</div>

<h2 class="btn-slide-form"><a href="javascript:void(0)" id="formOption">オプション</a></h2>

<div id ="formOptionBody" class="slide-body section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th class="col-head"><?php echo $bcForm->label('PageCategory.layout_template', 'レイアウトテンプレート') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('PageCategory.layout_template', array('type' => 'select', 'options' => $bcPage->getTemplates())) ?>
				<?php echo $bcForm->error('PageCategory.layout_template') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('PageCategory.content_template', 'コンテンツテンプレート') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('PageCategory.content_template', array('type' => 'select', 'options' => $bcPage->getTemplates('content'))) ?>
				<?php echo $bcForm->error('PageCategory.content_template') ?>
			</td>
		</tr>
<?php if($bcBaser->siteConfig['category_permission']): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('PageCategory.owner_id', '管理グループ') ?></th>
			<td class="col-input">
	<?php if($bcAdmin->isSystemAdmin()): ?>
				<?php echo $bcForm->input('PageCategory.owner_id', array(
						'type'		=> 'select',
						'options'	=> $bcForm->getControlSource('PageCategory.owner_id'),
						'empty'		=> '指定しない')) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('PageCategory.owner_id') ?>
	<?php else: ?>
				<?php echo $bcText->arrayValue($this->data['PageCategory']['owner_id'], $owners) ?>
				<?php echo $bcForm->input('PageCategory.owner_id', array('type' => 'hidden')) ?>
	<?php endif ?>
				<div class="helptext">
					<ul>
						<li>管理グループを指定した場合、このカテゴリに属したページは、管理グループのユーザーしか編集する事ができなくなります。</li>
					</ul>
				</div>
			</td>
		</tr>
<?php endif ?>
	</table>
</div>
<div class="submit">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if ($this->action == 'admin_edit' && $bcForm->value('PageCategory.name')!='mobile'): ?>
	<?php $bcBaser->link('削除', 
			array('action' => 'delete', $bcForm->value('PageCategory.id')),
			array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？\n\nこのカテゴリに関連するページは、どのカテゴリにも関連しない状態として残ります。', $bcForm->value('PageCategory.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>