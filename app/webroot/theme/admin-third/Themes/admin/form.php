<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			https://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマ フォーム
 *
 * @var BcAppView $this
 */
$this->BcBaser->js('admin/themes/form');
?>


<?php if ($folderDisabled): ?>
	<p><span class="required"><?php echo __d('baser', 'テーマフォルダに書込権限がありません。') ?></span></p>
<?php endif ?>
<?php if ($configDisabled): ?>
	<p><span class="required"><?php echo __d('baser', 'テーマ設定ファイルに書込権限がありません。') ?></span></p>
<?php endif ?>


<?php echo $this->BcForm->create('Theme', ['url' => ['action' => 'edit', $theme]]) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table bca-form-table">
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Theme.name', __d('baser', 'テーマ名')) ?>&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('Theme.name', ['type' => 'text', 'size' => 20, 'maxlength' => 255, 'autofocus' => true, 'disabled' => $folderDisabled]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('Theme.name') ?>
				<div id="helptextName" class="helptext"><?php echo __d('baser', ' 半角英数字のみで入力してください。 ') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Theme.title', __d('baser', 'タイトル')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('Theme.title', ['type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $configDisabled]) ?>
				<?php echo $this->BcForm->error('Theme.title') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Theme.description', __d('baser', '説明')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('Theme.description', ['type' => 'textarea', 'rows' => 5, 'cols' => 60, 'disabled' => $configDisabled]) ?>
				<?php echo $this->BcForm->error('Theme.description') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Theme.author', __d('baser', '制作者')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('Theme.author', ['type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $configDisabled]) ?>
				<?php echo $this->BcForm->error('Theme.author') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Theme.url', 'URL') ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('Theme.url', ['type' => 'text', 'size' => 60, 'maxlength' => 255, 'disabled' => $configDisabled]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('Theme.url') ?>
				<div id="helptextUrl" class="helptext">
					<ul>
						<li><?php echo __d('baser', '制作者のWEBサイトのURL。') ?></li>
						<li><?php echo __d('baser', '半角英数字のみで入力してください。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<?php if (!$folderDisabled && $siteConfig['theme'] != $this->BcForm->value('Theme.name')): ?>
	<div class="submit bca-actions">
		<div class="bca-actions__main">
			<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item', 'id' => 'BtnSave', 'data-bca-btn-type' => 'save', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg',]) ?>
		</div>
		<div class="bca-actions__sub">
			<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'del', $this->BcForm->value('Theme.name')], ['class' => 'submit-token btn-gray button bca-btn bca-actions__item', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'sm'], sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('Theme.name')), false); ?>
		</div>
	</div>
<?php endif; ?>

<?php echo $this->BcForm->end() ?>
