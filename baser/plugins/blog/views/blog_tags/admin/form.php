<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログタグ フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(window).load(function() {
	$("#BlogTagName").focus();
});
</script>


<!-- form -->
<?php echo $bcForm->create('BlogTag') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogTag.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('BlogTag.id') ?>
				<?php echo $bcForm->input('BlogTag.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogTag.name', 'ブログタグ名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogTag.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $bcForm->error('BlogTag.name') ?>
			</td>
		</tr>
	</table>
</div>
<!-- button -->
<div class="submit">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if($this->action == 'admin_edit'): ?>
	<?php $bcBaser->link('削除',
			array('action' => 'delete', $bcForm->value('BlogTag.id')),
			array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？', $bcForm->value('BlogTag.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>