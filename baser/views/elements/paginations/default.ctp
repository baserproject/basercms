<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ページネーション標準
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
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