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
$this->bcBaser->css(array('/blog/css/style','colorbox/colorbox'), array('inline' => true));
$this->bcBaser->js('jquery.colorbox-min', false);
$this->bcBaser->setDescription($this->blog->getTitle().'｜'.$this->blog->getPostContent($post,false,false,50));
?>

<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
});
</script>

<!-- blog title -->
<h2 class="contents-head">
	<?php $this->blog->title() ?>
</h2>

<!-- post title -->
<h3 class="contents-head">
	<?php $this->bcBaser->contentsTitle() ?>
</h3>

<!-- post detail -->
<div class="post">
	<?php $this->blog->postContent($post) ?>
	<div class="meta"><span>
		<?php $this->blog->category($post) ?>
		&nbsp;
		<?php $this->blog->postDate($post) ?>
		&nbsp;
		<?php $this->blog->author($post) ?>
	</span></div>
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