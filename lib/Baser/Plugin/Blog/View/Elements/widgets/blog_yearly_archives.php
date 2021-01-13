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
 * [PUBLISH] ブログ年別アーカイブ
 * @var BcAppView $this
 */
if (!isset($view_count)) {
	$view_count = false;
}
if (!isset($limit)) {
	$limit = false;
}
if (isset($blogContent)) {
	$id = $blogContent['BlogContent']['id'];
} else {
	$id = $blog_content_id;
}

$actionUrl = '/blog/blog/get_posted_years/' . $id;
if ($limit) {
	$actionUrl .= '/' . $limit;
} else {
	$actionUrl .= '/0';
}
if ($view_count) {
	$actionUrl .= '/1';
} else {
	$actionUrl .= '/0';
}

$data = $this->requestAction($actionUrl, ['entityId' => $id]);
$postedDates = $data['postedDates'];
$blogContent = $data['blogContent'];
$baseCurrentUrl = $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/';
?>


<div class="widget widget-blog-yearly-archives widget-blog-yearly-archives-<?php echo $id ?> blog-widget">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php if (!empty($postedDates)): ?>
		<ul>
			<?php foreach($postedDates as $postedDate): ?>
				<?php if (isset($this->params['named']['year']) && $this->params['named']['year'] == $postedDate['year']): ?>
					<?php $class = ' class="selected"' ?>
				<?php elseif ($this->request->url == $baseCurrentUrl . $postedDate['year']): ?>
					<?php $class = ' class="current"' ?>
				<?php else: ?>
					<?php $class = '' ?>
				<?php endif ?>
				<?php if ($view_count): ?>
					<?php $title = sprintf(__d('baser', '%s年'), $postedDate['year']) . '(' . $postedDate['count'] . ')' ?>
				<?php else: ?>
					<?php $title = sprintf(__d('baser', '%s年'), $postedDate['year']) ?>
				<?php endif ?>
				<li<?php echo $class ?>>
					<?php $this->BcBaser->link($title, $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/' . $postedDate['year']) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
