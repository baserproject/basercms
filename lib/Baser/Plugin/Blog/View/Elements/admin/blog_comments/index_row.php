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
 * [ADMIN] ブログ記事コメント 一覧　行
 * @var \BcAppView $this
 */
if (!$data['BlogComment']['status']) {
	$class = ' class="disablerow unpublish"';
} else {
	$class = ' class="publish"';
}
?>


<tr<?php echo $class; ?>>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['BlogComment']['id'], ['type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['BlogComment']['id']]) ?>
		<?php endif ?>
		<?php if (!empty($this->params['pass'][1])): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_unpublish.png', ['alt' => __d('baser', '非公開'), 'class' => 'btn']), ['action' => 'ajax_unpublish', $blogContent['BlogContent']['id'], $data['BlogComment']['blog_post_id'], $data['BlogComment']['id']], ['title' => __d('baser', '非公開'), 'class' => 'btn-unpublish']) ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_publish.png', ['alt' => __d('baser', '公開'), 'class' => 'btn']), ['action' => 'ajax_publish', $blogContent['BlogContent']['id'], $data['BlogComment']['blog_post_id'], $data['BlogComment']['id']], ['title' => __d('baser', '公開'), 'class' => 'btn-publish']) ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $blogContent['BlogContent']['id'], $data['BlogComment']['blog_post_id'], $data['BlogComment']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
		<?php else: ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_unpublish.png', ['alt' => __d('baser', '非公開'), 'class' => 'btn']), ['action' => 'ajax_unpublish', $blogContent['BlogContent']['id'], 0, $data['BlogComment']['id']], ['title' => __d('baser', '非公開'), 'class' => 'btn-unpublish']) ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_publish.png', ['alt' => __d('baser', '公開'), 'class' => 'btn']), ['action' => 'ajax_publish', $blogContent['BlogContent']['id'], 0, $data['BlogComment']['id']], ['title' => __d('baser', '公開'), 'class' => 'btn-publish']) ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $blogContent['BlogContent']['id'], 0, $data['BlogComment']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
		<?php endif ?>
	</td>
	<td><?php echo $data['BlogComment']['no'] ?></td>
	<td>
		<?php if (!empty($data['BlogComment']['url'])): ?>
			<?php $this->BcBaser->link($data['BlogComment']['name'], $data['BlogComment']['url'], ['target' => '_blank', 'escape' => true]) ?>
		<?php else: ?>
			<?php echo h($data['BlogComment']['name']) ?>
		<?php endif ?>
	</td>
	<td>
		<?php if (!empty($data['BlogComment']['email'])): ?>
			<?php $this->BcBaser->link($data['BlogComment']['email'], 'mailto:' . $data['BlogComment']['email'], ['escape' => true]) ?>
		<?php endif; ?>
		<br/>
		<?php echo $this->BcText->autoLinkUrls($data['BlogComment']['url']) ?>
	</td>
	<td>
		<strong>
			<?php $this->BcBaser->link($data['BlogPost']['name'], ['controller' => 'blog_posts', 'action' => 'edit', $blogContent['BlogContent']['id'], $data['BlogPost']['id']], ['escape' => true]) ?>
		</strong><br/>
		<?php echo nl2br($this->BcText->autoLinkUrls($data['BlogComment']['message'])) ?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td style="white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogComment']['created']); ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogComment']['modified']); ?>
	</td>
</tr>
