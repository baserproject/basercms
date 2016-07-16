<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			SingleBlog.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<table class="form-table">
	<tr>
		<th><?php echo $this->BcForm->label('title', 'タイトル') ?></th>
		<td><?php echo $this->BcForm->input('title', array('type' => 'text', 'size' => 20)) ?></td>
	</tr>
	<tr>
		<th><?php echo $this->BcForm->label('content', '本文') ?></th>
		<td><?php echo $this->BcForm->input('content', array('type' => 'textarea', 'cols' => 20, 'rows' => 14)) ?></td>
	</tr>
</table>