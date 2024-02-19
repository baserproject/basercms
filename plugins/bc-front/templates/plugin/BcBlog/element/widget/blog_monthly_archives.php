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
 * ブログ月別アーカイブ
 *
 * 呼出箇所：ウィジェット
 *
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 */
if (!isset($view_count)) $view_count = false;
if (!isset($limit)) $limit = 12;
if (isset($blogContent)) {
	$id = $blogContent->id;
} else {
	$id = $blog_content_id;
}
$data = $this->Blog->getViewVarsBlogMonthlyArchivesWidget($id, $limit, $view_count);
$postedDates = $data['postedDates'];
$blogContent = $data['blogContent'];
$baseCurrentUrl = $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/';
?>


<div class="bs-widget bs-widget-blog-monthly-archives bs-widget-blog-monthly-archives-<?php echo h($id) ?> bs-blog-widget">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-head"><?php echo h($name) ?></h2>
	<?php endif ?>
	<?php if (!empty($postedDates)): ?>
		<ul class="bs-widget-list">
			<?php foreach ($postedDates as $postedDate): ?>
				<?php
				$class = ['bs-widget-list__item'];
				if ($this->getRequest()->getQuery('year') === $postedDate['year'] && $this->getRequest()->getQuery('month') === $postedDate['month']) {
					$class[] = 'selected';
				} elseif ($this->getRequest()->getPath() === $baseCurrentUrl . $postedDate['year'] . '/' . $postedDate['month']) {
					$class[] = 'current';
				}
				if ($view_count) {
					$title = $postedDate['year'] . '/' . $postedDate['month'] . '(' . $postedDate['count'] . ')';
				} else {
					$title = $postedDate['year'] . '/' . $postedDate['month'];
				}
				$url = $this->BcBaser->getBlogContentsUrl($blogContent->id) . 'archives/date/' . $postedDate['year'] . '/' . $postedDate['month'];
				?>
				<li class="<?php echo implode(' ', $class) ?>">
					<?php $this->BcBaser->link($title, $url, ['class' => 'bs-widget-list__item-title']) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
