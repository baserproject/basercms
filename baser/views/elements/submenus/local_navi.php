<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ページカテゴリリスト
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$pageCategory = $bcPage->getCategory();
?>
<?php if($pageCategory): ?>
<div id="local-navi">
	<h2><?php echo $pageCategory['title'] ?></h2>
	<?php $bcBaser->element('page_list',array('categoryId'=>$pageCategory['id'])) ?>
</div>
<?php endif ?>