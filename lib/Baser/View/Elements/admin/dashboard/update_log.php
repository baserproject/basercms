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
 * @var BcAppView $this
 */
$this->BcBaser->i18nScript([
	'alertMessage1' => __d('baser', '処理に失敗しました。')
]);
$this->BcBaser->js('admin/dblogs/ajax_index', false);
?>


<h2><?php echo __d('baser', '最近の動き') ?></h2>
<div id="DblogList"></div>
