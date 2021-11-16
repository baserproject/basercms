<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事 フォーム
 */
$url = $this->request->params['Content']['url'] . 'archives/' . $this->BcForm->value('BlogPost.no');
$fullUrl = $this->BcBaser->getContentsUrl($url, true, $this->request->params['Site']['use_subdomain']);
$statuses = [0 => __d('baser', '非公開'), 1 => __d('baser', '公開')];
$this->BcBaser->css('admin/ckeditor/editor', ['inline' => true]);
$this->BcBaser->i18nScript([
	'alertMessage1' => __d('baser', 'ブログタグの追加に失敗しました。既に登録されていないか確認してください。'),
	'alertMessage2' => __d('baser', 'ブログタグの追加に失敗しました。'),
	'alertMessage3' => __d('baser', 'ブログカテゴリの追加に失敗しました。入力したブログカテゴリ名が既に登録されていないか確認してください。'),
	'alertMessage4' => __d('baser', 'ブログカテゴリの追加に失敗しました。'),
	'alertMessage5' => __d('baser', 'ブログカテゴリの追加に失敗しました。')
]);
$this->BcBaser->js('Blog.admin/blog_posts/form', false, [
	'id' => 'AdminBlogBLogPostsEditScript',
	'data-fullurl' => $fullUrl,
	'data-previewurl' => $this->Blog->getPreviewUrl($url, $this->request->params['Site']['use_subdomain'])
]);
?>


<div id="AddTagUrl"
	 style="display:none"><?php echo $this->BcBaser->url(['plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'ajax_add']) ?></div>
<div id="AddBlogCategoryUrl"
	 style="display:none"><?php echo $this->BcBaser->url(['plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'ajax_add', $blogContent['BlogContent']['id']]) ?></div>

<div id="AddBlogCategoryForm" title="<?php echo __d('baser', 'カテゴリ新規追加') ?>" style="display:none">
	<dl>
		<dt><?php echo $this->BcForm->label('BlogCategory.title', __d('baser', 'カテゴリタイトル')) ?></dt>
		<dd>
			<?php echo $this->BcForm->input('BlogCategory.title', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'value' => '', 'autofocus' => true]) ?>
		</dd>
		<dt><?php echo $this->BcForm->label('BlogCategory.name', __d('baser', 'カテゴリ名')) ?></dt>
		<dd>
			<?php echo $this->BcForm->input('BlogCategory.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'value' => '']) ?>
			<i class="bca-icon--question-circle btn help bca-help"></i>
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



<?php if ($this->action == 'admin_edit'): ?>
	<div class="bca-section bca-section__post-top">
  <span class="bca-post__no">
    <?php echo $this->BcForm->label('BlogPost.no', 'No') ?> : <strong><?php echo $this->BcForm->value('BlogPost.no') ?></strong>
    <?php echo $this->BcForm->input('BlogPost.no', ['type' => 'hidden']) ?>
  </span>

		<span class="bca-post__url">
    <?php //echo $this->BcForm->label('BlogPost.url', 'URL') ?>
    <a href="<?php echo $this->BcBaser->getUri(urldecode($this->request->params['Content']['url']) . '/archives/' . $this->BcForm->value('BlogPost.no')) ?>"
	   class="bca-text-url" target="_blank" data-toggle="tooltip" data-placement="top" title="公開URLを開きます"><i
			class="bca-icon--globe"></i><?php echo $this->BcBaser->getUri(urldecode($this->request->params['Content']['url']) . '/archives/' . $this->BcForm->value('BlogPost.no')) ?></a>
    <?php echo $this->BcForm->button('', [
		'id' => 'BtnCopyUrl',
		'class' => 'bca-btn',
		'type' => 'button',
		'data-bca-btn-type' => 'textcopy',
		'data-bca-btn-category' => 'text',
		'data-bca-btn-size' => 'sm'
	]) ?>
  </span>
	</div>
<?php endif; ?>

<!-- form -->
<section class="bca-section">
	<table id="FormTable" class="form-table bca-form-table">
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogPost.name', __d('baser', 'タイトル')) ?>
				&nbsp;<span class="required bca-label"
							data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('BlogPost.name', [
					'type' => 'text',
					'size' => 80,
					'maxlength' => 255,
					'autofocus' => true,
					'data-input-text-size' => 'full-counter',
					'counter' => true
				]) ?>
				<?php echo $this->BcForm->error('BlogPost.name') ?>
			</td>
		</tr>
		<?php if ($categories): ?>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogPost.blog_category_id', __d('baser', 'カテゴリー')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('BlogPost.blog_category_id', [
						'type' => 'select',
						'options' => $categories,
						'escape' => true
					]) ?>&nbsp
					<?php if ($hasNewCategoryAddablePermission): ?>
						<?php echo $this->BcForm->button(__d('baser', '新しいカテゴリを追加'), ['id' => 'BtnAddBlogCategory', 'class' => 'bca-btn']) ?>
					<?php endif ?>
					<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'display:none', 'id' => 'BlogCategoryLoader', 'class' => 'loader']) ?>
					<?php echo $this->BcForm->error('BlogPost.blog_category_id') ?>
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogPost.eye_catch', __d('baser', 'アイキャッチ画像')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('BlogPost.eye_catch', [
					'type' => 'file',
					'imgsize' => 'thumb',
					'width' => '300'
				]) ?>
				<?php echo $this->BcForm->error('BlogPost.eye_catch') ?>
			</td>
		</tr>
	</table>
</section>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php if (!empty($blogContent['BlogContent']['use_content'])): ?>
	<section class="bca-section bca-section__post-content">
		<label for="BlogPostContentTmp"
			   class="bca-form-table__label -label"><?php echo $this->BcForm->label('BlogPost.content', __d('baser', '概要')) ?></label>
		<span class="bca-form-table__input-wrap">
	  <?php echo $this->BcForm->ckeditor('BlogPost.content', [
		  'editorWidth' => 'auto',
		  'editorHeight' => '120px',
		  'editorToolType' => 'simple',
		  'editorEnterBr' => @$siteConfig['editor_enter_br']
	  ]); ?>
	  <?php echo $this->BcForm->error('BlogPost.content') ?>
   </span>
	</section>
<?php endif ?>

<section class="bca-section bca-section__post-detail">
	<label for="BlogPostDetailTmp" class="bca-form-table__label -label"><?php echo __d('baser', '本文') ?></label>
	<span class="bca-form-table__input-wrap">
  <?php echo $this->BcForm->editor('BlogPost.detail', array_merge([
	  'type' => 'editor',
	  'editor' => @$siteConfig['editor'],
	  'editorUseDraft' => true,
	  'editorDraftField' => 'detail_draft',
	  'editorWidth' => 'auto',
	  'editorHeight' => '480px',
	  'editorEnterBr' => @$siteConfig['editor_enter_br']
  ], $editorOptions)) ?>
  <?php echo $this->BcForm->error('BlogPost.detail') ?>
  </span>
</section>

<section class="bca-section">
	<table class="form-table bca-form-table">
		<?php if (!empty($blogContent['BlogContent']['tag_use'])): ?>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogTag.BlogTag', __d('baser', 'タグ')) ?></th>
				<td class="col-input bca-form-table__input">
					<div id="BlogTags" class="bca-form-table__group bca-blogtags">
						<?php echo $this->BcForm->input('BlogTag.BlogTag', [
							'type' => 'select',
							'multiple' => 'checkbox',
							'options' => $this->BcForm->getControlSource('BlogPost.blog_tag_id')
						]); ?>
						<?php echo $this->BcForm->error('BlogTag.BlogTag') ?>
					</div>
					<?php if ($hasNewTagAddablePermission): ?>
						<div class="bca-form-table__group">
							<?php echo $this->BcForm->input('BlogTag.name', [
								'type' => 'text'
							]) ?>
							<?php echo $this->BcForm->button(__d('baser', '新しいタグを追加'), [
								'id' => 'BtnAddBlogTag',
								'class' => 'bca-btn'
							]) ?>
							<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'vertical-align:middle;display:none', 'id' => 'TagLoader', 'class' => 'loader']) ?>
						</div>
					<?php endif ?>
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogPost.status', __d('baser', '公開状態')) ?>
				&nbsp;<span class="required bca-label"
							data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('BlogPost.status', [
					'type' => 'radio',
					'options' => $statuses
				]) ?>
				<?php echo $this->BcForm->error('BlogPost.status') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogPost.status', __d('baser', '公開日時')) ?></th>
			<td class="col-input bca-form-table__input">
        <span class="bca-datetimepicker__group">
          <span class="bca-datetimepicker__start">
            <?php echo $this->BcForm->input('BlogPost.publish_begin', [
				'type' => 'dateTimePicker',
				'size' => 12,
				'maxlength' => 10,
				'dateLabel' => ['text' => __d('baser', '開始日付')],
				'timeLabel' => ['text' => __d('baser', '開始時間')]
			], true) ?>
          </span>
          <span class="bca-datetimepicker__delimiter">〜</span>
          <span class="bca-datetimepicker__end">
            <?php echo $this->BcForm->input('BlogPost.publish_end', [
				'type' => 'dateTimePicker',
				'size' => 12,
				'maxlength' => 10,
				'dateLabel' => ['text' => __d('baser', '終了日付')],
				'timeLabel' => ['text' => __d('baser', '終了時間')]
			], true) ?>
            </span>
        </span>
				<?php echo $this->BcForm->error('BlogPost.publish_begin') ?>
				<?php echo $this->BcForm->error('BlogPost.publish_end') ?>
			</td>
		</tr>

		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogPost.status', __d('baser', 'サイト内検索')) ?>
				&nbsp
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('BlogPost.exclude_search', [
					'type' => 'checkbox',
					'label' => __d('baser', 'サイト内検索の検索結果より除外する')
				]) ?>
			</td>
		</tr>

		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogPost.user_id', __d('baser', '作成者')) ?>
				&nbsp;<span class="required bca-label"
							data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
			<td class="col-input bca-form-table__input">
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
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogPost.posts_date', __d('baser', '投稿日時')) ?>
				&nbsp;<span class="required bca-label"
							data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('BlogPost.posts_date', [
					'type' => 'dateTimePicker',
					'size' => 12,
					'maxlength' => 10
				], true) ?>
				<?php echo $this->BcForm->error('BlogPost.posts_date') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</section>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<section class="bca-actions">
	<?php if ($this->action == 'admin_edit' || $this->action == 'admin_add'): ?>
		<div class="bca-actions__main">
			<?php echo $this->BcForm->button(__d('baser', 'プレビュー'),
				[
					'id' => 'BtnPreview',
					'div' => false,
					'class' => 'button bca-btn bca-actions__item',
					'data-bca-btn-type' => 'preview',
				]) ?>
			<?php echo $this->BcForm->button(__d('baser', '保存'),
				[
					'type' => 'submit',
					'id' => 'BtnSave',
					'div' => false,
					'class' => 'button bca-btn bca-actions__item',
					'data-bca-btn-type' => 'save',
					'data-bca-btn-size' => 'lg',
					'data-bca-btn-width' => 'lg',
				]) ?>
		</div>
	<?php endif ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<div class="bca-actions__sub">
			<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogPost.id')],
				[
					'class' => 'submit-token button bca-btn bca-actions__item',
					'data-bca-btn-type' => 'delete',
					'data-bca-btn-size' => 'sm',
					'data-bca-btn-color' => 'danger'
				], sprintf(__d('baser', '%s を本当に削除してもいいですか？\n※ ブログ記事はゴミ箱に入らず完全に消去されます。'), $this->BcForm->value('BlogPost.name')), false); ?>
		</div>
	<?php endif ?>
</section>

<?php echo $this->BcForm->end() ?>

