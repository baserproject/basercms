<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログトップ
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$baser->css(array('/blog/css/style','colorbox/colorbox'), null, null, false);
$baser->js('jquery.colorbox-min', false);
$baser->setDescription($blog->getDescription());
?>

<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
});
</script>

<!-- title -->
<h2 class="contents-head">
	<?php $blog->title() ?>
</h2>

<!-- description -->
<?php if($blog->descriptionExists()): ?>
<p class="blog-description">
	<?php $blog->description() ?>
</p>
<?php endif ?>

<!-- list -->
<?php if(!empty($posts)): ?>
	<?php foreach($posts as $post): ?>
<div class="post">
	<h4 class="contents-head">
		<?php $blog->postTitle($post) ?>
	</h4>
	<?php $blog->postContent($post,true,true) ?>
	<div class="meta"><span>
		<?php $blog->category($post) ?>
		&nbsp;
		<?php $blog->postDate($post) ?>
		&nbsp;
		<?php $blog->author($post) ?>
	</span></div>
	<?php $baser->element('blog_tag', array('post' => $post)) ?>
</div>
	<?php endforeach; ?>
<?php else: ?>
<p class="no-data">記事がありません。</p>
<?php endif; ?>

<!-- pagination -->
<?php $baser->pagination('simple'); ?>