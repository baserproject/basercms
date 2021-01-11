<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var \BcAppView $this
 * @var int $blogContentId
 * @var bool $postCount
 */
if (!isset($blogContentId) && isset($blogContent['BlogContent']['id'])) {
	$blogContentId = $blogContent['BlogContent']['id'];
}
if (!isset($postCount)) {
	$postCount = false;
}
?>


<?php if (!empty($tags)): ?>
	<ul class="bc-blog-tags">
		<?php foreach($tags as $tag): ?>
			<li>
				<?php $this->Blog->tagLink($blogContentId, $tag) ?>
				<?php if ($postCount): ?>
					(<?php echo $tag['BlogTag']['post_count'] ?>)
				<?php endif ?>
			</li>
		<?php endforeach ?>
	</ul>
<?php endif ?>
