<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] ブログ
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
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$baser->setDescription($blog->getDescription());
?>

<!-- title -->
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;"><?php echo $baser->getContentsTitle(); ?></span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<br />

<!-- description -->
<div><?php $blog->description() ?></div>
<br />

<!-- pagination -->
<?php $baser->pagination() ?>
<br />
<br />

<!-- list -->
<?php if(!empty($posts)): ?>
	<?php foreach($posts as $post): ?>
<span style="color:#8ABE08">◆</span>
<?php $blog->postTitle($post) ?>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
<br />
<?php $blog->postContent($post,false,true) ?>
<br />
<p align="right">
	<?php $blog->category($post) ?>
	<br />
	<?php $blog->postDate($post) ?>
	<br />
	<?php $blog->author($post) ?>
</p>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<br />
	<?php endforeach; ?>
<?php else: ?>
<p class="no-data">記事がありません。</p>
<?php endif; ?>

<!-- pagination -->
<?php echo $baser->pagination() ?>