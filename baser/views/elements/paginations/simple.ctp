<?php
/* SVN FILE: $Id$ */
/**
 * ページネーションシンプル
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
?>
<?php $paginator->options = array('url' => $this->passedArgs) ?>
<?php if((int)$paginator->counter(array('format'=>'%pages%')) > 1): ?>
<div class="pagination">
	<?php echo $paginator->prev('<', null, null,  array('class'=>'disabled','tag'=>'span')) ?>
	<span class="page-numbers">
		<?php echo $paginator->numbers(array('separator'=>'')) ?>
	</span>
	<?php echo $paginator->next('>', null, null, array('class'=>'disabled','tag'=>'span')) ?>
</div>
<?php endif; ?>