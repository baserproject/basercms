<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページカテゴリー フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$pageType = array();
if(Configure::read('Baser.mobile') || Configure::read('Baser.smartphone')) {
	$pageType = array('1' => 'PC');	
}
if(Configure::read('Baser.mobile')) {
	$pageType['2'] = 'モバイル';
}
if(Configure::read('Baser.smartphone')) {
	$pageType['3'] = 'スマートフォン';
}
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

<div id="AjaxCategorySourceUrl" class="display-none"><?php $baser->url(array('controller' => 'pages', 'action' => 'ajax_category_source')) ?></div>

<?php if($this->action == 'admin_edit' && $indexPage): ?>
	<?php if($indexPage['status']): ?>
	<p><strong>このカテゴリのURL：<?php $baser->link($baser->getUri('/' . $indexPage['url']), '/' . $indexPage['url'], array('target' => '_blank')) ?></strong></p>
	<?php else: ?>
	<p><strong>このカテゴリのURL：<?php echo $baser->getUri('/' . $indexPage['url']) ?></strong></p>
	<?php endif ?>
<?php endif ?>



<?php echo $formEx->create('PageCategory') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('PageCategory.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $formEx->value('PageCategory.id') ?>
				<?php echo $formEx->input('PageCategory.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php endif; ?>
<?php if($pageType): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('PageCategory.page_category_type', 'タイプ') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('PageCategory.page_category_type', array('type' => 'radio', 'options' => $pageType)) ?>　
				<?php echo $formEx->input('PageCategory.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php endif ?>
<?php if($parents): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('PageCategory.parent_id', '親カテゴリ') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('PageCategory.parent_id', array(
						'type'		=> 'select', 
						'options'	=> $parents,
						'escape'	=> false)) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('PageCategory.parent_id') ?>
				<div class="helptext">
					<ul>
						<li>カテゴリの下の階層にカテゴリを作成するには親カテゴリを選択します。</li>
					</ul>
				</div>
			</td>
		</tr>
<?php else: ?>
		<?php echo $formEx->input('PageCategory.parent_id', array('type' => 'hidden')) ?>
<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('PageCategory.name', 'ページカテゴリー名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('PageCategory.name', array('type' => 'text', 'size' => 40, 'maxlength' => 50)) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('PageCategory.name') ?>
				<div class="helptext">
					<ul>
						<li>ページカテゴリ名はURLで利用します。</li>
						<li>日本語の入力が可能です。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('PageCategory.title', 'ページカテゴリータイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('PageCategory.title', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('PageCategory.title') ?>
				<div class="helptext">
					<ul>
						<li>ページカテゴリタイトルはTitleタグとして出力されます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('PageCategory.contents_navi', 'コンテンツナビ') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('PageCategory.contents_navi', array(
						'type'		=> 'radio',
						'options'	=> $textEx->booleanDolist('利用'),
						'legend'	=> false,
						'separator'		=> '&nbsp;&nbsp;')) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('PageCategory.parent_id') ?>
				<div class="helptext">
					<ul>
						<li>同カテゴリ内のページ間ナビゲーション（コンテンツナビ）を利用するには「利用する」を選択します。</li>
					</ul>
				</div>
			</td>
		</tr>
<?php if($baser->siteConfig['category_permission']): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('PageCategory.owner_id', '管理グループ') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('PageCategory.owner_id', array(
						'type'		=> 'select',
						'options'	=> $formEx->getControlSource('PageCategory.owner_id'),
						'empty'		=> '指定しない')) ?>
				<?php echo $html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('PageCategory.owner_id') ?>
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
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit' && $formEx->value('PageCategory.name')!='mobile'): ?>
	<?php echo $formEx->submit('更新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削除', 
			array('action'=>'delete', $formEx->value('PageCategory.id')),
			array('class'=>'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？\n\nこのカテゴリに関連するページは、どのカテゴリにも関連しない状態として残ります。', $formEx->value('PageCategory.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>