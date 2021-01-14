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
 * [ADMIN] ブログカテゴリ フォーム
 * @var \BcAppView $this
 */
$this->BcBaser->js('Blog.admin/blog_categories/form', false);
$owners = $this->BcForm->getControlSource('BlogCategory.owner_id');
$fullUrl = $this->BcBaser->getContentsUrl($this->request->params['Content']['url'], true, $this->request->params['Site']['use_subdomain']) . 'archives/category/' . $this->BcForm->value('BlogCategory.name');
?>


<?php if ($this->action == 'admin_edit'): ?>
	<div class="em-box align-left">
		<p>
			<strong><?php echo sprintf(__d('baser', 'このカテゴリのURL：%s'), $this->BcBaser->getLink($fullUrl, $fullUrl, ['target' => '_blank'])) ?></strong>
		</p>
	</div>
<?php endif ?>


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
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('BlogCategory.no', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('BlogCategory.no') ?>
					<?php echo $this->BcForm->input('BlogCategory.no', ['type' => 'hidden']) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogCategory.name', __d('baser', 'カテゴリ名')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogCategory.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpName', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('BlogCategory.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'URLに利用されます') ?></li>
						<li><?php echo __d('baser', '半角（半角英数字、ハイフン、アンダーバー）のみで入力してください') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogCategory.title', __d('baser', 'カテゴリタイトル')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogCategory.title', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<?php echo $this->BcForm->error('BlogCategory.title') ?>
			</td>
		</tr>
		<?php if ($parents): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('BlogCategory.parent_id', __d('baser', '親カテゴリ')) ?></th>
				<td class="col-input">
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
				<th class="col-head"><?php echo $this->BcForm->label('BlogCategory.owner_id', __d('baser', '管理グループ')) ?></th>
				<td class="col-input">
					<?php if ($this->BcAdmin->isSystemAdmin()): ?>
						<?php
						echo $this->BcForm->input('BlogCategory.owner_id', [
							'type' => 'select',
							'options' => $owners,
							'empty' => __d('baser', '指定しない')])
						?>
						<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpOwnerId', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
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
<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php
		$this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogCategory.id')], ['class' => 'submit-token button'], sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('BlogCategory.name')), false);
		?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
