<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマフォルダ登録・編集
 *
 * @var BcAppView $this
 */
$this->BcBaser->js('admin/theme_files/form_folder');
$parentPrams = $params = explode('/', $path);
if ($this->request->action !== 'admin_add_folder') {
	unset($parentPrams[count($parentPrams) - 1]);
}
?>


<!-- current -->
<div class="em-box bca-current-box">
	<?php echo __d('baser', '現在の位置') ?>：<?php echo h($currentPath) ?>
</div>

<?php if ($this->request->action == 'admin_add_folder'): ?>
	<?php echo $this->BcForm->create('ThemeFolder', ['id' => 'TemplateForm', 'url' => array_merge(['controller' => 'theme_files', 'action' => 'add_folder', $theme, $type], $params)]) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('ThemeFolder', ['id' => 'TemplateForm', 'url' => array_merge(['controller' => 'theme_files', 'action' => 'edit_folder', $theme, $type], $params)]) ?>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php if ($theme != 'core' && !$isWritable): ?>
	<div id="AlertMessage">ファイルに書き込み権限がないので編集できません。</div>
<?php endif ?>

<?php echo $this->BcForm->input('ThemeFolder.parent', ['type' => 'hidden']) ?>
<?php echo $this->BcForm->input('ThemeFolder.pastname', ['type' => 'hidden']) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table bca-form-table">
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('ThemeFolder.name', __d('baser', 'フォルダ名')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php if ($this->request->action != 'admin_view_folder'): ?>
					<?php echo $this->BcForm->input('ThemeFolder.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<div id="helptextName" class="helptext">
						<ul>
							<li><?php echo __d('baser', 'フォルダ名は半角で入力してください。') ?></li>
						</ul>
					</div>
					<?php echo $this->BcForm->error('ThemeFolder.name') ?>
				<?php else: ?>
					<?php echo $this->BcForm->input('ThemeFolder.name', ['type' => 'text', 'size' => 40, 'readonly' => 'readonly']) ?>
				<?php endif ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">

	<div class="bca-actions__before">
	<?php if ($this->request->action == 'admin_add_folder' || $this->request->action == 'admin_edit_folder'): ?>
		<?php $this->BcBaser->link(__d('baser', '一覧に戻る'), array_merge(['action' => 'index', $theme, $plugin, $type], explode('/', $path)), ['class' => 'btn-gray button bca-btn', 'data-bca-btn-type' => 'back-to-list']); ?>
	<?php else: ?>
		<?php $this->BcBaser->link(__d('baser', '一覧に戻る'), array_merge(['action' => 'index', $theme, $plugin, $type], explode('/', dirname($path))), ['class' => 'btn-gray button bca-btn', 'data-bca-btn-type' => 'back-to-list']); ?>
	<?php endif ?>
	</div>

	<?php if ($this->request->action == 'admin_add_folder'): ?>
		<div class="bca-actions__main">
			<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item', 'data-bca-btn-type' => 'save', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg', 'id' => 'BtnSave']) ?>
		</div>
	<?php elseif ($this->request->action == 'admin_edit_folder'): ?>
		<?php if ($isWritable): ?>
			<div class="bca-actions__main">
				<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item', 'data-bca-btn-type' => 'save', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg', 'id' => 'BtnSave']) ?>
			</div>
			<div class="bca-actions__sub">
				<?php $this->BcBaser->link(__d('baser', '削除'), array_merge(['action' => 'del', $theme, $type], $params), ['class' => 'submit-token button bca-btn bca-actions__item', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'sm'], sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('ThemeFolder.name')), false) ?>
			</div>
		<?php endif ?>
	<?php else: ?>
		<?php if (!$safeModeOn): ?>
			<?php if ($theme == 'core'): ?>
				<?php $this->BcBaser->link(__d('baser', '現在のテーマにコピー'), array_merge(['action' => 'copy_folder_to_theme', $theme, $plugin, $type], $params), ['class' => 'submit-token btn-red button bca-btn'], sprintf(__d('baser', '本当に現在のテーマ「%s」にコピーしてもいいですか？\n既に存在するファイルは上書きされます。'), Inflector::camelize($siteConfig['theme']))); ?>
			<?php endif ?>
		<?php else: ?>
			<?php echo __d('baser', '機能制限のセーフモードで動作していますので、現在のテーマへのコピーはできません。') ?>
		<?php endif ?>
	<?php endif ?>
</div>
