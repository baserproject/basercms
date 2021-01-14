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
 * ブログ投稿者一覧
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
if (isset($blogContent)) {
	$id = $blogContent['BlogContent']['id'];
} else {
	$id = $blog_content_id;
}
$data = $this->requestAction('/blog/blog/get_authors/' . $id . '/' . $view_count, ['entityId' => $id]);
$authors = $data['authors'];
$blogContent = $data['blogContent'];
$baseCurrentUrl = $this->BcBaser->getBlogContentsUrl($id) . 'archives/author/';
?>


<div class="bs-widget bs-widget-blog-authors bs-widget-blog-authors-<?php echo $id ?> bs-blog-widget">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-head"><?php echo $name ?></h2>
	<?php endif ?>
	<?php if ($authors): ?>
		<ul class="bs-widget-list">
			<?php foreach ($authors as $author): ?>
				<?php
				$class = ['bs-widget-list__item'];
				if ('/' . $this->request->url == $baseCurrentUrl . $author['User']['name']) {
					$class[] = 'current';
				}
				if ($view_count) {
					$title = h($this->BcBaser->getUserName($author['User'])) . ' (' . $author['count'] . ')';
				} else {
					$title = h($this->BcBaser->getUserName($author['User']));
				}
				?>
				<li class="<?php echo implode(' ', $class) ?>">
					<?php $this->BcBaser->link($title, $baseCurrentUrl . $author['User']['name'], [
						'escape' => true,
						'class' => 'bs-widget-list__item-title'
					]) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
