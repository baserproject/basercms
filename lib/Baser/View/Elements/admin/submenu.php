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
 * [PUBLISH] サブメニュー
 */
if (!empty($user)) {
	$elementPath = 'submenus' . DS;
	if (!empty($subMenuElements)) {
		?>
		<div id="SubMenu" class="clearfix">
			<table class="sub-menu">
				<?php
				foreach($subMenuElements as $subMenuElement) {
					$plugin = '';
					if (strpos($subMenuElement, '.') !== false) {
						list($plugin, $subMenuElement) = explode('.', $subMenuElement);
						$plugin .= '.';
					}
					$this->BcBaser->element($plugin . $elementPath . $subMenuElement);
				}
				?>
			</table>
		</div>
		<?php
	}
}
