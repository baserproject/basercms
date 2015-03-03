<?php
/**
 * [ADMIN] コンテンツメニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php if (!empty($user)): ?>
	<div id="ContentsMenu">
		<ul class="clearfix">
			<?php if (!empty($search)): ?>
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_menu_search.png', array('alt' => '検索', 'width' => 50, 'height' => '18', 'class' => 'btn')), 'javascript:void(0)', array('id' => 'BtnMenuSearch')) ?></li>
			<?php endif ?>
			<?php if (!empty($help)): ?>
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_menu_help.png', array('alt' => 'ヘルプ', 'width' => 60, 'height' => '18', 'class' => 'btn')), 'javascript:void(0)', array('id' => 'BtnMenuHelp')) ?></li>
			<?php endif ?>
			<?php if ($this->BcBaser->isAdminUser()): ?>
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_menu_permission.png', array('alt' => '制限設定', 'width' => 50, 'height' => '18', 'class' => 'btn')), 'javascript:void(0)', array('id' => 'BtnMenuPermission')) ?></li>
			<?php endif ?>
		</ul>
	</div>
	<?php
 endif ?>