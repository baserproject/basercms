<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */
?>


<?php echo $this->BcForm->create() ?>
<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcForm->hidden('ContentLink.id') ?>

<table class="form-table">
	<tr>
		<th><?php echo __d('baser', 'リンク先URL') ?></th>
		<td>
			<?php echo $this->BcForm->input('ContentLink.url', ['type' => 'text', 'size' => 60, 'placeholder' => 'http://']) ?>
			<br>
			<?php echo $this->BcForm->error('ContentLink.url') ?>
		</td>
	</tr>
	<?php echo $this->BcForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['class' => 'button', 'div' => false]) ?>
</div>
<?php echo $this->BcForm->end() ?>
