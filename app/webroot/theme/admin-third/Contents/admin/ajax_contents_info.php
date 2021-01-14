<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
?>


<?php if ($sites): ?>
	<div class="bca-content-info">
		<?php foreach($sites as $site): ?>
			<h3 class="bca-content-info__title"><?php echo h($site['Site']['display_name']) ?></h3>
			<ul class="bca-content-info__list">
				<li class="bca-content-info__list-item">
					<?php echo sprintf(__d('baser', '公開中： %s ページ'), $site['published']) ?><br>
					<?php echo sprintf(__d('baser', '非公開： %s ページ'), $site['unpublished']) ?><br>
					<?php echo sprintf(__d('baser', '合計： %s ページ'), $site['total']) ?>
				</li>
			</ul>
		<?php endforeach ?>
	</div>
<?php endif ?>
