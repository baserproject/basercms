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


<h1><?php echo $this->request->params['Content']['title'] ?></h1>
<?php if ($children): ?>
	<ul>
		<?php foreach($children as $child): ?>
			<li><?php $this->BcBaser->link($child['Content']['title'], $child['Content']['url']) ?></li>
		<?php endforeach ?>
	</ul>
<?php endif ?>
