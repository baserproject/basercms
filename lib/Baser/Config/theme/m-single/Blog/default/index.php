<?php
/**
 * [PUBLISH] ブログトップ
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->css(array('Blog.style', 'admin/colorbox/colorbox'), array('inline' => false));
$this->BcBaser->js('admin/jquery.colorbox-min-1.4.5', false);
$this->BcBaser->setDescription($this->Blog->getDescription());
?>

<script>
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
	});
</script>

<div class="articleArea lastArticle" id="news">
<article class="mainWidth">
<h2 class="fontawesome-circle-arrow-down">NEWS RELEASE <span>新着情報</span></h2>

<div class="newsArea">
	<?php if (!empty($posts)): ?>
		<?php foreach ($posts as $post): ?>
	<article class="entry">
		<h3><?php $this->Blog->postTitle($post) ?></h3>
		<time datetime="<?php $this->Blog->postDate($post, 'Y-m-d') ?>"><?php $this->Blog->postDate($post, 'Y.m.d') ?></time>
		
		<?php $this->Blog->postContent($post) ?>
		
		<div class="metaArea">
			<ul>
				<li>Category：<?php $this->Blog->category($post) ?></li>
				<li><?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?></li>
				<li>Author：<?php $this->Blog->author($post) ?></li>
			</ul>
		</div><!-- /metaArea -->
	</article><!-- /entry -->
		<?php endforeach; ?>
	<?php else: ?>
	<p class="no-data">記事がありません。</p>
	<?php endif; ?>
	
	<!-- pagination -->
	<?php $this->BcBaser->pagination('simple'); ?>
	
</div><!-- /newsArea -->
	
<?php /* サイドナビ */ ?>
<?php $this->BcBaser->element('sidebox') ?>

</article>
</div><!-- /articleArea -->
