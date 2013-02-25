<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] ブログコメント単記事
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php if(!empty($dbData)): ?>
	<?php if($dbData['status']): ?>

<div class="comment" id="Comment<?php echo $dbData['no'] ?>"> <span class="comment-name"> <span style="color:#8ABE08">◆ </span>
		<?php if($dbData['url']): ?>
	<?php echo $bcBaser->link($dbData['name'],$dbData['url'],array('target'=>'_blank')) ?>
		<?php else: ?>
	<?php echo $dbData['name'] ?>
		<?php endif ?>
	</span> <br />
	<span class="comment-message"> <?php echo nl2br($dbData['message']) ?> </span>
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#cccccc;background:#cccccc;border:1px solid #cccccc;" />
</div>
	<?php endif ?>
<?php endif ?>