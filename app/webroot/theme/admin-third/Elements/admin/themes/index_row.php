<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマ一覧　行
 */
?>


<li>
	<p class="theme-name"><strong><?php echo h($data['title']) ?></strong>&nbsp;(&nbsp;<?php echo h($data['name']) ?>&nbsp;)
	</p>
	<p class="bca-current-theme__screenshot">
		<a class="theme-popup" href="<?php echo '#Contents' . Inflector::camelize(h($data['name'])) ?>">
			<?php if ($data['screenshot']): ?>
				<?php $this->BcBaser->img('/theme/' . $data['name'] . '/screenshot.png', ['alt' => $data['title']]) ?>
			<?php else: ?>
				<?php $this->BcBaser->img('admin/no-screenshot.png', ['alt' => $data['title']]) ?>
			<?php endif ?>
		</a>
	</p>
	<p class="row-tools">
		<?php if ($data['name'] != $this->BcBaser->siteConfig['theme']): ?>
			<?php $this->BcBaser->link('', ['action' => 'apply', $data['name']], ['title' => __d('baser', '適用'), 'class' => 'submit-token bca-btn-icon', 'data-bca-btn-type' => 'apply', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
		<?php if (Configure::read('BcApp.allowedThemeEdit')): ?>
			<?php $this->BcBaser->link('', ['controller' => 'theme_files', 'action' => 'index', $data['name']], ['title' => __d('baser', 'テンプレート編集'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'file-list', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif; ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_copy', $data['name']], ['title' => __d('baser', 'テーマコピー'), 'class' => 'btn-copy bca-btn-icon', 'data-bca-btn-type' => 'copy', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data['name']], ['title' => __d('baser', 'テーマ削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
	</p>
	<p class="theme-version"><?php echo __d('baser', 'バージョン') ?>：<?php echo h($data['version']) ?></p>
	<p class="theme-author"><?php echo __d('baser', '制作者') ?>：
		<?php if (!empty($data['url']) && !empty($data['author'])): ?>
			<?php $this->BcBaser->link($data['author'], $data['url'], ['target' => '_blank', 'escape' => true]) ?>
		<?php else: ?>
			<?php echo h($data['author']) ?>
		<?php endif ?>
	</p>
	<div style='display:none'>
		<div id="<?php echo 'Contents' . Inflector::camelize($data['name']) ?>" class="theme-popup-contents clearfix">
			<div class="bca-current-theme__screenshot">
				<?php if ($data['screenshot']): ?>
					<?php $this->BcBaser->img('/theme/' . $data['name'] . '/screenshot.png', ['alt' => $data['title']]) ?>
				<?php else: ?>
					<?php $this->BcBaser->img('admin/no-screenshot.png', ['alt' => $data['title']]) ?>
				<?php endif ?>
			</div>
			<div class="theme-name">
				<strong><?php echo h($data['title']) ?></strong>&nbsp;(&nbsp;<?php echo h($data['name']) ?>&nbsp;)
			</div>
			<div class="theme-version"><?php echo __d('baser', 'バージョン') ?>：<?php echo h($data['version']) ?></div>
			<div class="theme-author">
				<?php echo __d('baser', '制作者') ?>：
				<?php if (!empty($data['url']) && !empty($data['author'])): ?>
					<?php $this->BcBaser->link($data['author'], $data['url'], ['target' => '_blank', 'escape' => true]) ?>
				<?php else: ?>
					<?php echo h($data['author']) ?>
				<?php endif ?>
			</div>
			<div class="theme-description"><?php echo nl2br($this->BcText->autoLinkUrls($data['description'])) ?></div>
		</div>
	</div>
</li>
