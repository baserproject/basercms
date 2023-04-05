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
 * ブログタグ
 *
 * 呼出箇所：ブログトップ、ブログ記事詳細、カテゴリ別ブログ記事一覧、タグ別ブログ記事一覧、年別ブログ記事一覧、月別ブログ記事一覧、日別ブログ記事一覧
 *
 * @var \BaserCore\View\BcFrontAppView $this
 */
?>


<?php if (!empty($this->Blog->blogContent['tag_use'])): ?>
	<?php if (!empty($post['BlogTag'])) : ?>
		<div class="bs-blog-tag"><?php echo __d('baser_core', 'タグ') ?>：<?php $this->Blog->tag($post) ?></div>
	<?php endif ?>
<?php endif ?>
