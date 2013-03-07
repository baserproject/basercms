<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマファイル登録・編集
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
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

<?php if($this->action == 'admin_add'): ?>
<?php echo $bcForm->create('ThemeFile', array('id' => 'ThemeFileForm', 'url' => array('action' => 'add', $theme, $plugin, $type, $path))) ?>
<?php elseif($this->action == 'admin_edit'): ?>
<?php echo $bcForm->create('ThemeFile', array('id' => 'ThemeFileForm', 'url' => array('action' => 'edit', $theme, $plugin, $type, $path))) ?>
<?php endif ?>

<?php echo $bcForm->input('ThemeFile.parent', array('type'=>'hidden')) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<tr>
			<th class="col-head"><?php echo $bcForm->label('ThemeFile.name', 'ファイル名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
	<?php if($this->action != 'admin_view'): ?>
				<?php echo $bcForm->input('ThemeFile.name', array('type' => 'text', 'size' => 30, 'maxlength' => 255)) ?> 
				<?php if($bcForm->value('ThemeFile.ext')): ?>.<?php endif ?>
				<?php echo $bcForm->value('ThemeFile.ext') ?>
				<?php echo $bcForm->input('ThemeFile.ext', array('type' => 'hidden')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('ThemeFile.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>ファイル名は半角で入力してください。</li>
					</ul>
				</div>
	<?php else: ?>
				<?php echo $bcForm->input('ThemeFile.name', array('type' => 'text', 'size' => 30, 'readonly' => 'readonly')) ?> .<?php echo $bcForm->value('ThemeFile.ext') ?>
				<?php echo $bcForm->input('ThemeFile.ext', array('type' => 'hidden')) ?>
	<?php endif ?>
			</td>
		</tr>
	<?php if($this->action == 'admin_add' || (($this->action == 'admin_edit' || $this->action == 'admin_view') && in_array($this->data['ThemeFile']['type'], array('text', 'image')))): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('ThemeFile.contents', '内容') ?></th>
			<td class="col-input">
	<?php if(($this->action == 'admin_edit' || $this->action == 'admin_view') && $this->data['ThemeFile']['type'] == 'image'): ?>
				<div class="align-center" style="margin:20px auto">
					<?php $bcBaser->link(
							$bcBaser->getImg(array('action' => 'img_thumb', 550, 550, $theme, $plugin, $type, $path), array('alt' => basename($path))),
							array('action' => 'img', $theme, $plugin, $type, $path),
							array('rel' => 'colorbox', 'title' => basename($path))
					) ?>
				</div>
	<?php elseif($this->action == 'admin_add' || $this->data['ThemeFile']['type'] == 'text'): ?>
		<?php if($this->action != 'admin_view'): ?>
				<?php echo $bcForm->input('ThemeFile.contents', array('type' => 'textarea', 'cols' => 80, 'rows' => 30)) ?>
				<?php echo $bcForm->error('ThemeFile.contents') ?>
			<?php else: ?>
				<?php echo $bcForm->input('ThemeFile.contents',array('type' => 'textarea', 'cols' => 80, 'rows' => 30, 'readonly' => 'readonly')) ?>
		<?php endif ?>
	<?php endif ?>
			</td>
		</tr>
	<?php endif ?>
	</table>
</div>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php $bcBaser->link('一覧に戻る', array('action' => 'index', $theme, $plugin, $type, $path), array('class' => 'btn-gray button')); ?>
<?php else: ?>
	<?php $bcBaser->link('一覧に戻る', array('action' => 'index', $theme, $plugin, $type, dirname($path)), array('class' => 'btn-gray button')); ?>
<?php endif ?>
<?php if($this->action == 'admin_add'): ?>
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php $bcBaser->link('削除', 
			array('action' => 'del', $theme, $plugin, $type , $path) , array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？', basename($path)),false
	) ?>
<?php else: ?>
<?php // プラグインのアセットの場合はコピーできない ?>
	<?php if(!$safeModeOn): ?>
		<?php //if($theme == 'core' && !(($type == 'css' || $type == 'js' || $type == 'img') && $plugin)): ?>
		<?php if($theme == 'core'): ?>
	<?php $bcBaser->link('現在のテーマにコピー',
			array('action' => 'copy_to_theme', $theme, $plugin, $type , $path),
			array('class'=>'btn-red button'),
			'本当に現在のテーマ「'.Inflector::camelize($siteConfig['theme']).'」にコピーしてもいいですか？\n既に存在するファイルは上書きされます。'
	) ?>
		<?php endif ?>
	<?php else: ?>
	機能制限のセーフモードで動作していますので、現在のテーマへのコピーはできません。
	<?php endif ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>