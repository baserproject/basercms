<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] missing class
 */
?>


<div id="errorPage">
	<h2><?php echo __d('baser', 'クラスが見つかりません')?></h2>
	<p class="error"> <strong>
			<?php echo __d('baser', 'エラー'); ?>
			: </strong> <?php echo " <em>{$className}</em>" ?> クラスが見つかりません。 </p>
	<p class="error"> <strong>
			<?php echo __d('baser', 'エラー'); ?>
			: </strong> <?php echo " <em>{$className}</em>" ?> クラスを定義するか、読み込まれているか確認してください。 </p>
	<?php if ($notice): ?>
		<p class="notice"> <strong>
				<?php echo __d('baser', '注意'); ?>
				: </strong> <?php echo $notice ?> </p>
	<?php endif ?>
</div>
