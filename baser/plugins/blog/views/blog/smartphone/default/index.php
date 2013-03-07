<?php
/* SVN FILE: $Id$ */
/**
 * [SMARTPHONE] ブログトップ
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
$bcBaser->setDescription($blog->getDescription());
?>

<!-- title -->
<h2 class="contents-head">
	<?php $blog->title() ?>
</h2>

<!-- description -->
<?php if($blog->descriptionExists()): ?>
<section class="blog-description">
	<?php $blog->description() ?>
</section>
<?php endif ?>

<section class="box news">
<!-- list -->
<?php if(!empty($posts)): ?>
<ul>
	<?php foreach($posts as $post): ?>
<li><?php $blog->postLink($post, '<span class="date">'.$blog->getPostDate($post).'</span><br />'.$blog->getPostTitle($post)) ?></li>
	<?php endforeach; ?>
</ul>
<?php else: ?>
<p class="no-data">記事がありません。</p>
<?php endif; ?>
</section>

<!-- pagination -->
<?php $bcBaser->pagination('simple'); ?>