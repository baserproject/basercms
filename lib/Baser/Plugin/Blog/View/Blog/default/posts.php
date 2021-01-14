<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] 記事一覧
 *
 * BaserHelper::blogPosts( コンテンツ名, 件数 ) で呼び出す
 * （例）<?php $this->BcBaser->blogPosts('news', 3) ?>
 */
?>


<?php if ($posts): ?>
	<ul class="post-list">
		<?php foreach($posts as $key => $post): ?>
			<?php $class = ['clearfix', 'post-' . ($key + 1)] ?>
			<?php if ($this->BcArray->first($posts, $key)): ?>
				<?php $class[] = 'first' ?>
			<?php elseif ($this->BcArray->last($posts, $key)): ?>
				<?php $class[] = 'last' ?>
			<?php endif ?>
			<li class="<?php echo implode(' ', $class) ?>">
				<span class="date"><?php $this->Blog->postDate($post, 'Y.m.d') ?></span><br/>
				<span class="title"><?php $this->Blog->postTitle($post) ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p class="no-data"><?php echo __d('baser', '記事がありません') ?></p>
<?php endif ?>
