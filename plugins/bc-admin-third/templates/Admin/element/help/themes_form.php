<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマ編集　ヘルプ
 */
?>


<p><?php echo __d('baser', 'テーマ情報の編集が行えます。編集内容は、テーマフォルダ名と、テーマ設定ファイルに反映されます。') ?><br/>
	<small><?php echo __d('baser', 'テーマフォルダ') ?>：<?php echo WWW_ROOT . 'theme' . DS . $theme . DS ?></small><br/>
	<small><?php echo __d('baser', 'テーマ設定ファイル') ?>
		：<?php echo WWW_ROOT . 'theme' . DS . $theme . DS . 'config.php' ?></small>
</p>
