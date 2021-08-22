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
 * ブログ月別アーカイブ
 * 呼出箇所：ウィジェット
 *
 * @var BcAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 */
if (!isset($view_count)) {
	$view_count = false;
}
if (!isset($limit)) {
	$limit = 12;
}
if (isset($blogContent)) {
	$id = $blogContent['BlogContent']['id'];
} else {
	$id = $blog_content_id;
}
$actionUrl = '/blog/blog/get_posted_months/' . $id . '/' . $limit;
if ($view_count) {
	$actionUrl .= '/1';
}
$data = $this->requestAction($actionUrl, ['entityId' => $id]);
$postedDates = $data['postedDates'];
$blogContent = $data['blogContent'];
$baseCurrentUrl = $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/';
?>


<div class="bs-widget bs-widget-blog-monthly-archives bs-widget-blog-monthly-archives-<?php echo $id ?> bs-blog-widget">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-head"><?php echo $name ?></h2>
	<?php endif ?>
	<?php if (!empty($postedDates)): ?>
		<ul class="bs-widget-list">
			<?php foreach ($postedDates as $postedDate): ?>
				<?php
				$class = ['bs-widget-list__item'];
				if (isset($this->request->params['named']['year']) && isset($this->request->params['named']['month']) && $this->request->params['named']['year'] == $postedDate['year'] && $this->request->params['named']['month'] == $postedDate['month']) {
					$class[] = 'selected';
				} elseif ('/' . $this->request->url == $baseCurrentUrl . $postedDate['year'] . '/' . $postedDate['month']) {
					$class[] = 'current';
				}
				if ($view_count) {
					$title = $postedDate['year'] . '/' . $postedDate['month'] . '(' . $postedDate['count'] . ')';
				} else {
					$title = $postedDate['year'] . '/' . $postedDate['month'];
				}
				$url = $this->BcBaser->getBlogContentsUrl($blogContent['BlogContent']['id']) . 'archives/date/' . $postedDate['year'] . '/' . $postedDate['month'];
				?>
				<li class="<?php echo implode(' ', $class) ?>">
					<?php $this->BcBaser->link($title, $url, ['class' => 'bs-widget-list__item-title']) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
