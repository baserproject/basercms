<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.4.0
 * @license			https://basercms.net/license/index.html
 */

/**
 * ヘッダー
 *
 * BcBaserHelper::header() で呼び出す
 * （例）<?php $this->BcBaser->header() ?>
 *
 * @var BcAppView $this
 */
$isSmartphone = $this->request->is('smartphone');
?>


<header class="bs-header">
	<div class="bs-header-inner">
		<?php $this->BcBaser->logo(['class' => 'bs-header-inner__logo']) ?>
	</div>

	<script>
		let isOpen = false
		function clickMenuBtn() {
			if(isOpen) {
				isOpen = false;
				$("#bs-menu-btn").removeClass("bs-open")
				$("#bs-menu-content").removeClass("bs-open")
			} else {
				isOpen = true;
				$("#bs-menu-btn").addClass("bs-open")
				$("#bs-menu-content").addClass("bs-open")
			}
		}
	</script>

	<div class="bs-header-btn" id="bs-menu-btn" onclick="clickMenuBtn()">
		<span></span>
		<span></span>
		<span></span>
	</div>

	<!-- /Elements/global_menu.php -->
	<nav class="bs-header-nav<?php echo ($isSmartphone)? '' : ' use-mega-menu' ?>" id="bs-menu-content"><?php $this->BcBaser->globalMenu(2) ?></nav>

</header>
