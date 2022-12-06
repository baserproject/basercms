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
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 * @checked
 * @noTodo
 * @unitTest
 */

if (empty($view_count)) {
	$view_count = '0';
}
if (isset($blogContent)) {
	$id = $blogContent->id;
} else {
	$id = $blog_content_id;
}
$data = $this->Blog->getViewVarsForBlogAuthorArchivesWidget($id, $view_count);
if(!$data) return;
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
				if ('/' . $this->getRequest()->getPath() === $baseCurrentUrl . $author->name) {
					$class[] = 'current';
				}
				if ($view_count) {
					$title = h($this->BcBaser->getUserName($author)) . ' (' . $author['count'] . ')';
				} else {
					$title = h($this->BcBaser->getUserName($author));
				}
				?>
				<li class="<?php echo implode(' ', $class) ?>">
				  <?php if($author->name): ?>
            <?php $this->BcBaser->link($title, $baseCurrentUrl . $author->name, [
              'escape' => true,
              'class' => 'bs-widget-list__item-title'
            ]) ?>
          <?php else: ?>
            <?php echo $title ?>
          <?php endif ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
