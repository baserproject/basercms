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
 * [PUBLISH] 関連投稿一覧
 */
$relatedPosts = $this->Blog->getRelatedPosts($post);
?>


<?php if ($relatedPosts): ?>
	<div id="RelatedPosts">
		<h4 class="contents-head"><?php echo __('関連記事') ?></h4>
		<ul>
			<?php foreach($relatedPosts as $relatedPost): ?>
				<li><?php $this->Blog->postTitle($relatedPost) ?></li>
			<?php endforeach ?>
		</ul>
	</div>
<?php endif ?>
