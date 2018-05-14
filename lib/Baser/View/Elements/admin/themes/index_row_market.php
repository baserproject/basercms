<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマ一覧　行
 */
// ↓一時対応
if(empty($data['version'])) {
	$data['version'] = '';
}
if(empty($data['authorUrl'])) {
	$data['authorUrl'] = '';
}
?>


<li>
	<p class="theme-name"><strong><?php echo $data['title'] ?></strong></p>
	<p class="theme-screenshot">
		<a class="theme-popup" href="<?php echo '#Contents' . $key ?>">
			<?php if ($data['enclosure']['@url']): ?>
				<?php $this->BcBaser->img($data['enclosure']['@url'], ['alt' => $data['title'], 'height' => 194]) ?>
			<?php else: ?>
				<?php $this->BcBaser->img('admin/no-screenshot.png', ['alt' => $data['title']]) ?>
			<?php endif ?>
		</a>
	</p>
	<p class="row-tools">
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_down.png', ['title' => __d('baser', 'ダウンロード'), 'alt' => __d('baser', 'ダウンロード')]), $data['link'], ['target' => '_blank']) ?>
	</p>
<p class="theme-version"><?php echo __d('baser', 'バージョン')?>：<?php echo $data['version'] ?></p>
<p class="theme-author"><?php echo __d('baser', '制作者')?>：
	<?php if (!empty($data['authorLink']) && !empty($data['author'])): ?>
		<?php $this->BcBaser->link($data['author'], $data['authorLink'], ['target' => '_blank']) ?>
	<?php else: ?>
		<?php echo $data['author'] ?>
	<?php endif ?>
</p>
<div style='display:none'>
	<div id="<?php echo 'Contents' . $key ?>" class="theme-popup-contents clearfix">
		<div class="theme-screenshot">
			<?php if ($data['enclosure']['@url']): ?>
				<?php $this->BcBaser->img($data['enclosure']['@url'], ['alt' => $data['title'], 'width' => 300]) ?>
			<?php else: ?>
				<?php $this->BcBaser->img('admin/no-screenshot.png', ['alt' => $data['title']]) ?>
			<?php endif ?>
		</div>
		<div class="theme-name"><strong><?php echo $data['title'] ?></strong></div>
		<div class="theme-version"><?php echo __d('baser', 'バージョン')?>：<?php echo $data['version'] ?></div>
		<div class="theme-author"><?php echo __d('baser', '制作者')?>：
	<?php if (!empty($data['authorLink']) && !empty($data['author'])): ?>
		<?php $this->BcBaser->link($data['author'], $data['authorLink'], ['target' => '_blank']) ?>
	<?php else: ?>
		<?php echo h($data['author']) ?>
	<?php endif ?>
		</div>
		<div class="theme-description"><?php echo nl2br($data['description']) ?></div>
	</div>
</div>
</li>
