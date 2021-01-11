<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事 フォーム
 * @var BcAppView $this
 */
$url = $this->request->params['Content']['url'] . 'archives/' . $this->BcForm->value('BlogPost.no');
$fullUrl = $this->BcBaser->getContentsUrl($url, true, $this->request->params['Site']['use_subdomain']);
$statuses = [0 => __d('baser', '非公開'), 1 => __d('baser', '公開')];
$this->BcBaser->css('admin/ckeditor/editor', ['inline' => true]);
$this->BcBaser->i18nScript([
	'alertMessage1' => __d('baser', 'タグの追加に失敗しました。既に登録されていないか確認してください。'),
	'alertMessage2' => __d('baser', 'タグの追加に失敗しました。'),
	'alertMessage3' => __d('baser', 'カテゴリの追加に失敗しました。入力したカテゴリ名が既に登録されていないか確認してください。'),
	'alertMessage4' => __d('baser', 'カテゴリの追加に失敗しました。'),
	'alertMessage5' => __d('baser', 'カテゴリの追加に失敗しました。')
]);
$this->BcBaser->js('Blog.admin/blog_posts/form', false, [
	'id' => 'AdminBlogBLogPostsEditScript',
	'data-fullurl' => $fullUrl,
	'data-previewurl' => $this->Blog->getPreviewUrl($url, $this->request->params['Site']['use_subdomain'])
]);
?>


<div id="AddTagUrl"
	 style="display:none"><?php $this->BcBaser->url(['plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'ajax_add']) ?></div>
<div id="AddBlogCategoryUrl"
	 style="display:none"><?php $this->BcBaser->url(['plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'ajax_add', $blogContent['BlogContent']['id']]) ?></div>


<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if ($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('BlogPost', ['type' => 'file', 'url' => ['controller' => 'blog_posts', 'action' => 'add', $blogContent['BlogContent']['id']], 'id' => 'BlogPostForm']) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('BlogPost', ['type' => 'file', 'url' => ['controller' => 'blog_posts', 'action' => 'edit', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogPost.id'), 'id' => false], 'id' => 'BlogPostForm']) ?>
<?php endif; ?>
<?php echo $this->BcForm->input('BlogPost.id', ['type' => 'hidden']) ?>
<?php echo $this->BcForm->input('BlogPost.blog_content_id', ['type' => 'hidden', 'value' => $blogContent['BlogContent']['id']]) ?>
<?php echo $this->BcForm->hidden('BlogPost.mode') ?>


<?php if (empty($blogContent['BlogContent']['use_content'])): ?>
	<?php echo $this->BcForm->hidden('BlogPost.content') ?>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head" style="width:53px"><?php echo $this->BcForm->label('BlogPost.no', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('BlogPost.no') ?>
					<?php echo $this->BcForm->input('BlogPost.no', ['type' => 'hidden']) ?>
				</td>
			</tr>
			<tr>
				<th class="col-head" style="width:53px"><?php echo $this->BcForm->label('BlogPost.url', 'URL') ?></th>
				<td class="col-input">
					<span class="url"><?php echo urldecode($this->BcBaser->getUri($fullUrl)) ?></span>　
					<?php echo $this->BcForm->button(__d('baser', 'URLコピー'), ['class' => 'small-button', 'style' => 'font-weght:normal', 'id' => 'BtnCopyUrl']) ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ($categories): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('BlogPost.blog_category_id', __d('baser', 'カテゴリー')) ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->input('BlogPost.blog_category_id', ['type' => 'select', 'options' => $categories, 'escape' => true]) ?>
					&nbsp;
					<?php if ($hasNewCategoryAddablePermission): ?>
						<?php echo $this->BcForm->button(__d('baser', '新しいカテゴリを追加'), ['id' => 'BtnAddBlogCategory']) ?>
					<?php endif ?>
					<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'vertical-align:middle;display:none', 'id' => 'BlogCategoryLoader', 'class' => 'loader']) ?>
					<?php echo $this->BcForm->error('BlogPost.blog_category_id') ?>
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.name', __d('baser', 'タイトル')) ?>&nbsp;<span
					class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true, 'counter' => true]) ?>
				<?php echo $this->BcForm->error('BlogPost.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.eye_catch', __d('baser', 'アイキャッチ画像')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.eye_catch', ['type' => 'file', 'imgsize' => 'thumb', 'width' => '300']) ?>
				<?php echo $this->BcForm->error('BlogPost.eye_catch') ?>
			</td>
		</tr>
		<?php if (!empty($blogContent['BlogContent']['use_content'])): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('BlogPost.content', __d('baser', '概要')) ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->ckeditor('BlogPost.content', [
						'editorWidth' => 'auto',
						'editorHeight' => '120px',
						'editorToolType' => 'simple',
						'editorEnterBr' => @$siteConfig['editor_enter_br']
					]); ?>
					<?php echo $this->BcForm->error('BlogPost.content') ?>
				</td>
			</tr>
		<?php endif ?>
	</table>
</div>

<div class="section" style="text-align: center">
	<?php
	echo $this->BcForm->input('BlogPost.detail', array_merge([
		'type' => 'editor',
		'editor' => @$siteConfig['editor'],
		'editorUseDraft' => true,
		'editorDraftField' => 'detail_draft',
		'editorWidth' => 'auto',
		'editorHeight' => '480px',
		'editorEnterBr' => @$siteConfig['editor_enter_br']
	], $editorOptions))
	?>
	<?php echo $this->BcForm->error('BlogPost.detail') ?>
</div>

<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<?php if (!empty($blogContent['BlogContent']['tag_use'])): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('BlogTag.BlogTag', __d('baser', 'タグ')) ?></th>
				<td class="col-input">
					<div class="clearfix" id="BlogTags" style="padding:5px">
						<?php echo $this->BcForm->input('BlogTag.BlogTag', ['type' => 'select', 'multiple' => 'checkbox', 'options' => $this->BcForm->getControlSource('BlogPost.blog_tag_id')]); ?>
					</div>
					<?php echo $this->BcForm->error('BlogTag.BlogTag') ?>
					<?php if ($hasNewTagAddablePermission): ?>
						<?php echo $this->BcForm->input('BlogTag.name', ['type' => 'text']) ?>
						<?php echo $this->BcForm->button(__d('baser', '新しいタグを追加'), ['id' => 'BtnAddBlogTag']) ?>
						<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'vertical-align:middle;display:none', 'id' => 'TagLoader', 'class' => 'loader']) ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.status', __d('baser', '公開状態')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.status', ['type' => 'radio', 'options' => $statuses]) ?>
				<?php echo $this->BcForm->error('BlogPost.status') ?>
				&nbsp;&nbsp;&nbsp;&nbsp;<small>[公開期間]</small>&nbsp;
				<?php echo $this->BcForm->input('BlogPost.publish_begin', [
					'type' => 'dateTimePicker',
					'size' => 12,
					'maxlength' => 10,
					'dateLabel' => ['text' => '開始日付'],
					'timeLabel' => ['text' => '開始時間']
				]) ?>
				&nbsp;〜&nbsp;
				<?php echo $this->BcForm->input('BlogPost.publish_end', [
					'type' => 'dateTimePicker',
					'size' => 12,
					'maxlength' => 10,
					'dateLabel' => ['text' => '終了日付'],
					'timeLabel' => ['text' => '終了時間']
				]) ?><br/>
				<?php echo $this->BcForm->input('BlogPost.exclude_search', ['type' => 'checkbox', 'label' => __d('baser', 'サイト内検索の検索結果より除外する')]) ?>
				<?php echo $this->BcForm->error('BlogPost.publish_begin') ?>
				<?php echo $this->BcForm->error('BlogPost.publish_end') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.user_id', __d('baser', '作成者')) ?>&nbsp;<span
					class="required">*</span></th>
			<td class="col-input">
				<?php if (isset($user) && $user['user_group_id'] == Configure::read('BcApp.adminGroupId')): ?>
					<?php echo $this->BcForm->input('BlogPost.user_id', [
						'type' => 'select',
						'options' => $users
					]); ?>
					<?php echo $this->BcForm->error('BlogPost.user_id') ?>
				<?php else: ?>
					<?php if (isset($users[$this->BcForm->value('BlogPost.user_id')])): ?>
						<?php echo h($users[$this->BcForm->value('BlogPost.user_id')]) ?>
					<?php endif ?>
					<?php echo $this->BcForm->hidden('BlogPost.user_id') ?>
				<?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.posts_date', __d('baser', '投稿日')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.posts_date', ['type' => 'dateTimePicker', 'size' => 12, 'maxlength' => 10]) ?>
				<?php echo $this->BcForm->error('BlogPost.posts_date') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit">
	<?php if ($this->action == 'admin_add'): ?>
		<?php echo $this->BcForm->button(__d('baser', 'プレビュー'), ['div' => false, 'class' => 'button', 'id' => 'BtnPreview']) ?>
		<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
	<?php elseif ($this->action == 'admin_edit'): ?>
		<?php echo $this->BcForm->button(__d('baser', 'プレビュー'), ['div' => false, 'class' => 'button', 'id' => 'BtnPreview']) ?>
		<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
		<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogPost.id')], ['class' => 'submit-token button', 'id' => 'BtnDelete'], sprintf(__d('baser', "%s を本当に削除してもいいですか？\n※ ブログ記事はゴミ箱に入らず完全に消去されます。"), $this->BcForm->value('BlogPost.name'))); ?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>

<div id="AddBlogCategoryForm" title="<?php echo __d('baser', 'カテゴリ新規追加') ?>" style="display:none">
	<dl>
		<dt><?php echo $this->BcForm->label('BlogCategory.title', __d('baser', 'カテゴリタイトル')) ?></dt>
		<dd>
			<?php echo $this->BcForm->input('BlogCategory.title', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'value' => '', 'autofocus' => true]) ?>
		</dd>
		<dt><?php echo $this->BcForm->label('BlogCategory.name', __d('baser', 'カテゴリ名')) ?></dt>
		<dd>
			<?php echo $this->BcForm->input('BlogCategory.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'value' => '']) ?>
			<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpName', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li><?php echo __d('baser', 'URLに利用されます') ?></li>
					<li><?php echo __d('baser', '半角のみで入力してください') ?></li>
					<li><?php echo __d('baser', '空の場合はカテゴリタイトルから値が自動で設定されます') ?></li>
				</ul>
			</div>
		</dd>
	</dl>
</div>
