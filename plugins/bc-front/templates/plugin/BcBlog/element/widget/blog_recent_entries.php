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
 * ブログ最近の投稿
 *
 * 呼出箇所：ウィジェット
 *
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 */
if (!isset($count)) $count = 5;
if (isset($blogContent)) {
	$id = $blogContent->id;
} else {
	$id = $blog_content_id;
}
$data = $this->Blog->getViewVarsRecentEntriesWidget($id, $count);
$recentEntries = $data['recentEntries'];
$blogContent = $data['blogContent'];
$baseCurrentUrl = $this->BcBaser->getBlogContentsUrl($id) . 'archives/';
?>


<div class="bs-widget bs-widget-blog-recent-entries bs-widget-blog-recent-entries-<?php echo h($id) ?> bs-blog-widget">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-head"><?php echo h($name) ?></h2>
	<?php endif ?>
	<?php if ($recentEntries): ?>
		<ul class="bs-widget-list">
			<?php foreach ($recentEntries as $recentEntry): ?>
				<?php
				$class = ['bs-widget-list__item'];
				if ($this->getRequest()->getPath() === $baseCurrentUrl . $recentEntry->no) {
					$class[] = 'current';
				}
				?>
				<li class="<?php echo implode(' ', $class) ?>">
					<?php $this->Blog->postTitle($recentEntry, true, ['class' => 'bs-widget-list__item-title']) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
