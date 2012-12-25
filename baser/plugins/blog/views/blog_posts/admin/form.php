<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事 フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$bcBaser->css('ckeditor/editor', array('inline' => true));
$statuses = array(0=>'非公開', 1=>'公開');
$bcBaser->link('&nbsp;', array('controller' => 'blog', 'action' => 'preview', $blogContent['BlogContent']['id'], $previewId, 'view'), array('style' => 'display:none', 'id' => 'LinkPreview'));
?>

<div id="CreatePreviewUrl" style="display:none"><?php echo $bcBaser->url(array('controller' => 'blog', 'action' => 'preview', $blogContent['BlogContent']['id'], $previewId, 'create')) ?></div>
<div id="AddTagUrl" style="display:none"><?php echo $bcBaser->url(array('plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'ajax_add')) ?></div>


<script type="text/javascript">
$(window).load(function() {
	$("#BlogPostName").focus();
});
$(function(){
	
/**
 * プレビューボタンクリック時イベント
 */
	$("#BtnPreview").click(function(){
		var content = $("#BlogPostContent").val();
		var detail = $("#BlogPostDetail").val();
		$("#BlogPostContent").val(editor_content_tmp.getData());
		$("#BlogPostDetail").val(editor_detail_tmp.getData());
		$.ajax({
			type: "POST",
			url: $("#CreatePreviewUrl").html(),
			data: $("#BlogPostForm").serialize(),
			success: function(result){
				if(result) {
					$("#LinkPreview").trigger("click");
				} else {
					alert('プレビューの読み込みに失敗しました。');
				}
			}
		});
		$("#BlogPostContent").val(content);
		$("#BlogPostDetail").val(detail);
		return false;
	});
	$("#LinkPreview").colorbox({width:"90%", height:"90%", iframe:true});
/**
 * フォーム送信時イベント
 */
	$("#btnSave").click(function(){
		editor_content_tmp.execCommand('synchronize');
		editor_detail_tmp.execCommand('synchronize');
		$("#BlogPostMode").val('save');
		$("#BlogPostForm").submit();
	});
/**
 * ブログタグ追加
 */
	$("#BlogTagName").keypress(function(ev) {
		if ((ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13)) {
			$("#BtnAddBlogTag").click();
			return false;
		} else {
			return true;
		}
	});
	$("#BtnAddBlogTag").click(function(){
		if(!$("#BlogTagName").val()) {
			return;
		}
		$.ajax({
			type: "POST",
			url: $("#AddTagUrl").html(),
			data: {'data[BlogTag][name]': $("#BlogTagName").val()},
			dataType: 'html',
			beforeSend: function() {
				$("#BtnAddBlogTag").attr('disabled', 'disabled');
				$("#TagLoader").show();
			},
			success: function(result){
				if(result) {
					$("#BlogTags").append(result);
					$("#BlogTagName").val('');
				} else {
					alert('ブログタグの追加に失敗しました。既に登録されていないか確認してください。');
				}
			},
			error: function(){
				alert('ブログタグの追加に失敗しました。');
			},
			complete: function(xhr, textStatus) {
				$("#BtnAddBlogTag").removeAttr('disabled');
				$("#TagLoader").hide();
				$("#BlogTags").effect("highlight",{},1500);
			}
		});
	});
});
</script>
<?php if($this->action == 'admin_edit'): ?>
<div class="em-box align-left">
	<?php if($bcForm->value('BlogPost.status')): ?>
	この記事のURL：<?php $bcBaser->link(
			$bcBaser->getUri('/' . $blogContent['BlogContent']['name'] . '/archives/' . $bcForm->value('BlogPost.no')),
			'/' . $blogContent['BlogContent']['name'] . '/archives/' . $bcForm->value('BlogPost.no')) ?>
	<?php else: ?>
	この記事のURL：<?php echo $bcBaser->getUri('/' . $blogContent['BlogContent']['name'] . '/archives/' . $bcForm->value('BlogPost.no')) ?>
	<?php endif ?>
</div>
<?php endif ?>


<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if($this->action=='admin_add'): ?>
<?php echo $bcForm->create('BlogPost', array('url' => array('controller' => 'blog_posts', 'action' => 'add', $blogContent['BlogContent']['id']), 'id' => 'BlogPostForm')) ?>
<?php elseif($this->action == 'admin_edit'): ?>
<?php echo $bcForm->create('BlogPost', array('url' => array('controller' => 'blog_posts', 'action' => 'edit', $blogContent['BlogContent']['id'], $bcForm->value('BlogPost.id'), 'id' => false), 'id' => 'BlogPostForm')) ?>
<?php endif; ?>
<?php echo $bcForm->input('BlogPost.id', array('type' => 'hidden')) ?>
<?php echo $bcForm->input('BlogPost.blog_content_id', array('type' => 'hidden', 'value' => $blogContent['BlogContent']['id'])) ?>
<?php echo $bcForm->hidden('BlogPost.mode') ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head" style="width:53px"><?php echo $bcForm->label('BlogPost.no', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('BlogPost.no') ?>
				<?php echo $bcForm->input('BlogPost.no', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if($categories): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogPost.blog_category_id', 'カテゴリー') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogPost.blog_category_id', array('type' => 'select', 'options' => $categories, 'escape' => false)) ?>
				<?php echo $bcForm->error('BlogPost.blog_category_id') ?>
			</td>
		</tr>
	<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogPost.name', 'タイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogPost.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255, 'counter' => true)) ?>
				<?php echo $bcForm->error('BlogPost.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogPost.content', '概要') ?></th>
			<td class="col-input">
				<?php echo $bcForm->ckeditor('BlogPost.content', 
						array('cols' => 60, 'rows' => 20),
						$ckEditorOptions1) ?>
				<?php echo $bcForm->error('BlogPost.content') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogPost.detail', '本文') ?></th>
			<td class="col-input">
				<?php echo $bcForm->ckeditor('BlogPost.detail',
						array('cols' => 60, 'rows' => 20),
						$ckEditorOptions2) ?>
				<?php echo $bcForm->error('BlogPost.detail') ?>
			</td>
		</tr>
	<?php if(!empty($blogContent['BlogContent']['tag_use'])): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogTag.BlogTag', 'タグ') ?></th>
			<td class="col-input">
				<div class="clearfix" id="BlogTags" style="padding:5px">
				<?php echo $bcForm->input('BlogTag.BlogTag',
						array('type' => 'select', 'multiple' => 'checkbox', 'options' => $bcForm->getControlSource('BlogPost.blog_tag_id'))) ?>
				</div>
				<?php echo $bcForm->error('BlogTag.BlogTag') ?>
				<?php echo $bcForm->input('BlogTag.name', array('type' => 'text')) ?>
				<?php echo $bcForm->button('新しいタグを追加', array('id' => 'BtnAddBlogTag')) ?>
				<?php $bcBaser->img('ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'TagLoader', 'class' => 'loader')) ?>
			</td>
		</tr>
	<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogPost.status', '公開状態') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogPost.status', array(
						'type'		=> 'radio',
						'options'	=> $statuses,
						'legend'	=> false,
						'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $bcForm->error('BlogPost.status') ?>
				&nbsp;&nbsp;
				<?php echo $bcForm->dateTimePicker('BlogPost.publish_begin', array('size' => 12, 'maxlength' => 10), true) ?>
				&nbsp;〜&nbsp;
				<?php echo $bcForm->dateTimePicker('BlogPost.publish_end', array('size' => 12, 'maxlength' => 10),true) ?><br />
				<?php echo $bcForm->input('BlogPost.exclude_search', array('type' => 'checkbox', 'label' => 'サイト内検索の検索結果より除外する')) ?>
				<?php echo $bcForm->error('BlogPost.publish_begin') ?>
				<?php echo $bcForm->error('BlogPost.publish_end') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogPost.user_id', '作成者') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
	<?php if(isset($user) && $user['user_group_id'] == 1): ?>
				<?php echo $bcForm->input('BlogPost.user_id', array(
						'type'		=> 'select',
						'options'	=> $users)) ?>
				<?php echo $bcForm->error('BlogPost.user_id') ?>
	<?php else: ?>
		<?php if(isset($users[$bcForm->value('BlogPost.user_id')])): ?>
				<?php echo $users[$bcForm->value('BlogPost.user_id')] ?>
		<?php endif ?>
				<?php echo $bcForm->hidden('BlogPost.user_id') ?>
	<?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogPost.posts_date', '作成日') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->dateTimePicker('BlogPost.posts_date', array('size' => 12, 'maxlength' => 10), true) ?>
				<?php echo $bcForm->error('BlogPost.posts_date') ?>
			</td>
		</tr>
	</table>
</div>
<!-- button -->
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $bcForm->button('登録', array('div' => false, 'class' => 'btn-red button', 'id' => 'btnSave')) ?>
	<?php echo $bcForm->button('保存前確認', array('div' => false, 'class' => 'btn-green button', 'id' => 'BtnPreview')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php if($editable): ?>
	<?php echo $bcForm->button('更新', array('div'=>false, 'class' => 'btn-orange button', 'id'=>'btnSave')) ?>
	<?php endif ?>
	<?php echo $bcForm->button('保存前確認', array('div' => false, 'class' => 'btn-green button', 'id' => 'BtnPreview')) ?>
	<?php if($editable): ?>
	<?php $bcBaser->link('削除',
			array('action' => 'delete', $blogContent['BlogContent']['id'], $bcForm->value('BlogPost.id')),
			array('class'=>'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $bcForm->value('BlogPost.name')),
			false); ?>
	<?php endif ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>