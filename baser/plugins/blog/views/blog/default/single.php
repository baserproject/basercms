<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログ詳細ページ
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
$bcBaser->css(array('/blog/css/style','colorbox/colorbox'), array('inline' => true));
$bcBaser->js('jquery.colorbox-min', false);
$bcBaser->setDescription($blog->getTitle().'｜'.$blog->getPostContent($post,false,false,50));
?>

<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
});
</script>

<!-- blog title -->
<h2 class="contents-head">
	<?php $blog->title() ?>
</h2>

<!-- post title -->
<h3 class="contents-head">
	<?php $bcBaser->contentsTitle() ?>
</h3>

<div class="eye-catch">
	<?php $blog->eyeCatch($post) ?>
</div>

<!-- post detail -->
<div class="post">
	<?php $blog->postContent($post) ?>
	<div class="meta"><span>
		<?php $blog->category($post) ?>
		&nbsp;
		<?php $blog->postDate($post) ?>
		&nbsp;
		<?php $blog->author($post) ?>
	</span></div>
	<?php $bcBaser->element('blog_tag', array('post' => $post)) ?>
</div>

<!-- contents navi -->
<div id="contentsNavi">
	<?php $blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
	<?php $blog->nextLink($post) ?>
</div>

<!-- comments -->
<?php $bcBaser->element('blog_comments') ?>