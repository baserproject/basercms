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
	<div id="ContentsMenu" class="bca-content-menu">
		<ul>
			<li class="bca-content-menu__item">
				<?php # TODO: button要素に変更 ?>
				<?php $this->BcBaser->link(__d('baser', 'お気に入りに追加'), 'javascript:void(0)', ['id' => 'BtnFavoriteAdd', 'data-bca-fn' => 'BtnFavoriteAdd', 'class' => 'bca-content-menu__link bca-icon--plus-square']) ?></li>
			<?php if (!empty($help)): ?>
				<li class="bca-content-menu__item">
					<?php # TODO: button要素に変更 ?>
					<?php $this->BcBaser->link(__d('baser', 'ヘルプ'), 'javascript:void(0)', ['id' => 'BtnMenuHelp', 'class' => 'bca-content-menu__link bca-icon--help']) ?></li>
			<?php endif ?>
			<?php if ($this->BcBaser->isAdminUser()): ?>
				<li class="bca-content-menu__item">
					<?php # TODO: button要素に変更 ?>
					<?php $this->BcBaser->link(__d('baser', '制限'), 'javascript:void(0)', ['id' => 'BtnMenuPermission', 'class' => 'bca-content-menu__link bca-icon--permission']) ?></li>
			<?php endif ?>
		</ul>
	</div>
<?php
endif ?>
