<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ページネーションシンプル
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
if(empty($paginator)) {
	return;
}
if(!isset($modules)) {
	$modules = 8;
}
?>
<?php $paginator->options = array('url' => $this->passedArgs) ?>
<?php if((int)$paginator->counter(array('format'=>'%pages%')) > 1): ?>
<div class="pagination">
<?php echo $paginator->prev('< 前へ', array('class'=>'prev'), null, array('class'=>'disabled')) ?>
<?php echo $html->tag('span', $paginator->numbers(array('separator' => '', 'class' => 'number', 'modulus' => $modules), array('class' => 'page-numbers'))) ?>
<?php echo $paginator->next('次へ >', array('class'=>'next'), null, array('class'=>'disabled')) ?>
</div>
<?php endif; ?>