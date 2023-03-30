<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * ブログコメント
 * 呼出箇所：ブログ記事詳細
 *
 * @var BcAppView $this
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
