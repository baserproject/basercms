<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ローカルナビゲーションウィジェット
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
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if (!isset($this->BcPage)) {
	return;
}
$pageCategory = $this->BcPage->getCategory();
?>
<?php if ($pageCategory): ?>
<article class="mainWidth widget-local-navi-<?php echo $id ?>">
<?php if ($use_title): ?>
<h2 class="fontawesome-circle-arrow-down"><?php echo $pageCategory['title'] ?></h2>
<?php endif ?>
<?php $this->BcBaser->element('page_list', array('categoryId' => $pageCategory['id'])) ?>
</article>
<?php endif; ?>
