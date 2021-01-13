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
 * [PUBLISH] ブログ投稿者一覧
 * @var BcAppView $this
 */
if (empty($view_count)) {
	$view_count = '0';
}
if (isset($blogContent)) {
	$id = $blogContent['BlogContent']['id'];
} else {
	$id = $blog_content_id;
}
$data = $this->requestAction('/blog/blog/get_authors/' . $id . '/' . $view_count, ['entityId' => $id]);
$authors = $data['authors'];
$blogContent = $data['blogContent'];
$baseCurrentUrl = $this->BcBaser->getBlogContentsUrl($id) . 'archives/author/';
?>


<div class="widget widget-blog-authors widget-blog-authors-<?php echo $id ?> blog-widget">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php if ($authors): ?>
		<ul>
			<?php foreach($authors as $author): ?>
				<?php
				if ('/' . $this->request->url == $baseCurrentUrl . $author['User']['name']) {
					$class = ' class="current"';
				} else {
					$class = '';
				}
				if ($view_count) {
					$title = h($this->BcBaser->getUserName($author['User'])) . ' (' . $author['count'] . ')';
				} else {
					$title = h($this->BcBaser->getUserName($author['User']));
				}
				?>
				<li<?php echo $class ?>>
					<?php $this->BcBaser->link($title, $baseCurrentUrl . $author['User']['name'], ['escape' => true]) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
