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
 * [ADMIN] コンテンツメニュー
 */
?>


<?php if (!empty($user)): ?>
	<div id="ContentsMenu">
		<ul class="clearfix">
			<?php if (!empty($search)): ?>
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_menu_search.png', ['alt' => __d('baser', '検索')]) . __d('baser', '検索'), 'javascript:void(0)', ['id' => 'BtnMenuSearch']) ?></li>
			<?php endif ?>
			<?php if (!empty($help)): ?>
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_menu_help.png', ['alt' => __d('baser', 'ヘルプ')]) . __d('baser', 'ヘルプ'), 'javascript:void(0)', ['id' => 'BtnMenuHelp']) ?></li>
			<?php endif ?>
			<?php if ($this->BcBaser->isAdminUser()): ?>
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_menu_permission.png', ['alt' => __d('baser', '制限')]) . __d('baser', '制限'), 'javascript:void(0)', ['id' => 'BtnMenuPermission']) ?></li>
			<?php endif ?>
		</ul>
	</div>
<?php
endif ?>
