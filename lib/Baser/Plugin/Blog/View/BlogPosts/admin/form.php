<?php
/**
 * [ADMIN] ブログ記事 フォーム
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
$this->BcBaser->css('admin/ckeditor/editor', array('inline' => true));
$statuses = array(0 => '非公開', 1 => '公開');
$this->BcBaser->link('&nbsp;', array('controller' => 'blog', 'action' => 'preview', $blogContent['BlogContent']['id'], $previewId, 'view'), array('style' => 'display:none', 'id' => 'LinkPreview'));
?>

<div id="CreatePreviewUrl" style="display:none"><?php echo $this->BcBaser->url(array('controller' => 'blog', 'action' => 'preview', $blogContent['BlogContent']['id'], $previewId, 'create')) ?></div>
<div id="AddTagUrl" style="display:none"><?php echo $this->BcBaser->url(array('plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'ajax_add')) ?></div>
<div id="AddBlogCategoryUrl" style="display:none"><?php echo $this->BcBaser->url(array('plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'ajax_add', $blogContent['BlogContent']['id'])) ?></div>
<?php echo $this->BcForm->input('UseContent', array('type' => 'hidden', 'value' => $blogContent['BlogContent']['use_content'])) ?>


<script type="text/javascript">
	$(window).load(function() {
		$("#BlogPostName").focus();
	});
$(function(){

	$("input[type=text]").each(function(){
		$(this).keypress(function(e){
			if(e.which && e.which === 13) {
				return false;
			}
			return true;
		});
	});
	
/**
 * プレビューボタンクリック時イベント
 */
	var useContent = Number($("#UseContent").val());
	$("#BtnPreview").click(function(){

		var detail = $("#BlogPostDetail").val();
		if(typeof editor_detail_tmp != "undefined") {
			$("#BlogPostDetail").val(editor_detail_tmp.getData());
		}

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

		$("#BlogPostDetail").val(detail);

		return false;

	});
	
	$("#LinkPreview").colorbox({width:"90%", height:"90%", iframe:true,
		onCleanup: function() {
			$.bcToken.update(function(){
				$("input[name='data[_Token][key]']").val($.bcToken.key);
			}, {loaderType: 'none'});
		}
	});
	
/**
 * フォーム送信時イベント
 */
	$("#BtnSave").click(function(){
		if(typeof editor_detail_tmp != "undefined") {
			editor_detail_tmp.execCommand('synchronize');
		}
		$("#BlogPostMode").val('save');
		$("#BlogPostForm").submit();
		return false;
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
			return false;
		}
		$.bcToken.check(function(){
			$.ajax({
				type: "POST",
				url: $("#AddTagUrl").html(),
				data: {
					'data[BlogTag][name]': $("#BlogTagName").val(),
					'data[_Token][key]': $.bcToken.key
				},
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
		}, {loaderType: 'target', loaderSelector: '#TagLoader', hideLoader: false});
		return false;
	});
/**
 * ブログカテゴリ追加
 */
	$("#BtnAddBlogCategory").click(function(){
		var category = prompt("新しいブログカテゴリを入力してください。");
		if(!category) {
			return false;
		}
		$.bcToken.check(function(){
			$.ajax({
				type: "POST",
				url: $("#AddBlogCategoryUrl").html(),
				data: {
					'data[BlogCategory][name]': category,
					'data[_Token][key]': $.bcToken.key
				},
				dataType: 'script',
				beforeSend: function() {
					$("#BtnAddBlogCategory").attr('disabled', 'disabled');
					$("#BlogCategoryLoader").show();
				},
				success: function(result){
					if(result) {
						$("#BlogPostBlogCategoryId").append($('<option />').val(result).html(category));
						$("#BlogPostBlogCategoryId").val(result);
					} else {
						alert('ブログカテゴリの追加に失敗しました。既に登録されていないか確認してください。');
					}
				},
				error: function(XMLHttpRequest, textStatus){
					if(XMLHttpRequest.responseText) {
						alert('ブログカテゴリの追加に失敗しました。\n\n' + XMLHttpRequest.responseText);
					} else {
						alert('ブログカテゴリの追加に失敗しました。\n\n' + XMLHttpRequest.statusText);
					}
				},
				complete: function(xhr, textStatus) {
					$("#BtnAddBlogCategory").removeAttr('disabled');
					$("#BlogCategoryLoader").hide();
					$("#BlogPostBlogCategoryId").effect("highlight",{},1500);
				}
			});
		}, {loaderType: 'target', loaderSelector: '#BlogCategoryLoader', hideLoader: false});
		return false;
	});
});
</script>


<?php if ($this->action == 'admin_edit'): ?>
	<div class="em-box align-left">
		<?php if ($this->BcForm->value('BlogPost.status') && $blogContent['BlogContent']['status']): ?>
			この記事のURL　：<?php
			$this->BcBaser->link(
				$this->BcBaser->getUri('/' . $blogContent['BlogContent']['name'] . '/archives/' . $this->BcForm->value('BlogPost.no')), '/' . $blogContent['BlogContent']['name'] . '/archives/' . $this->BcForm->value('BlogPost.no'))
			?>
		<?php else: ?>
			この記事のURL　：<?php echo $this->BcBaser->getUri('/' . $blogContent['BlogContent']['name'] . '/archives/' . $this->BcForm->value('BlogPost.no')) ?>
		<?php endif ?>
			<br />
			プレビュー用URL：<?php $this->BcBaser->link(
				$this->BcBaser->getUri(array('controller' => 'blog', 'action'=>'preview', $blogContent['BlogContent']['id'], $this->data['BlogPost']['id'], 'view')),
				$this->BcBaser->getUri(array('controller' => 'blog', 'action'=>'preview', $blogContent['BlogContent']['id'], $this->data['BlogPost']['id'], 'view')),
				array('target' => '_blank')
			); ?>
	</div>
<?php endif ?>


<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if ($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('BlogPost', array('type' => 'file', 'url' => array('controller' => 'blog_posts', 'action' => 'add', $blogContent['BlogContent']['id']), 'id' => 'BlogPostForm')) ?>
	<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('BlogPost', array('type' => 'file', 'url' => array('controller' => 'blog_posts', 'action' => 'edit', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogPost.id'), 'id' => false), 'id' => 'BlogPostForm')) ?>
<?php endif; ?>
<?php echo $this->BcForm->input('BlogPost.id', array('type' => 'hidden')) ?>
<?php echo $this->BcForm->input('BlogPost.blog_content_id', array('type' => 'hidden', 'value' => $blogContent['BlogContent']['id'])) ?>
<?php echo $this->BcForm->hidden('BlogPost.mode') ?>


<?php if (empty($blogContent['BlogContent']['use_content'])): ?>
	<?php echo $this->BcForm->hidden('BlogPost.content') ?>
<?php endif ?>


<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if ($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head" style="width:53px"><?php echo $this->BcForm->label('BlogPost.no', 'NO') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->value('BlogPost.no') ?>
				<?php echo $this->BcForm->input('BlogPost.no', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if ($categories): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.blog_category_id', 'カテゴリー') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.blog_category_id', array('type' => 'select', 'options' => $categories, 'escape' => false)) ?>&nbsp;
				<?php if($newCatAddable && $hasNewCategoryAddablePermission): ?>
					<?php echo $this->BcForm->button('新しいカテゴリを追加', array('id' => 'BtnAddBlogCategory')) ?>
				<?php endif ?>
				<?php $this->BcBaser->img('admin/ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'BlogCategoryLoader', 'class' => 'loader')) ?>
				<?php echo $this->BcForm->error('BlogPost.blog_category_id') ?>
			</td>
		</tr>
	<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.name', 'タイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255, 'counter' => true)) ?>
				<?php echo $this->BcForm->error('BlogPost.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.eye_catch', 'アイキャッチ画像') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->file('BlogPost.eye_catch', array('imgsize' => 'thumb')) ?>
				<?php echo $this->BcForm->error('BlogPost.eye_catch') ?>
			</td>
		</tr>
	<?php if (!empty($blogContent['BlogContent']['use_content'])): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.content', '概要') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->ckeditor('BlogPost.content', array(
					'editorWidth' => 'auto',
					'editorHeight' => '120px',
					'editorToolType' => 'simple',
					'editorEnterBr' => @$siteConfig['editor_enter_br']
				)); ?>
				<?php echo $this->BcForm->error('BlogPost.content') ?>
			</td>
		</tr>
	<?php endif ?>
	</table>
</div>

<div class="section" style="text-align: center">
	<?php
	echo $this->BcForm->editor('BlogPost.detail', array_merge(array(
		'editor' => @$siteConfig['editor'],
		'editorUseDraft' => true,
		'editorDraftField' => 'detail_draft',
		'editorWidth' => 'auto',
		'editorHeight' => '480px',
		'editorEnterBr' => @$siteConfig['editor_enter_br']
			), $editorOptions))
	?>
		<?php echo $this->BcForm->error('BlogPost.detail') ?>
</div>

<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<?php if (!empty($blogContent['BlogContent']['tag_use'])): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogTag.BlogTag', 'タグ') ?></th>
			<td class="col-input">
				<div class="clearfix" id="BlogTags" style="padding:5px">
					<?php echo $this->BcForm->input('BlogTag.BlogTag', array('type' => 'select', 'multiple' => 'checkbox', 'options' => $this->BcForm->getControlSource('BlogPost.blog_tag_id'))); ?>
				</div>
				<?php echo $this->BcForm->error('BlogTag.BlogTag') ?>
				<?php echo $this->BcForm->input('BlogTag.name', array('type' => 'text')) ?>
				<?php echo $this->BcForm->button('新しいタグを追加', array('id' => 'BtnAddBlogTag')) ?>
				<?php $this->BcBaser->img('admin/ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'TagLoader', 'class' => 'loader')) ?>
			</td>
		</tr>
		<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.status', '公開状態') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.status', array('type' => 'radio', 'options' => $statuses)) ?>
				<?php echo $this->BcForm->error('BlogPost.status') ?>
				&nbsp;&nbsp;
				<?php echo $this->BcForm->dateTimePicker('BlogPost.publish_begin', array('size' => 12, 'maxlength' => 10), true) ?>
				&nbsp;〜&nbsp;
				<?php echo $this->BcForm->dateTimePicker('BlogPost.publish_end', array('size' => 12, 'maxlength' => 10), true) ?><br />
				<?php echo $this->BcForm->input('BlogPost.exclude_search', array('type' => 'checkbox', 'label' => 'サイト内検索の検索結果より除外する')) ?>
				<?php echo $this->BcForm->error('BlogPost.publish_begin') ?>
				<?php echo $this->BcForm->error('BlogPost.publish_end') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.user_id', '作成者') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php if (isset($user) && $user['user_group_id'] == Configure::read('BcApp.adminGroupId')): ?>
					<?php echo $this->BcForm->input('BlogPost.user_id', array(
						'type' => 'select',
						'options' => $users
					)); ?>
					<?php echo $this->BcForm->error('BlogPost.user_id') ?>
				<?php else: ?>
					<?php if (isset($users[$this->BcForm->value('BlogPost.user_id')])): ?>
					<?php echo $users[$this->BcForm->value('BlogPost.user_id')] ?>
					<?php endif ?>
					<?php echo $this->BcForm->hidden('BlogPost.user_id') ?>
				<?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.posts_date', '投稿日') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->dateTimePicker('BlogPost.posts_date', array('size' => 12, 'maxlength' => 10), true) ?>
				<?php echo $this->BcForm->error('BlogPost.posts_date') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<!-- button -->
<div class="submit">
	<?php if ($this->action == 'admin_add'): ?>
		<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
		<?php echo $this->BcForm->button('保存前確認', array('div' => false, 'class' => 'button', 'id' => 'BtnPreview')) ?>
	<?php elseif ($this->action == 'admin_edit'): ?>
		<?php if ($editable): ?>
		<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
		<?php endif ?>
		<?php echo $this->BcForm->button('保存前確認', array('div' => false, 'class' => 'button', 'id' => 'BtnPreview')) ?>
		<?php if ($editable): ?>
		<?php $this->BcBaser->link('削除', array('action' => 'delete', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogPost.id')), array('class' => 'submit-token button'), sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('BlogPost.name')), false); ?>
		<?php endif ?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
