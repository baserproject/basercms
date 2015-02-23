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
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->Blog->getPostContent($post, false, false, 50));
$this->Blog->editPost($post['BlogPost']['blog_content_id'], $post['BlogPost']['id']);
?>

<!-- blog title -->
<h2 class="contents-head">
	<?php $this->Blog->title() ?>
</h2>

<!-- post detail -->
<div class="post">

	<!-- post title -->
	<h3 class="contents-head">
		<?php $this->BcBaser->contentsTitle() ?><br />
		<small><?php $this->Blog->postDate($post) ?></small>
	</h3>

	<?php $this->Blog->postContent($post) ?>

	<div class="meta"><span><?php $this->Blog->category($post) ?>&nbsp;<?php $this->Blog->author($post) ?></span></div>
	<?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?>
</div>

<!-- contents navi -->
<div id="contentsNavi">
	<?php $this->Blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
	<?php $this->Blog->nextLink($post) ?>
</div>
<!-- comments -->
<?php $this->BcBaser->element('blog_comments') ?>