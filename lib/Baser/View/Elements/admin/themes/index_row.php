<?php
/**
 * [ADMIN] テーマ一覧　行
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<li>
	<p class="theme-name"><strong><?php echo $data['title'] ?></strong>&nbsp;(&nbsp;<?php echo $data['name'] ?>&nbsp;)</p>
	<p class="theme-screenshot">
		<a class="theme-popup" href="<?php echo '#Contents' . Inflector::camelize($data['name']) ?>">
			<?php if ($data['screenshot']): ?>
				<?php $this->BcBaser->img('/theme/' . $data['name'] . '/screenshot.png', array('alt' => $data['title'])) ?>
			<?php else: ?>
				<?php $this->BcBaser->img('admin/no-screenshot.png', array('alt' => $data['title'])) ?>
			<?php endif ?>
		</a>
	</p>
	<p class="row-tools">
		<?php if ($data['name'] != $this->BcBaser->siteConfig['theme']): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_apply.png', array('width' => 24, 'height' => 24, 'alt' => '適用', 'class' => 'btn')), array('action' => 'apply', $data['name']), array('title' => '適用', 'class' => 'submit-token')) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_manage.png', array('width' => 24, 'height' => 24, 'alt' => '管理', 'class' => 'btn')), array('controller' => 'theme_files', 'action' => 'index', $data['name']), array('title' => '管理')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['name']), array('title' => '編集')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['name']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['name']), array('title' => '削除', 'class' => 'btn-delete')) ?>
		<?php if (!$data['is_writable_pages']): ?>
		<br /><div class="error-message lastChild clearfix" style="clear:both">「Pages」フォルダに書き込み権限を与えてください。</div>
	<?php endif ?>
</p>
<p class="theme-version">バージョン：<?php echo $data['version'] ?></p>
<p class="theme-author">制作者：
	<?php if (!empty($data['url']) && !empty($data['author'])): ?>
		<?php $this->BcBaser->link($data['author'], $data['url'], array('target' => '_blank')) ?>
	<?php else: ?>
		<?php echo $data['author'] ?>
	<?php endif ?>
</p>
<div style='display:none'>
	<div id="<?php echo 'Contents' . Inflector::camelize($data['name']) ?>" class="theme-popup-contents clearfix">
		<div class="theme-screenshot">
			<?php if ($data['screenshot']): ?>
				<?php $this->BcBaser->img('/theme/' . $data['name'] . '/screenshot.png', array('alt' => $data['title'])) ?>
			<?php else: ?>
				<?php $this->BcBaser->img('admin/no-screenshot.png', array('alt' => $data['title'])) ?>
			<?php endif ?>
		</div>
		<div class="theme-name"><strong><?php echo $data['title'] ?></strong>&nbsp;(&nbsp;<?php echo $data['name'] ?>&nbsp;)</div>
		<div class="theme-version">バージョン：<?php echo $data['version'] ?></div>
		<div class="theme-author">制作者：
			<?php if (!empty($data['url']) && !empty($data['author'])): ?>
				<?php $this->BcBaser->link($data['author'], $data['url'], array('target' => '_blank')) ?>
			<?php else: ?>
				<?php echo $data['author'] ?>
			<?php endif ?>
		</div>
		<div class="theme-description"><?php echo nl2br($data['description']) ?></div>
	</div>
</div>
</li>
