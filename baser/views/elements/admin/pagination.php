<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ページネーション
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(empty($paginator)) {
	return;
}
if(!isset($modules)) {
	$modules = 8;
}
$paginator->options = array('url' => $this->passedArgs);
$pageCount = 0;
if(isset($paginator->params['paging'][$paginator->defaultModel()]['pageCount'])) {
	$pageCount = $paginator->params['paging'][$paginator->defaultModel()]['pageCount'];
}
?>
<div class="pagination clearfix">
	
<?php if($pageCount > 1): ?>
	<div class="page-numbers">
		<?php echo $paginator->prev('< 前へ', array('class'=>'prev'), null, array('class'=>'prev disabled')) ?>
		<?php echo $html->tag('span', $paginator->numbers(array('separator' => '', 'class' => 'number', 'modulus' => $modules), array('class' => 'page-numbers'))) ?>
		<?php echo $paginator->next('次へ >', array('class'=>'next'), null, array('class'=>'next disabled')) ?>
	</div>
<?php endif ?>
	<div class="page-result">
		<?php echo $paginator->counter(array('format'=>'<span class="page-start-num">%start%</span>～<span class="page-end-num">%end%</span> 件 ／ <span class="page-total-num">%count%</span> 件')) ?>
	</div>
</div>