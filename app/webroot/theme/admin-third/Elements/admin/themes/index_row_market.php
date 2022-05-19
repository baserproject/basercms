<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマ一覧　行
 */
// ↓一時対応
if (empty($data['version'])) {
	$data['version'] = '';
}
if (empty($data['authorUrl'])) {
	$data['authorUrl'] = '';
}
?>


<li>
	<p class="theme-name"><strong><?php echo h($data['title']) ?></strong></p>
	<p class="theme-screenshot">
		<a class="theme-popup" href="<?php echo '#Contents' . $key ?>">
			<?php if ($data['enclosure']['@url']): ?>
				<?php $this->BcBaser->img(h($data['enclosure']['@url']), ['alt' => $data['title'], 'height' => 194]) ?>
			<?php else: ?>
				<?php $this->BcBaser->img('admin/no-screenshot.png', ['alt' => $data['title']]) ?>
			<?php endif ?>
		</a>
	</p>
	<p class="row-tools">
		<?php $this->BcBaser->link('', $data['link'], ['target' => '_blank', 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'download', 'data-bca-btn-size' => 'lg']) ?>
	</p>
	<p class="theme-version"><?php echo __d('baser', 'バージョン') ?>：<?php echo h($data['version']) ?></p>
	<p class="theme-author"><?php echo __d('baser', '制作者') ?>：
		<?php if (!empty($data['authorLink']) && !empty($data['author'])): ?>
			<?php $this->BcBaser->link($data['author'], $data['authorLink'], ['target' => '_blank', 'escape' => true]) ?>
		<?php else: ?>
			<?php echo h($data['author']) ?>
		<?php endif ?>
	</p>
	<div style='display:none'>
		<div id="<?php echo 'Contents' . $key ?>" class="theme-popup-contents clearfix">
			<div class="theme-screenshot">
				<?php if ($data['enclosure']['@url']): ?>
					<?php $this->BcBaser->img(h($data['enclosure']['@url']), ['alt' => $data['title'], 'width' => 300]) ?>
				<?php else: ?>
					<?php $this->BcBaser->img('admin/no-screenshot.png', ['alt' => $data['title']]) ?>
				<?php endif ?>
			</div>
			<div class="theme-name"><strong><?php echo h($data['title']) ?></strong></div>
			<div class="theme-version"><?php echo __d('baser', 'バージョン') ?>：<?php echo h($data['version']) ?></div>
			<div class="theme-author"><?php echo __d('baser', '制作者') ?>：
				<?php if (!empty($data['authorLink']) && !empty($data['author'])): ?>
					<?php $this->BcBaser->link($data['author'], $data['authorLink'], ['target' => '_blank', 'escape' => true]) ?>
				<?php else: ?>
					<?php echo h($data['author']) ?>
				<?php endif ?>
			</div>
			<div class="theme-description"><?php echo nl2br($this->BcText->autoLinkUrls($data['description'])) ?></div>
		</div>
	</div>
</li>
