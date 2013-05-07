<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログ詳細ページ
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$this->bcBaser->setDescription($this->Blog->getTitle().'｜'.$this->Blog->getPostContent($post,false,false,50));
$this->blog->editPost($post['BlogPost']['blog_content_id'],$post['BlogPost']['id']);
?>

<!-- blog title -->
<h2 class="contents-head">
	<?php $this->Blog->title() ?>
</h2>

<!-- post detail -->
<div class="post">
	
	<!-- post title -->
	<h3 class="contents-head">
		<?php $this->bcBaser->contentsTitle() ?><br />
		<small><?php $this->blog->postDate($post) ?></small>
	</h3>

	<?php $this->Blog->postContent($post) ?>
	
	<div class="meta"><span><?php $this->blog->category($post) ?>&nbsp;<?php $this->blog->author($post) ?></span></div>
	<?php $this->bcBaser->element('blog_tag', array('post' => $post)) ?>
</div>

<!-- contents navi -->
<div id="contentsNavi">
	<?php $this->blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
	<?php $this->blog->nextLink($post) ?>
</div>
<!-- comments -->
<?php $this->bcBaser->element('blog_comments') ?>