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
?>


<?php foreach($sites as $site): ?>
<h3><?php echo $site['Site']['display_name'] ?></h3>
<ul style="margin-bottom:15px;">
	<li>公開中： <?php echo $site['published'] ?> ページ<br />
		非公開： <?php echo $site['unpublished'] ?> ページ<br />
		合　計： <?php echo $site['total'] ?> ページ
	</li>
</ul>
<?php endforeach ?>