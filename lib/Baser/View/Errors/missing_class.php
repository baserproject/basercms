<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] missing class
 */
?>


<div id="errorPage">
	<h2><?php echo __d('baser', 'クラスが見つかりません') ?></h2>
	<p class="error"><strong>
			<?php echo __d('baser', 'エラー'); ?>
			: </strong> <?php echo " <em>{$className}</em>" ?> クラスが見つかりません。 </p>
	<p class="error"><strong>
			<?php echo __d('baser', 'エラー'); ?>
			: </strong> <?php echo " <em>{$className}</em>" ?> クラスを定義するか、読み込まれているか確認してください。 </p>
	<?php if ($notice): ?>
		<p class="notice"><strong>
				<?php echo __d('baser', '注意'); ?>
				: </strong> <?php echo $notice ?> </p>
	<?php endif ?>
</div>
