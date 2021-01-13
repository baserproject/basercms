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
 * [ADMIN] ブログ記事 一覧　行
 * @var \BcAppView $this
 */
?>


<tr<?php $this->BcListTable->rowClass($this->Blog->allowPublish($data), $data) ?>>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['BlogPost']['id'], ['type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['BlogPost']['id']]) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_unpublish.png', ['alt' => __d('baser', '非公開'), 'class' => 'btn']), ['action' => 'ajax_unpublish', $data['BlogContent']['id'], $data['BlogPost']['id']], ['title' => __d('baser', '非公開'), 'class' => 'btn-unpublish']) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_publish.png', ['alt' => __d('baser', '公開'), 'class' => 'btn']), ['action' => 'ajax_publish', $data['BlogContent']['id'], $data['BlogPost']['id']], ['title' => __d('baser', '公開'), 'class' => 'btn-publish']) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', ['alt' => __d('baser', '確認'), 'class' => 'btn']), $this->BcBaser->getContentsUrl($this->request->params['Content']['url'] . 'archives/' . $data['BlogPost']['no'], true, $this->request->params['Site']['use_subdomain']), ['title' => __d('baser', '確認'), 'target' => '_blank']) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), ['action' => 'edit', $data['BlogContent']['id'], $data['BlogPost']['id']], ['title' => __d('baser', '編集')]) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', ['alt' => __d('baser', 'コピー'), 'class' => 'btn']), ['action' => 'ajax_copy', $data['BlogContent']['id'], $data['BlogPost']['id']], ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy']) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $data['BlogContent']['id'], $data['BlogPost']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
	</td>
	<td><?php echo $data['BlogPost']['no']; ?></td>
	<td style="white-space:nowrap"><?php echo $this->BcTime->format('Y-m-d', $data['BlogPost']['posts_date']); ?></td>
	<td class="eye_catch"><?php echo $this->BcUpload->uploadImage('BlogPost.eye_catch', $data['BlogPost']['eye_catch'], ['imgsize' => 'mobile_thumb']) ?></td>
	<td>
		<?php if (!empty($data['BlogCategory']['title'])): ?>
			<?php echo h($data['BlogCategory']['title']); ?>
		<?php endif; ?>
		<?php if ($data['BlogContent']['tag_use'] && !empty($data['BlogTag'])): ?>
			<?php $tags = Hash::extract($data['BlogTag'], '{n}.name') ?>
			<span class="tag"><?php echo implode('</span><span class="tag">', h($tags)) ?></span>
		<?php endif ?>
		<br/>
		<?php $this->BcBaser->link($data['BlogPost']['name'], ['action' => 'edit', $data['BlogContent']['id'], $data['BlogPost']['id']], ['escape' => true]) ?>
	</td>
	<td style="text-align:center" class="status">
		<?php echo h($this->BcBaser->getUserName($data['User'])) ?><br>
		<?php echo $this->BcText->booleanMark($data['BlogPost']['status']); ?>
	</td>

	<?php if ($data['BlogContent']['comment_use']): ?>
		<td>
			<?php $comment = count($data['BlogComment']) ?>
			<?php if ($comment): ?>
				<?php $this->BcBaser->link($comment, ['controller' => 'blog_comments', 'action' => 'index', $data['BlogContent']['id'], $data['BlogPost']['id']]) ?>
			<?php else: ?>
				<?php echo $comment ?>
			<?php endif ?>
		</td>
	<?php endif ?>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td style="white-space:nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogPost']['created']); ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogPost']['modified']); ?>
	</td>
</tr>
