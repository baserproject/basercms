<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログ詳細ページ
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
$baser->setDescription($blog->getTitle().'｜'.$blog->getPostContent($post,false,false,50));
$blog->editPost($post['BlogPost']['blog_content_id'],$post['BlogPost']['id']);
?>

<!-- blog title -->
<h2 class="contents-head">
	<?php $blog->title() ?>
</h2>

<!-- post detail -->
<div class="post">
	
	<!-- post title -->
	<h3 class="contents-head">
		<?php $baser->contentsTitle() ?><br />
		<small><?php $blog->postDate($post) ?></small>
	</h3>

	<?php $blog->postContent($post) ?>
	
	<div class="meta"><span><?php $blog->category($post) ?>&nbsp;<?php $blog->author($post) ?></span></div>
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