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
 * [ADMIN] ブログカテゴリ フォーム
 */
$owners = $this->BcForm->getControlSource('BlogCategory.owner_id');
$fullUrl = $this->BcBaser->getContentsUrl($this->request->params['Content']['url'], true, $this->request->params['Site']['use_subdomain']) . 'archives/category/' . $this->BcForm->value('BlogCategory.name');
?>

<?php if ($this->action == 'admin_edit'): ?>
	<div class="bca-section bca-section__post-top">
	<span class="bca-post__no">
		<?php echo $this->BcForm->label('BlogCategory.no', 'No') ?> : <strong><?php echo $this->BcForm->value('BlogCategory.no') ?></strong>
		<?php echo $this->BcForm->input('BlogCategory.no', ['type' => 'hidden']) ?>
	</span>
		<span class="bca-post__url">
	  <a href="<?php echo $this->BcBaser->getUri($fullUrl) ?>" class="bca-text-url" target="_blank"
		 data-toggle="tooltip" data-placement="top" title="公開URLを開きます"><i
			  class="bca-icon--globe"></i><?php echo $this->BcBaser->getUri($fullUrl) ?></a>
	  <?php echo $this->BcForm->button('', [
		  'id' => 'BtnCopyUrl',
		  'class' => 'bca-btn',
		  'type' => 'button',
		  'data-bca-btn-type' => 'textcopy',
		  'data-bca-btn-category' => 'text',
		  'data-bca-btn-size' => 'sm'
	  ]) ?>
	</div>
<?php endif; ?>

<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if ($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('BlogCategory', ['url' => ['controller' => 'blog_categories', 'action' => 'add', $blogContent['BlogContent']['id']]]) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('BlogCategory', ['url' => ['controller' => 'blog_categories', 'action' => 'edit', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogCategory.id'), 'id' => false]]) ?>
<?php endif; ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->input('BlogCategory.id', ['type' => 'hidden']) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table bca-form-table">
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogCategory.name', __d('baser', 'カテゴリ名')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('BlogCategory.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('BlogCategory.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'URLに利用されます') ?></li>
						<li><?php echo __d('baser', '半角のみで入力してください') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogCategory.title', __d('baser', 'カテゴリタイトル')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('BlogCategory.title', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<?php echo $this->BcForm->error('BlogCategory.title') ?>
			</td>
		</tr>
		<?php if ($parents): ?>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogCategory.parent_id', __d('baser', '親カテゴリ')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php
					echo $this->BcForm->input('BlogCategory.parent_id', [
						'type' => 'select',
						'options' => $parents,
						'escape' => true])
					?>
					<?php echo $this->BcForm->error('BlogCategory.parent_id') ?>
				</td>
			</tr>
		<?php else: ?>
			<?php echo $this->BcForm->input('BlogCategory.parent_id', ['type' => 'hidden']) ?>
		<?php endif ?>
		<?php if ($this->BcBaser->siteConfig['category_permission']): ?>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('BlogCategory.owner_id', __d('baser', '管理グループ')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php if ($this->BcAdmin->isSystemAdmin()): ?>
						<?php
						echo $this->BcForm->input('BlogCategory.owner_id', [
							'type' => 'select',
							'options' => $owners,
							'empty' => __d('baser', '指定しない')])
						?>
						<i class="bca-icon--question-circle btn help bca-help"></i>
						<?php echo $this->BcForm->error('BlogCategory.owner_id') ?>
					<?php else: ?>
						<?php echo $this->BcText->arrayValue($this->request->data['BlogCategory']['owner_id'], $owners) ?>
						<?php echo $this->BcForm->input('BlogCategory.owner_id', ['type' => 'hidden']) ?>
					<?php endif ?>
					<div id="helptextOwnerId" class="helptext">
						<ul>
							<li><?php echo __d('baser', '管理グループを指定した場合、このカテゴリに属した記事は、管理グループのユーザーしか編集する事ができなくなります。') ?></li>
						</ul>
					</div>
				</td>
			</tr>
		<?php endif ?>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item',
			'data-bca-btn-type' => 'save',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg',]) ?>
	</div>
	<?php if ($this->action == 'admin_edit'): ?>
		<div class="bca-actions__sub">
			<?php
			$this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogCategory.id')], ['class' => 'submit-token button bca-btn bca-actions__item', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'sm'], sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('BlogCategory.name')), false);
			?>
		</div>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
