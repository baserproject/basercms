<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * ヘッダー
 *
 * BcBaserHelper::header() で呼び出す
 * （例）<?php $this->BcBaser->header() ?>
 *
 * @var \BaserCore\View\BcFrontAppView $this
 */
$isSmartphone = $this->getRequest()->is('smartphone');
?>


<header class="bs-header">
	<div class="bs-header__inner">
		<?php $this->BcBaser->logo(['class' => 'bs-header__logo']) ?>
	</div>

	<div class="bs-header__menu-button" id="BsMenuBtn">
		<span></span>
		<span></span>
		<span></span>
	</div>

	<nav class="bs-header__nav<?php echo ($isSmartphone)? '' : ' use-mega-menu' ?>" id="BsMenuContent">
		<!-- /Elements/global_menu.php -->
		<?php if(\BaserCore\Utility\BcUtil::isInstalled()): ?>
		<?php $this->BcBaser->globalMenu(2) ?>
		<?php endif ?>
	</nav>

</header>
