<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログタグ フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<!-- form -->
<?php echo $formEx->create('BlogTag') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogTag.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $formEx->value('BlogTag.id') ?>
				<?php echo $formEx->input('BlogTag.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogTag.name', 'ブログタグ名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogTag.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $formEx->error('BlogTag.name') ?>
			</td>
		</tr>
	</table>
</div>
<!-- button -->
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->submit('更新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削除',
			array('action' => 'delete', $formEx->value('BlogTag.id')),
			array('class' => 'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('BlogTag.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>