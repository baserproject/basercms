<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ページネーション標準
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
$paginator->options = array('url' => $this->passedArgs);
?>
<div class="pagination">
	<div class="page-result">
		<?php echo $paginator->counter(array('format'=>'結果：　%start%～%end% 件 ／ 総件数：　%count% 件')) ?>
	</div>
	<div class="page-numbers">
		<?php echo $paginator->first('|<') ?>　
		<?php echo $paginator->prev('<<', null, null,  array('class'=>'disabled','tag'=>'span')) ?>　
		<?php echo $paginator->numbers() ?>　
		<?php echo $paginator->next('>>', null, null, array('class'=>'disabled','tag'=>'span')) ?>　
		<?php echo $paginator->last('>|') ?>
	</div>
</div>