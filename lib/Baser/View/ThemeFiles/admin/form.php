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
 * [ADMIN] テーマファイル登録・編集
 *
 * @var BcAppView $this
 */
$this->BcBaser->js('admin/themes/form');
$params = explode('/', $path);
$parentPrams = explode('/', $path);
if ($this->request->action !== 'admin_add') {
	unset($parentPrams[count($parentPrams) - 1]);
}
?>


<div class="em-box align-left">
	<?php echo __d('baser', '現在の位置') ?>：<?php echo h($currentPath) ?>
</div>

<?php if ($theme != 'core' && !$isWritable): ?>
	<div id="AlertMessage"><?php echo __d('baser', 'ファイルに書き込み権限がないので編集できません。') ?></div>
<?php endif ?>

<?php if ($this->request->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('ThemeFile', ['id' => 'ThemeFileForm', 'url' => array_merge(['action' => 'add'], [$theme, $plugin, $type], explode('/', $path))]) ?>
<?php elseif ($this->request->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('ThemeFile', ['id' => 'ThemeFileForm', 'url' => array_merge(['action' => 'edit'], [$theme, $plugin, $type], explode('/', $path))]) ?>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->input('ThemeFile.parent', ['type' => 'hidden']) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('ThemeFile.name', __d('baser', 'ファイル名')) ?>&nbsp;<span
					class="required">*</span></th>
			<td class="col-input">
				<?php if ($this->request->action != 'admin_view'): ?>
					<?php echo $this->BcForm->input('ThemeFile.name', ['type' => 'text', 'size' => 30, 'maxlength' => 255, 'autofocus' => true]) ?>
					<?php if ($this->BcForm->value('ThemeFile.ext')): ?>.<?php endif ?>
					<?php echo h($this->BcForm->value('ThemeFile.ext')) ?>
					<?php echo $this->BcForm->input('ThemeFile.ext', ['type' => 'hidden']) ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpName', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<?php echo $this->BcForm->error('ThemeFile.name') ?>
					<div id="helptextName" class="helptext">
						<ul>
							<li><?php echo __d('baser', 'ファイル名は半角で入力してください。') ?></li>
						</ul>
					</div>
				<?php else: ?>
					<?php echo $this->BcForm->input('ThemeFile.name', ['type' => 'text', 'size' => 30, 'readonly' => 'readonly']) ?> .<?php echo $this->BcForm->value('ThemeFile.ext') ?>
					<?php echo $this->BcForm->input('ThemeFile.ext', ['type' => 'hidden']) ?>
				<?php endif ?>
			</td>
		</tr>
		<?php if ($this->request->action == 'admin_add' || (($this->request->action == 'admin_edit' || $this->request->action == 'admin_view') && in_array($this->request->data['ThemeFile']['type'], ['text', 'image']))): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('ThemeFile.contents', __d('baser', '内容')) ?></th>
				<td class="col-input">
					<?php if (($this->request->action == 'admin_edit' || $this->request->action == 'admin_view') && $this->request->data['ThemeFile']['type'] == 'image'): ?>
						<div class="align-center" style="margin:20px auto">
							<?php $this->BcBaser->link(
								$this->BcBaser->getImg(array_merge(['action' => 'img_thumb', 550, 550, $theme, $plugin, $type], explode('/', $path)), ['alt' => basename($path)]), array_merge(['action' => 'img', $theme, $plugin, $type], explode('/', $path)), ['rel' => 'colorbox', 'title' => basename($path)]
							); ?>
						</div>
					<?php elseif ($this->request->action == 'admin_add' || $this->request->data['ThemeFile']['type'] == 'text'): ?>
						<?php if ($this->request->action != 'admin_view'): ?>
							<?php echo $this->BcForm->input('ThemeFile.contents', ['type' => 'textarea', 'cols' => 80, 'rows' => 30]) ?>
							<?php echo $this->BcForm->error('ThemeFile.contents') ?>
						<?php else: ?>
							<?php echo $this->BcForm->input('ThemeFile.contents', ['type' => 'textarea', 'cols' => 80, 'rows' => 30, 'readonly' => 'readonly']) ?>
						<?php endif ?>
					<?php endif ?>
				</td>
			</tr>
		<?php endif ?>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
	<?php if ($this->request->action == 'admin_add'): ?>
		<?php $this->BcBaser->link(__d('baser', '一覧に戻る'), array_merge(['action' => 'index', $theme, $plugin, $type], $parentPrams), ['class' => 'btn-gray button']); ?>
		<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
	<?php elseif ($this->request->action == 'admin_edit'): ?>
		<?php $this->BcBaser->link(__d('baser', '一覧に戻る'), array_merge(['action' => 'index', $theme, $plugin, $type], $parentPrams), ['class' => 'btn-gray button']); ?>
		<?php if ($isWritable): ?>
			<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
			<?php $this->BcBaser->link(__d('baser', '削除'), array_merge(['action' => 'del', $theme, $plugin, $type], $params), ['class' => 'submit-token button'], sprintf(__d('baser', '%s を本当に削除してもいいですか？'), basename($path)), false) ?>
		<?php endif ?>
	<?php else: ?>
		<?php $this->BcBaser->link(__d('baser', '一覧に戻る'), array_merge(['action' => 'index', $theme, $plugin, $type], $parentPrams), ['class' => 'btn-gray button']); ?>
		<?php // プラグインのアセットの場合はコピーできない ?>
		<?php if (!$safeModeOn): ?>
			<?php //if($theme == 'core' && !(($type == 'css' || $type == 'js' || $type == 'img') && $plugin)): ?>
			<?php // テーマ編集が許可されていない場合コピー不可 ?>
			<?php if ($theme == 'core' && Configure::read('BcApp.allowedThemeEdit')): ?>
				<?php $this->BcBaser->link(__d('baser', '現在のテーマにコピー'), array_merge(['action' => 'copy_to_theme', $theme, $plugin, $type], explode('/', $path)), ['class' => 'submit-token btn-red button'], sprintf(__d('baser', "本当に現在のテーマ「 %s 」にコピーしてもいいですか？\n既に存在するファイルは上書きされます。"), Inflector::camelize($siteConfig['theme']))); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php echo __d('baser', '機能制限のセーフモードで動作していますので、現在のテーマへのコピーはできません。') ?>
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>
