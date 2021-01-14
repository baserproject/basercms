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
 * [PUBLISH] ブログカテゴリー一覧
 */
App::uses('BlogHelper', 'Blog.View/Helper');

if (empty($view_count)) {
	$view_count = '0';
}
if (empty($limit)) {
	$limit = '0';
}
if (!isset($by_year)) {
	$by_year = null;
}
if (isset($blogContent)) {
	$id = $blogContent['BlogContent']['id'];
} else {
	$id = $blog_content_id;
}
if (empty($depth)) {
	$depth = 1;
}
$actionUrl = '/blog/blog/get_categories/' . $id . '/' . $limit . '/' . $view_count . '/' . $depth;
if ($by_year) {
	$actionUrl .= '/year';
}
$data = $this->requestAction($actionUrl, ['entityId' => $id]);
$categories = $data['categories'];
$this->viewVars['blogContent'] = $data['blogContent'];
$this->Blog = new BlogHelper($this);
?>


<div class="widget widget-blog-categories-archives widget-blog-categories-archives-<?php echo $id ?> blog-widget">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php if ($by_year): ?>
		<ul>
			<?php foreach($categories as $key => $category): ?>
				<li class="category-year">
					<span><?php $this->BcBaser->link(sprintf(__('%s年'), $key), $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/' . $key) ?></span>
					<?php echo $this->Blog->getCategoryList($category, $depth, $view_count, ['named' => ['year' => $key]]) ?>
				</li>
			<?php endforeach ?>
		</ul>
	<?php else: ?>
		<?php echo $this->Blog->getCategoryList($categories, $depth, $view_count) ?>
	<?php endif ?>
</div>
