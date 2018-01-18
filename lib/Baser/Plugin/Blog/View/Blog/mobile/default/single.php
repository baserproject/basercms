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
 * [MOBILE] ブログ詳細
 */
$this->BcBaser->setTitle($this->pageTitle . '｜' . $this->Blog->getTitle());
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->Blog->getPostContent($post, false, false, 50));
?>


<!-- title -->
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;"><?php echo $this->BcBaser->getContentsTitle(); ?></span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<br />

<!-- detail -->
<?php if (!empty($post)): ?>
	<?php $this->Blog->eyeCatch($post, ['mobile' => true]) ?>
	<?php $this->Blog->postContent($post) ?>
	<br />
	<p align="right">
		<?php $this->Blog->category($post) ?>
		<br />
		<?php $this->Blog->postDate($post) ?>
		<br />
		<?php $this->Blog->author($post) ?>
	</p>
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
	<br />
<?php else: ?>
	<p class="no-data">記事がありません。</p>
<?php endif; ?>

<!-- comments -->
<?php $this->BcBaser->element('blog_comments') ?>