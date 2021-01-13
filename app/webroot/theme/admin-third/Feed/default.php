<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Feed.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] フィード
 */
?>


<?php if (!empty($items)): ?>
	<div class="bca-feed">
		<ul class="bca-feed__list">
			<?php foreach($items as $key => $item): ?>
				<?php $no = sprintf('%02d', $key + 1) ?>
				<?php if ($key == 0): ?>
					<?php $class = ' class="bca-feed__list-item first feed' . $no . '"' ?>
				<?php elseif ($key == count($items) - 1): ?>
					<?php $class = ' class="bca-feed__list-item last feed' . $no . '"' ?>
				<?php else: ?>
					<?php $class = ' class="bca-feed__list-item feed' . $no . '"' ?>
				<?php endif ?>
				<li<?php echo $class ?>><span
						class="date bca-feed__list-item-date"><?php echo date("Y.m.d", strtotime($item['pubDate']['value'])); ?></span><br/>
					<span class="title bca-feed__list-item-title"><a
							href="<?php echo $item['link']['value']; ?>"><?php echo $item['title']['value']; ?></a></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php else: ?>
	<div class="bca-feed"><p style="text-align:center">－</p></div>
<?php endif; ?>
