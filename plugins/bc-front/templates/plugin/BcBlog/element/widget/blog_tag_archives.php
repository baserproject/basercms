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
 * ブログタグアーカイブ
 *
 * 呼出箇所：ウィジェット
 *
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var string $name
 * @var bool $use_title
 * @var int $blog_content_id
 * @var bool $view_count
 * @checked
 * @noTodo
 * @unitTest
 */
$id = '';
?>


<div class="widget widget-blog-authors widget-blog-authors-<?php echo $id ?> blog-widget">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php $this->BcBaser->blogTagList((int)$blog_content_id, ['postCount' => $view_count]) ?>
</div>
