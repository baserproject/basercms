<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ローカルナビゲーションウィジェット
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
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