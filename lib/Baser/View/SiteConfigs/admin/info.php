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

/**
 * [管理画面] サイト設定 フォーム
 */
?>


<h2><?php echo __d('baser', 'baserCMS環境') ?></h2>

<ul class="section">
	<li><?php echo __d('baser', '設置フォルダ') ?>： <?php echo ROOT . DS ?></li>
	<li><?php echo __d('baser', 'セーフモード') ?>：<?php if ($safeModeOn): ?>On<?php else: ?>Off<?php endif ?>
	<li><?php echo __d('baser', 'データベース') ?>： <?php echo $datasource ?></li>
	<li><?php echo __d('baser', 'baserCMSバージョン') ?>： <?php echo $baserVersion ?></li>
	<li><?php echo __d('baser', 'CakePHPバージョン') ?>： <?php echo $cakeVersion ?></li>
</ul>

<h2><?php echo __d('baser', 'PHP環境') ?></h2>

<iframe src="<?php $this->BcBaser->url(['action' => 'phpinfo']) ?>" class="phpinfo" width="100%" height="100%"
		style="min-height:600px"></iframe>
