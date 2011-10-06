<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事 フォーム
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$statuses = array(0=>'非公開', 1=>'公開');
$baser->css('ckeditor/editor', null, null, false);
$baser->link('&nbsp;', array('controller' => 'blog', 'action' => 'preview', $blogContent['BlogContent']['id'], $previewId, 'view'), array('style' => 'display:none', 'id' => 'LinkPreview'));
?>

<script type="text/javascript">
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
			url: '<?php echo $this->base ?>/admin/blog/preview/<?php echo $blogContent['BlogContent']['id'] ?>/<?php echo $previewId ?>/create',
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
			url: '<?php echo $this->base ?>/admin/blog/blog_tags/ajax_add',
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

<!-- title -->
<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ブログ記事の登録を行います。</p>
	<ul>
		<li>ワード感覚でブログ記事の作成を行う事ができます。</li>
		<li>記事を作成してもすぐに公開しない場合は、公開状態を「公開しない」にしておきます。</li>
		<li>「公開しない」にした記事を確認するには、画面下の「確認」ボタン、または、一覧の「確認」ボタンをクリックします。</li>
	</ul>
</div>

<?php if($this->action == 'admin_edit'): ?>
	<?php if($formEx->value('BlogPost.status')): ?>
	<p><strong>この記事のURL：<?php $baser->link(
			$baser->getUri('/' . $blogContent['BlogContent']['name'] . '/archives/' . $formEx->value('BlogPost.no')),
			'/' . $blogContent['BlogContent']['name'] . '/archives/' . $formEx->value('BlogPost.no'),
			array('target' => '_blank')) ?></strong></p>
	<?php else: ?>
	<p><strong>この記事のURL：<?php echo $baser->getUri('/' . $blogContent['BlogContent']['name'] . '/archives/' . $formEx->value('BlogPost.no')) ?></strong></p>
	<?php endif ?>
<?php endif ?>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if($this->action=='admin_add'): ?>
<?php echo $formEx->create('BlogPost', array('url' => '/admin/blog/blog_posts/add/' . $blogContent['BlogContent']['id'], 'id' => 'BlogPostForm')) ?>
<?php elseif($this->action == 'admin_edit'): ?>
<?php echo $formEx->create('BlogPost', array('url' => '/admin/blog/blog_posts/edit/' . $blogContent['BlogContent']['id'] . '/' . $formEx->value('BlogPost.id'), 'id' => 'BlogPostForm')) ?>
<?php endif; ?>
<?php echo $formEx->input('BlogPost.id', array('type' => 'hidden')) ?>
<?php echo $formEx->input('BlogPost.blog_content_id', array('type' => 'hidden', 'value' => $blogContent['BlogContent']['id'])) ?>
<?php echo $formEx->hidden('BlogPost.mode') ?>

<!-- form -->
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head" style="width:53px"><?php echo $formEx->label('BlogPost.no', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->value('BlogPost.no') ?>
			<?php echo $formEx->input('BlogPost.no', array('type' => 'hidden')) ?>
		</td>
	</tr>
<?php endif; ?>
<?php if($categories): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogPost.blog_category_id', 'カテゴリ') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('BlogPost.blog_category_id', array('type' => 'select', 'options' => $categories, 'escape' => false)) ?>
			<?php echo $formEx->error('BlogPost.blog_category_id') ?>
		</td>
	</tr>
<?php endif ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogPost.name', 'タイトル') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('BlogPost.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255, 'counter' => true)) ?>
			<?php echo $formEx->error('BlogPost.name') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogPost.content', '本文') ?></th>
		<td class="col-input">
			<?php echo $formEx->ckeditor('BlogPost.content', 
					array('cols' => 60, 'rows' => 20),
					$ckEditorOptions1) ?>
			<?php echo $formEx->error('BlogPost.content') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogPost.detail', '詳細') ?></th>
		<td class="col-input">
			<?php echo $formEx->ckeditor('BlogPost.detail',
					array('cols' => 60, 'rows' => 20),
					$ckEditorOptions2) ?>
			<?php echo $formEx->error('BlogPost.detail') ?>
		</td>
	</tr>
<?php if(!empty($blogContent['BlogContent']['tag_use'])): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogTag.BlogTag', 'タグ') ?></th>
		<td class="col-input">
			<div class="clearfix" id="BlogTags" style="padding:5px">
			<?php echo $formEx->input('BlogTag.BlogTag',
					array('type' => 'select', 'multiple' => 'checkbox', 'options' => $formEx->getControlSource('BlogPost.blog_tag_id'))) ?>
			</div>
			<?php echo $formEx->error('BlogTag.BlogTag') ?>
			<?php echo $formEx->input('BlogTag.name', array('type' => 'text')) ?>
			<?php echo $formEx->button('新しいタグを追加', array('id' => 'BtnAddBlogTag')) ?>
			<?php $baser->img('ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'TagLoader', 'class' => 'loader')) ?>
		</td>
	</tr>
<?php endif ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogPost.status', '公開状態') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('BlogPost.status', array(
					'type'		=> 'radio',
					'options'	=> $statuses,
					'legend'	=> false,
					'separator'	=> '&nbsp;&nbsp;')) ?>
			<?php echo $formEx->error('BlogPost.status') ?>
			&nbsp;&nbsp;
			<?php echo $formEx->dateTimePicker('BlogPost.publish_begin', array('size' => 12, 'maxlength' => 10), true) ?>
			&nbsp;〜&nbsp;
			<?php echo $formEx->dateTimePicker('BlogPost.publish_end', array('size' => 12, 'maxlength' => 10),true) ?><br />
			<?php echo $formEx->input('BlogPost.exclude_search', array('type' => 'checkbox', 'label' => 'サイト内検索の検索結果より除外する')) ?>
			<?php echo $formEx->error('BlogPost.publish_begin') ?>
			<?php echo $formEx->error('BlogPost.publish_end') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogPost.user_id', '作成者') ?></th>
		<td class="col-input">
<?php if(isset($user) && $user['user_group_id'] == 1): ?>
			<?php echo $formEx->input('BlogPost.user_id', array(
					'type'		=> 'select',
					'options'	=> $users)) ?>
			<?php echo $formEx->error('BlogPost.user_id') ?>
<?php else: ?>
	<?php if(isset($users[$formEx->value('BlogPost.user_id')])): ?>
			<?php echo $users[$formEx->value('BlogPost.user_id')] ?>
	<?php endif ?>
			<?php echo $formEx->hidden('BlogPost.user_id') ?>
<?php endif ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogPost.posts_date', '作成日') ?></th>
		<td class="col-input">
			<?php echo $formEx->dateTimePicker('BlogPost.posts_date', array('size' => 12, 'maxlength' => 10), true) ?>
			<?php echo $formEx->error('BlogPost.posts_date') ?>
		</td>
	</tr>
</table>

<!-- button -->
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->button('登　録', array('div' => false, 'class' => 'btn-red button', 'id' => 'btnSave')) ?>
	<?php echo $formEx->button('保存前確認', array('div' => false, 'class' => 'btn-green button', 'id' => 'BtnPreview')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php if($editable): ?>
	<?php echo $formEx->button('更　新', array('div'=>false, 'class' => 'btn-orange button', 'id'=>'btnSave')) ?>
	<?php endif ?>
	<?php echo $formEx->button('保存前確認', array('div' => false, 'class' => 'btn-green button', 'id' => 'BtnPreview')) ?>
	<?php if($editable): ?>
	<?php $baser->link('削　除',
			array('action' => 'delete', $blogContent['BlogContent']['id'], $formEx->value('BlogPost.id')),
			array('class'=>'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('BlogPost.name')),
			false); ?>
	<?php endif ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>