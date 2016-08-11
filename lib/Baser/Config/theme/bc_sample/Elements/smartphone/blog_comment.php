<?php
/**
 * ブログコメント（スマホ用）
 * 呼出箇所：ブログ記事詳細
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
	</span><br>
			<span class="comment-message"><?php echo nl2br($this->BcText->autoLinkUrls($dbData['message'])) ?></span>
		</div>
	<?php endif ?>
<?php endif ?>