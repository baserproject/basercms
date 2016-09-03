<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログコンテンツ 一覧　行
 */
?>


<?php if (!$data['BlogContent']['status']): ?>
	<?php $class = ' class="unpublish disablerow"'; ?>
	<?php else: ?>
	<?php $class = ' class="publish"'; ?>
<?php endif; ?>
<tr <?php echo $class; ?>>
	<td class="row-tools">
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', array('alt' => '確認', 'class' => 'btn')), '/' . $data['Content']['name'], array('title' => '確認', 'target' => '_blank')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_manage.png', array('alt' => '管理', 'class' => 'btn')), array('controller' => 'blog_posts', 'action' => 'index', $data['BlogContent']['id']), array('title' => '管理')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['BlogContent']['id']), array('title' => '編集')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['BlogContent']['id']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['BlogContent']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['BlogContent']['id']; ?></td>
	<td><?php $this->BcBaser->link($data['Content']['name'], array('action' => 'edit', $data['BlogContent']['id'])) ?></td>
	<td><?php echo $data['BlogContent']['title'] ?></td>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['BlogContent']['created']); ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogContent']['modified']); ?></td>
</tr>