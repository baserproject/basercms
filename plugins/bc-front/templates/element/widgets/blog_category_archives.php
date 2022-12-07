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

/**
 * ブログカテゴリ一覧
 * 呼出箇所：ウィジェット
 *
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 * @noTodo
 * @checked
 * @unitTest
 */
if (empty($view_count)) $view_count = '0';
if (empty($limit)) $limit = '0';
if (!isset($by_year)) $by_year = null;
if (empty($depth)) $depth = 1;
if (isset($blogContent)) {
	$id = $blogContent->id;
} else {
	$id = $blog_content_id;
}
$data = $this->Blog->getViewVarsForBlogCategoryArchivesWdget($id, $limit, $view_count, $depth, $by_year? 'year' : null);
$categories = $data['categories'];
$this->viewVars['blogContent'] = $data['blogContent'];
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
						<?php $this->BcBaser->link(
						  $key . '年',
						  $this->request->getAttribute('currentContent')->url . 'archives/date/' . $key,
						  ['class' => 'bs-widget-list-by-year__item-title']
						) ?>
					</span>
					<?php echo $this->Blog->getCategoryList($category, $depth, $view_count, ['query' => ['year' => $key]]) ?>
				</li>
			<?php endforeach ?>
		</ul>
	<?php else: ?>
		<?php echo $this->Blog->getCategoryList($categories, $depth, $view_count) ?>
	<?php endif ?>
</div>
