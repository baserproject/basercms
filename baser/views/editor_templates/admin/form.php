<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] エディタテンプレートー登録・編集
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<?php $bcBaser->css('ckeditor/editor', array('inline' => true)); ?>
<?php echo $bcForm->create('EditorTemplate', array('type' => 'file')) ?>

<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('EditorTemplate.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('EditorTemplate.id') ?>
				<?php echo $bcForm->input('EditorTemplate.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('EditorTemplate.name', 'テンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('EditorTemplate.name', array('type' => 'text', 'size' => 20, 'maxlength' => 50)) ?>
				<?php echo $bcForm->error('EditorTemplate.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('EditorTemplate.image', 'アイコン画像') ?></th>
			<td class="col-input">
				<?php echo $bcUpload->file('EditorTemplate.image') ?>
				<?php echo $bcForm->error('EditorTemplate.image') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('EditorTemplate.description', '説明文') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('EditorTemplate.description', array('type' => 'textarea', 'cols' => 60, 'rows' => 2)) ?>
				<?php echo $bcForm->error('EditorTemplate.description') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('EditorTemplate.html', 'コンテンツ') ?></th>
			<td class="col-input">
				<?php echo $bcForm->ckeditor('EditorTemplate.html', array('cols' => 60, 'rows' => 20), array('width' => 'auto', 'useTemplates' => false)) ?>
				<?php echo $bcForm->error('EditorTemplate.html') ?>
				<?php echo $bcForm->error('EditorTemplate.html') ?>
			</td>
		</tr>
	</table>
</div>

<div class="submit section">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if ($this->action == 'admin_edit'): ?>
	<?php $bcBaser->link('削除', 
				array('action' => 'delete', $bcForm->value('EditorTemplate.id')),
				array('class' => 'button'),
				sprintf('%s を本当に削除してもいいですか？', $bcForm->value('EditorTemplate.name')), false); ?>
	<?php endif ?>
</div>

<?php echo $bcForm->end() ?>