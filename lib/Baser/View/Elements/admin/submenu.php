<?php
/**
 * [PUBLISH] サブメニュー
 * 
 * PHP versions 4 and 5
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
if (!empty($user)) {
	$elementPath = 'submenus' . DS;
	if (!empty($subMenuElements)) {
		?>
		<div id="SubMenu" class="clearfix">
			<table class="sub-menu">
				<?php
				foreach ($subMenuElements as $subMenuElement) {
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
