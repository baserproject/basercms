<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] コンテンツメニュー
 */
?>


<?php if (!empty($user)): ?>
	<div id="ContentsMenu" class="bca-content-menu">
		<ul>
			<?php if (!empty($help)): ?>
				<li>
					<?php $this->BcBaser->link(' ヘルプ', 'javascript:void(0)', array('id' => 'BtnMenuHelp', 'class'=>'bca-icon--help')) ?></li>
			<?php endif ?>
			<?php if ($this->BcBaser->isAdminUser()): ?>
				<li>
					<?php $this->BcBaser->link(' 制限', 'javascript:void(0)', array('id' => 'BtnMenuPermission', 'class'=>'bca-icon--permission')) ?></li>
			<?php endif ?>
		</ul>
	</div>
	<?php
 endif ?>