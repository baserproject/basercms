<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php echo $this->BcForm->create() ?>
<?php echo $this->BcForm->hidden('ContentLink.id') ?>

<table class="form-table">
	<tr>
		<th>リンク先URL</th>
		<td>
			<?php echo $this->BcForm->input('ContentLink.url', array('type' => 'text', 'size' => 60, 'placeholder' => 'http://')) ?><br>
			<?php echo $this->BcForm->error('ContentLink.url') ?>
		</td>
	</tr>
</table>

<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('class' => 'button', 'div' => false)) ?>
</div>
<?php echo $this->BcForm->end() ?>
