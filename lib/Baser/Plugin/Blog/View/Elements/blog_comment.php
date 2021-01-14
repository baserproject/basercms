<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] ブログコメント単記事
 *
 * Ajax でも利用される
 */
?>

<?php if (!empty($dbData)): ?>
	<?php if ($dbData['status']): ?>
		<div class="comment" id="Comment<?php echo $dbData['no'] ?>">
			<span class="comment-name">≫
				<?php if ($dbData['url']): ?>
					<?php echo $this->BcBaser->link($dbData['name'], $dbData['url'], ['target' => '_blank', 'escape' => true]) ?>
				<?php else: ?>
					<?php echo h($dbData['name']) ?>
				<?php endif ?>
			</span><br/>
			<span class="comment-message"><?php echo nl2br($this->BcText->autoLinkUrls($dbData['message'])) ?></span>
		</div>
	<?php endif ?>
<?php endif ?>
