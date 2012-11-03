<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログトップ
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
$this->bcBaser->setDescription($this->blog->getDescription());
?>

<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
});
</script>

<!-- title -->
<h2 class="contents-head">
	<?php $this->blog->title() ?>
</h2>

<!-- description -->
<?php if($this->blog->descriptionExists()): ?>
<div class="blog-description">
	<?php $this->blog->description() ?>
</div>
<?php endif ?>

<!-- list -->
<?php if(!empty($posts)): ?>
	<?php foreach($posts as $post): ?>
<div class="post">
	<h4 class="contents-head">
		<?php $this->blog->postTitle($post) ?>
	</h4>
	<?php $this->blog->postContent($post, false, true) ?>
	<div class="meta"><span>
		<?php $this->blog->category($post) ?>
		&nbsp;
		<?php $this->blog->postDate($post) ?>
		&nbsp;
		<?php $this->blog->author($post) ?>
	</span></div>
	<?php $this->bcBaser->element('blog_tag', array('post' => $post)) ?>
</div>
	<?php endforeach; ?>
<?php else: ?>
<p class="no-data">記事がありません。</p>
<?php endif; ?>

<!-- pagination -->
<?php $bcBaser->pagination('simple'); ?>