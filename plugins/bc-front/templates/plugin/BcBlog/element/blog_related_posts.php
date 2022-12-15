<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * [PUBLISH] 関連投稿一覧
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $post
 * @checked
 * @noTodo
 * @unitTest
 */
$relatedPosts = $this->Blog->getRelatedPosts($post);
?>


<?php if ($relatedPosts): ?>
	<div class="bs-blog-related-posts">
		<h4 class="contents-head"><?php echo __('関連記事') ?></h4>
		<ul>
			<?php foreach($relatedPosts as $relatedPost): ?>
				<li><?php $this->Blog->postTitle($relatedPost) ?></li>
			<?php endforeach ?>
		</ul>
	</div>
<?php endif ?>
