<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiPage.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<table class="form-table">
	<tr>
		<th><?php echo $this->BcForm->label('content', 'コンテンツ内容') ?></th>
		<td>
			<?php echo $this->BcForm->input('content', array('type' => 'textarea', 'size' => '60')) ?>
			<?php echo $this->BcForm->error('content') ?>
		</td>
	</tr>
</table>