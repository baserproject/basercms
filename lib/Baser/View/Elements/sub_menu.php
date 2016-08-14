<?php
/**
 * [PUBLISH] サブメニュー
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * $this->BcBaser->subMenu() 経由で呼び出す
 */
?>


<?php if(!empty($subMenuElements)): ?>
	<?php foreach ($subMenuElements as $subMenuElement): ?>
		<?php $this->BcBaser->element('submenus' . DS . $subMenuElement) ?>
	<?php endforeach ?>
<?php endif ?>
