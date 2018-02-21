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
 * [MOBILE] ブログ
 */
$this->BcBaser->setTitle($this->pageTitle . '｜' . $this->Blog->getTitle());
$this->BcBaser->setDescription(sprintf(__d('baser', '%s｜%sのアーカイブ一覧です。'), $this->Blog->getTitle(), $this->BcBaser->getContentsTitle()));
?>


<!-- title -->
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;"><?php echo $this->BcBaser->getContentsTitle(); ?></span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />

<!-- pagination -->
<?php echo $this->BcBaser->pagination() ?>

<!-- list -->
<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
		<span style="color:#8ABE08">◆</span>
		<?php $this->Blog->postTitle($post) ?>
		<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
		<br />
		<?php $this->Blog->postContent($post, false, true) ?>
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
	<?php endforeach; ?>
<?php else: ?>
	<p class="no-data"><?php echo __d('baser', '記事がありません。')?></p>
<?php endif; ?>

<!-- pagination -->
<?php echo $this->BcBaser->pagination() ?>