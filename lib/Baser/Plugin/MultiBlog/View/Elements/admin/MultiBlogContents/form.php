<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.View
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<table class="form-table">
	<tr>
		<th><?php echo $this->BcForm->label('content', 'ブログ内容') ?></th>
		<td>
			<?php echo $this->BcForm->input('content', array('type' => 'textarea', 'cols' => '20', 'rows' => 15)) ?>
			<?php echo $this->BcForm->error('content') ?>
		</td>
	</tr>
</table>