<?php
/**
 * [PUBLISH] ブログ投稿者一覧
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
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
$baseCurrentUrl = $this->request->params['Content']['url'] . '/archives/';
?>
<div class="widget widget-blog-authors widget-blog-authors-<?php echo $id ?> blog-widget">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php if ($authors): ?>
		<ul>
			<?php foreach ($authors as $author): ?>
				<?php
				if ($this->request->url == $baseCurrentUrl . $author['User']['name']) {
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
					<?php $this->BcBaser->link($title, $this->request->params['Content']['name'] . '/archives/author/' . $author['User']['name'], ['escape' => true]) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div> 
