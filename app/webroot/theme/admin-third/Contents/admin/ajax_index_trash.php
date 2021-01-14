<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */
header('Content-type: text/html; charset=utf-8');
?>


<?php if ($datas): ?>
	<div id="ContentsTreeList" style="display:none">
		<?php $this->BcBaser->element('contents/index_list_tree'); ?>
	</div>
<?php else: ?>
	<div class="tree-empty"><?php echo __d('baser', 'ゴミ箱は空です') ?></div>
<?php endif ?>
