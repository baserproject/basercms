<?php
/**
 * [PUBLISH] サブメニュー
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 * @deprecated since version 3.0.3
 */

trigger_error(deprecatedMessage('テンプレート：submenu.php', '3.0.3', '3.1.0', '$this->BcBaser->subMenu() を利用してください。'));

$elementPath = 'submenus' . DS;
if (!empty($subMenuElements)) {
	foreach ($subMenuElements as $subMenuElement) {
		$this->BcBaser->element($elementPath . $subMenuElement);
	}
} else {
	echo '&nbsp';
}
