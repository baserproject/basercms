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
 * ブログコメント
 *
 * 呼出箇所：ブログ記事詳細
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogComment $blogComment コメントデータ
 */
?>


<?php if (!empty($blogComment)): ?>
	<?php if ($blogComment->status): ?>
<div class="bs-blog-comment__list-item" id="Comment<?php echo $blogComment->no ?>">
	<div class="bs-blog-comment__list-item-name">
		<?php if ($blogComment->url): ?>
			<?php $this->BcBaser->link($blogComment->name, $blogComment->url, ['target' => '_blank', 'escape' => true]) ?>
		<?php else: ?>
			<?php echo h($blogComment->name) ?>
		<?php endif ?>
	</div>
	<div class="bs-blog-comment__list-item-message">
		<?php echo nl2br($this->BcText->autoLinkUrls($blogComment->message)) ?>
	</div>
</div>
	<?php endif ?>
<?php endif ?>
