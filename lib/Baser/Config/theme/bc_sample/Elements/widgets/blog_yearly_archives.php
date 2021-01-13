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
 * ブログ年別アーカイブ
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
$blogcontent = $data['blogContent'];
$baseCurrentUrl = $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/';
?>


<div class="bs-widget bs-widget-blog-yearly-archives bs-widget-blog-yearly-archives-<?php echo $id ?> bs-blog-widget">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-head"><?php echo $name ?></h2>
	<?php endif ?>
	<?php if (!empty($postedDates)): ?>
		<ul class="bs-widget-list">
			<?php foreach ($postedDates as $postedDate): ?>
				<?php
				$class = ['bs-widget-list__item'];
				if (isset($this->params['named']['year']) && $this->params['named']['year'] == $postedDate['year']) {
					$class[] = 'selected';
				} elseif ('/' . $this->request->url == $baseCurrentUrl . $postedDate['year']) {
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
