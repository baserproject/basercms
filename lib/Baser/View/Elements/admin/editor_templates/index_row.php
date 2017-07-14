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
 * [ADMIN] エディタテンプレート一覧　行
 */
?>


<tr<?php $this->BcListTable->rowClass(true, $data) ?>>
	<td class="row-tools">
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['EditorTemplate']['id']), array('title' => '編集')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['EditorTemplate']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['EditorTemplate']['id'] ?></td>
	<td>
		<?php if ($data['EditorTemplate']['image']): ?>
			<?php $this->BcBaser->img('/files/editor/' . $data['EditorTemplate']['image'], array('url' => array('action' => 'edit', $data['EditorTemplate']['id']), 'alt' => $data['EditorTemplate']['name'], 'title' => $data['EditorTemplate']['name'], 'style' => 'float:left;margin-right:10px;height:36px')) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($data['EditorTemplate']['name'], array('action' => 'edit', $data['EditorTemplate']['id'])) ?>
	</td>
	<td><?php echo $data['EditorTemplate']['description']; ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td style="white-space:nowrap"><?php echo $this->BcTime->format('Y-m-d', $data['EditorTemplate']['created']) ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['EditorTemplate']['modified']) ?></td>
</tr>