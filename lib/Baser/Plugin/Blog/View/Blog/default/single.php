<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] ブログ詳細ページ
 */
$this->BcBaser->css(['Blog.style'], ['inline' => false]);
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->Blog->getPostContent($post, false, false, 50));
?>


<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
	});
</script>

<!-- blog title -->
<h1 class="contents-head">
<?php $this->Blog->title() ?>
</h1>

<!-- post title -->
<h2 class="contents-head">
<?php $this->BcBaser->contentsTitle() ?>
</h2>

<div class="eye-catch">
<?php $this->Blog->eyeCatch($post) ?>
</div>

<!-- post detail -->
<div class="post">
			<?php $this->Blog->postContent($post) ?>
	<div class="meta"><span>
			<?php $this->Blog->category($post) ?>
			&nbsp;
			<?php $this->Blog->postDate($post) ?>
			&nbsp;
	<?php $this->Blog->author($post) ?>
		</span></div>
<?php $this->BcBaser->element('blog_tag', ['post' => $post]) ?>
</div>

<!-- contents navi -->
<div class="post-navi">
	<?php $this->Blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
<?php $this->Blog->nextLink($post) ?>
</div>

<!-- comments -->
<?php $this->BcBaser->element('blog_comments') ?>