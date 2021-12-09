<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BlogHelper', 'Blog.View/Helper');

/**
 * ブログカテゴリ一覧
 * 呼出箇所：ウィジェット
 *
 * @var BcAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 */
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


<div class="bs-widget bs-widget-blog-categories-archives bs-widget-blog-categories-archives-<?php echo $id ?> bs-blog-widget">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-head"><?php echo $name ?></h2>
	<?php endif ?>
	<?php if ($by_year): ?>
		<ul class="bs-widget-list-by-year">
			<?php foreach ($categories as $key => $category): ?>
				<li class="bs-widget-list-by-year__item">
					<span>
						<?php $this->BcBaser->link($key . '年', [
							'plugin' => null,
							'controller' => $this->request->params['Content']['url'],
							'action' => 'archives',
							'date', $key
						], ['class' => 'bs-widget-list-by-year__item-title']) ?>
					</span>
					<?php echo $this->Blog->getCategoryList($category, $depth, $view_count, ['named' => ['year' => $key]]) ?>
				</li>
			<?php endforeach ?>
		</ul>
	<?php else: ?>
		<?php echo $this->Blog->getCategoryList($categories, $depth, $view_count) ?>
	<?php endif ?>
</div>
