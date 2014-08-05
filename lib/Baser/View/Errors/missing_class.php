<?php
/**
 * [PUBLISH] missing class
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>

<div id="errorPage">
	<h2>クラスが見つかりません</h2>
	<p class="error"> <strong>
			<?php echo __('Error'); ?>
			: </strong> <?php echo " <em>{$className}</em>" ?> クラスが見つかりません。 </p>
	<p class="error"> <strong>
			<?php echo __('Error'); ?>
			: </strong> <?php echo " <em>{$className}</em>" ?> クラスを定義するか、読み込まれているか確認してください。 </p>
	<?php if ($notice): ?>
		<p class="notice"> <strong>
				<?php echo __('Notice'); ?>
				: </strong> <?php echo $notice ?> </p>
	<?php endif ?>
</div>
