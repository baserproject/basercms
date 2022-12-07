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
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blogContentId
 * @var bool $postCount
 * @checked
 * @noTodo
 * @unitTest
 */
if (!isset($blogContentId) && isset($blogContent->id)) {
	$blogContentId = $blogContent->id;
}
if (!isset($postCount)) $postCount = false;
?>


<?php if (!empty($tags)): ?>
	<ul class="bc-blog-tags">
		<?php foreach($tags as $tag): ?>
			<li>
				<?php $this->Blog->tagLink($blogContentId, $tag) ?>
				<?php if ($postCount): ?>
					(<?php echo $tag->post_count ?>)
				<?php endif ?>
			</li>
		<?php endforeach ?>
	</ul>
<?php endif ?>
