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
 * ブログ最近の投稿
 * 呼出箇所：ウィジェット
 *
 * @var BcAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
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


<div class="bs-widget bs-widget-blog-recent-entries bs-widget-blog-recent-entries-<?php echo $id ?> bs-blog-widget">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-head"><?php echo $name ?></h2>
	<?php endif ?>
	<?php if ($recentEntries): ?>
		<ul class="bs-widget-list">
			<?php foreach ($recentEntries as $recentEntry): ?>
				<?php
				$class = ['bs-widget-list__item'];
				if ('/' . $this->request->url == $baseCurrentUrl . $recentEntry['BlogPost']['no']) {
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
