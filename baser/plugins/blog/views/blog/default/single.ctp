<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログ詳細ページ
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$baser->css(array('/blog/css/style','colorbox/colorbox'), null, null, false);
$baser->js('jquery.colorbox-min', false);
$baser->setDescription($blog->getTitle().'｜'.$blog->getPostContent($post,false,false,50));
$blog->editPost($post['BlogPost']['blog_content_id'],$post['BlogPost']['id']);
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
	<?php $baser->contentsTitle() ?>
</h3>

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
	<?php $baser->element('blog_tag', array('post' => $post)) ?>
</div>

<!-- contents navi -->
<div id="contentsNavi">
	<?php $blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
	<?php $blog->nextLink($post) ?>
</div>

<!-- comments -->
<?php $baser->element('blog_comments') ?>