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
 * [PUBLISH] ブログ最近の投稿
 */
if (!isset($count)) {
	$count = 5;
}
if (isset($blogContent)) {
	$id = $blogContent['BlogContent']['id'];
} else {
	$id = $blog_content_id;
}
$data = $this->requestAction('/blog/blog/get_recent_entries/' . $id . '/' . $count, ['entityId' => $id]);
$recentEntries = $data['recentEntries'];
$blogContent = $data['blogContent'];
$baseCurrentUrl = $this->BcBaser->getBlogContentsUrl($id) . 'archives/';
?>
<div class="widget widget-blog-recent-entries widget-blog-recent-entries-<?php echo $id ?> blog-widget">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php if ($recentEntries): ?>
		<ul>
			<?php foreach($recentEntries as $recentEntry): ?>
				<?php if ($this->request->url == $baseCurrentUrl . $recentEntry['BlogPost']['no']): ?>
					<?php $class = ' class="current"' ?>
				<?php else: ?>
					<?php $class = '' ?>
				<?php endif ?>
				<li<?php echo $class ?>>
					<?php $this->Blog->postTitle($recentEntry) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
