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
 * ブログ年別アーカイブ
 *
 * 呼出箇所：ウィジェット
 *
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 */

if (!isset($view_count)) $view_count = false;
if (!isset($limit)) $limit = false;
if (isset($blogContent)) {
	$id = $blogContent->id;
} else {
	$id = $blog_content_id;
}
$data = $this->Blog->getViewVarsForBlogYearlyArchivesWidget($id, $limit, $view_count);
$postedDates = $data['postedDates'];
$blogcontent = $data['blogContent'];
$baseCurrentUrl = $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/';
?>


<div class="bs-widget bs-widget-blog-yearly-archives bs-widget-blog-yearly-archives-<?php echo h($id) ?> bs-blog-widget">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-head"><?php echo h($name) ?></h2>
	<?php endif ?>
	<?php if (!empty($postedDates)): ?>
		<ul class="bs-widget-list">
			<?php foreach ($postedDates as $postedDate): ?>
				<?php
				$class = ['bs-widget-list__item'];
				if ($this->getRequest()->getQuery('year') === $postedDate['year']) {
					$class[] = 'selected';
				} elseif ($this->getRequest()->getPath() === $baseCurrentUrl . $postedDate['year']) {
					$class[] = 'current';
				}
				if ($view_count) {
					$title = $postedDate['year'] . '年' . '(' . $postedDate['count'] . ')';
				} else {
					$title = $postedDate['year'] . '年';
				}
				?>
				<li class="<?php echo implode(' ', $class) ?>">
					<?php $this->BcBaser->link($title, $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/' . $postedDate['year']) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
