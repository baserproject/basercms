<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ローカルナビゲーションウィジェット
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(!isset($page)) {
	return;
}
$pageCategory = $page->getCategory();
?>
<?php if($pageCategory): ?>
<div class="widget widget-local-navi widget-local-navi-<?php echo $id ?>">
	<?php if($use_title): ?>
	<h2><?php echo $pageCategory['title'] ?></h2>
	<?php endif ?>
	<?php $baser->element('page_list',array('categoryId'=>$pageCategory['id'])) ?>
</div>
<?php endif ?>