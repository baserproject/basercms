<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] サブメニュー
 *
 * $this->BcBaser->subMenu() 経由で呼び出す
 */
?>


<?php if (!empty($subMenuElements)): ?>
	<?php foreach($subMenuElements as $subMenuElement): ?>
		<?php $this->BcBaser->element('submenus' . DS . $subMenuElement) ?>
	<?php endforeach ?>
<?php endif ?>
