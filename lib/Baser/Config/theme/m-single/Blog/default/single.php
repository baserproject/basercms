<?php
/**
 * [PUBLISH] ブログ詳細ページ
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
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->Blog->getPostContent($post, false, false, 50));
?>

<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
	});
</script>

<div class="articleArea lastArticle" id="news">
<article class="mainWidth">
<h2 class="fontawesome-circle-arrow-down">NEWS RELEASE <span>新着情報</span></h2>

<div class="newsArea">
	<article class="entry">
		<h3 class="fontawesome-file-alt"><?php $this->BcBaser->contentsTitle() ?></h3>
		<time datetime="<?php $this->Blog->postDate($post, 'Y-m-d') ?>"><?php $this->Blog->postDate($post, 'Y.m.d') ?></time>
		
		<?php if ( $this->Blog->getEyeCatch($post) ): ?>
		<div class="eyeCatch">
		<?php $this->Blog->eyeCatch($post) ?>
		</div><!-- /eyeCatch -->
		<?php endif; ?>
		
		<?php $this->Blog->postContent($post) ?>
		
		<div class="metaArea">
			<ul>
				<li>Category：<?php $this->Blog->category($post) ?></li>
				<li><?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?></li>
				<li>Author：<?php $this->Blog->author($post) ?></li>
			</ul>
		</div><!-- /metaArea -->
		
		<?php $this->BcBaser->element('blog_comments') ?>
	</article><!-- /entry -->
	
	<!-- pagination -->
	<div id="contentsNavi">
	<?php $this->Blog->prevLink($post) ?>
	&nbsp; &nbsp;
	<?php $this->Blog->nextLink($post) ?>
	</div>
	
</div><!-- /newsArea -->
	
<?php /* サイドナビ */ ?>
<?php $this->BcBaser->element('sidebox') ?>

</article>
</div><!-- /articleArea -->

