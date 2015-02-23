<?php
/**
 * [PUBLISH] ブログコメント単記事
 * 
 * Ajax でも利用される
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>

<?php if (!empty($dbData)): ?>
	<?php if ($dbData['status']): ?>
		<div class="comment" id="Comment<?php echo $dbData['no'] ?>">
			<span class="comment-name">≫
				<?php if ($dbData['url']): ?>
					<?php echo $this->BcBaser->link($dbData['name'], $dbData['url'], array('target' => '_blank')) ?>
				<?php else: ?>
					<?php echo $dbData['name'] ?>
				<?php endif ?>
			</span><br />
			<span class="comment-message"><?php echo nl2br($this->BcText->autoLinkUrls($dbData['message'])) ?></span>
		</div>
	<?php endif ?>
<?php endif ?>