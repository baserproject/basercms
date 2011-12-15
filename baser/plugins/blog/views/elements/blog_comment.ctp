<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログコメント単記事
 * 
 * Ajax でも利用される
 * 
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<?php if(!empty($dbData)): ?>
	<?php if($dbData['status']): ?>
<div class="comment" id="Comment<?php echo $dbData['no'] ?>">
	<span class="comment-name">≫
		<?php if($dbData['url']): ?>
		<?php echo $baser->link($dbData['name'], $dbData['url'], array('target' => '_blank')) ?>
		<?php else: ?>
		<?php echo $dbData['name'] ?>
		<?php endif ?>
	</span><br />
	<span class="comment-message"><?php echo nl2br($dbData['message']) ?></span>
</div>
	<?php endif ?>
<?php endif ?>