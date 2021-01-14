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


<script>
	$(function () {
		$.bcDashboard.ajax('/' + $.bcUtil.adminPrefix + '/contents/ajax_contents_info', '#ContentInfo');
	});
</script>

<h2><?php echo __d('baser', 'コンテンツ情報') ?></h2>
<div id="ContentInfo"></div>
