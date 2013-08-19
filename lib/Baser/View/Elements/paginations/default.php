<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ページネーション標準
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
?>
<div class="pagination">
	<div class="page-result">
		<?php echo $this->Paginator->counter(array('format'=>'結果：　%start%～%end% 件 ／ 総件数：　%count% 件')) ?>
	</div>
	<div class="page-numbers">
		<?php echo $this->Paginator->first('|<') ?>　
		<?php echo $this->Paginator->prev('<<', null, null,  array('class'=>'disabled','tag'=>'span')) ?>　
		<?php echo $this->Paginator->numbers() ?>　
		<?php echo $this->Paginator->next('>>', null, null, array('class'=>'disabled','tag'=>'span')) ?>　
		<?php echo $this->Paginator->last('>|') ?>
	</div>
</div>