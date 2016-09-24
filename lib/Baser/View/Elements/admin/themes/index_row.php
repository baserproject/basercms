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
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_apply.png', array('alt' => '適用', 'class' => 'btn')), array('action' => 'apply', $data['name']), array('title' => '適用', 'class' => 'submit-token')) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_manage.png', array('alt' => 'テンプレート編集', 'class' => 'btn')), array('controller' => 'theme_files', 'action' => 'index', $data['name']), array('title' => 'テンプレート編集')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('alt' => 'テーマ情報設定', 'class' => 'btn')), array('action' => 'edit', $data['name']), array('title' => 'テーマ情報設定')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('alt' => 'テーマコピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['name']), array('title' => 'テーマコピー', 'class' => 'btn-copy')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('alt' => 'テーマ削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['name']), array('title' => 'テーマ削除', 'class' => 'btn-delete')) ?>
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
