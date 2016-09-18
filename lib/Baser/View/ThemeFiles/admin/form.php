<?php
/**
 * [ADMIN] テーマファイル登録・編集
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$params = explode('/', $path);
?>


<script type="text/javascript">
$(window).load(function() {
	$("#ThemeFileName").focus();
});
</script>

<!-- current -->
<div class="em-box align-left">
	現在の位置：<?php echo $currentPath ?>
</div>

<?php if ($this->request->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('ThemeFile', array('id' => 'ThemeFileForm', 'url' => array_merge(array('action' => 'add'), array($theme, $plugin, $type), explode('/', $path)))) ?>
<?php elseif ($this->request->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('ThemeFile', array('id' => 'ThemeFileForm', 'url' => array_merge(array('action' => 'edit'), array($theme, $plugin, $type), explode('/', $path)))) ?>
<?php endif ?>

<?php echo $this->BcForm->input('ThemeFile.parent', array('type' => 'hidden')) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('ThemeFile.name', 'ファイル名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php if ($this->request->action != 'admin_view'): ?>
					<?php echo $this->BcForm->input('ThemeFile.name', array('type' => 'text', 'size' => 30, 'maxlength' => 255)) ?> 
					<?php if ($this->BcForm->value('ThemeFile.ext')): ?>.<?php endif ?>
					<?php echo $this->BcForm->value('ThemeFile.ext') ?>
					<?php echo $this->BcForm->input('ThemeFile.ext', array('type' => 'hidden')) ?>
					<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
					<?php echo $this->BcForm->error('ThemeFile.name') ?>
					<div id="helptextName" class="helptext">
						<ul>
							<li>ファイル名は半角で入力してください。</li>
						</ul>
					</div>
				<?php else: ?>
					<?php echo $this->BcForm->input('ThemeFile.name', array('type' => 'text', 'size' => 30, 'readonly' => 'readonly')) ?> .<?php echo $this->BcForm->value('ThemeFile.ext') ?>
					<?php echo $this->BcForm->input('ThemeFile.ext', array('type' => 'hidden')) ?>
				<?php endif ?>
			</td>
		</tr>
		<?php if ($this->request->action == 'admin_add' || (($this->request->action == 'admin_edit' || $this->request->action == 'admin_view') && in_array($this->request->data['ThemeFile']['type'], array('text', 'image')))): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('ThemeFile.contents', '内容') ?></th>
				<td class="col-input">
					<?php if (($this->request->action == 'admin_edit' || $this->request->action == 'admin_view') && $this->request->data['ThemeFile']['type'] == 'image'): ?>
						<div class="align-center" style="margin:20px auto">
							<?php $this->BcBaser->link(
								$this->BcBaser->getImg(array_merge(array('action' => 'img_thumb', 550, 550, $theme, $plugin, $type), explode('/', $path)), array('alt' => basename($path))), array_merge(array('action' => 'img', $theme, $plugin, $type), explode('/', $path)), array('rel' => 'colorbox', 'title' => basename($path))
							); ?>
						</div>
					<?php elseif ($this->request->action == 'admin_add' || $this->request->data['ThemeFile']['type'] == 'text'): ?>
						<?php if ($this->request->action != 'admin_view'): ?>
							<?php echo $this->BcForm->input('ThemeFile.contents', array('type' => 'textarea', 'cols' => 80, 'rows' => 30)) ?>
							<?php echo $this->BcForm->error('ThemeFile.contents') ?>
						<?php else: ?>
							<?php echo $this->BcForm->input('ThemeFile.contents', array('type' => 'textarea', 'cols' => 80, 'rows' => 30, 'readonly' => 'readonly')) ?>
						<?php endif ?>
					<?php endif ?>
				</td>
			</tr>
		<?php endif ?>
	</table>
</div>
<div class="submit">
	<?php if ($this->request->action == 'admin_add'): ?>
		<?php $this->BcBaser->link('一覧に戻る', array_merge(array('action' => 'index', $theme, $plugin, $type), $params), array('class' => 'btn-gray button')); ?>
	<?php else: ?>
		<?php $this->BcBaser->link('一覧に戻る', array_merge(array('action' => 'index', $theme, $plugin, $type), explode('/', dirname($path))), array('class' => 'btn-gray button')); ?>
	<?php endif ?>
	<?php if ($this->request->action == 'admin_add'): ?>
		<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php elseif ($this->request->action == 'admin_edit'): ?>
		<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
		<?php
		$this->BcBaser->link('削除', array_merge(array('action' => 'del', $theme, $plugin, $type), $params), array('class' => 'submit-token button'), sprintf('%s を本当に削除してもいいですか？', basename($path)), false
		)
		?>
	<?php else: ?>
		<?php // プラグインのアセットの場合はコピーできない ?>
		<?php if (!$safeModeOn): ?>
			<?php //if($theme == 'core' && !(($type == 'css' || $type == 'js' || $type == 'img') && $plugin)): ?>
			<?php if ($theme == 'core'): ?>
				<?php $this->BcBaser->link('現在のテーマにコピー', array_merge(array('action' => 'copy_to_theme', $theme, $plugin, $type), explode('/', $path)), array('class' => 'submit-token btn-red button'), '本当に現在のテーマ「' . Inflector::camelize($siteConfig['theme']) . "」にコピーしてもいいですか？\n既に存在するファイルは上書きされます。"); ?>
			<?php endif; ?>
		<?php else: ?>
			機能制限のセーフモードで動作していますので、現在のテーマへのコピーはできません。
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>
