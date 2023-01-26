<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事 一覧　行
 */
?>


<tr<?php $this->BcListTable->rowClass($this->Blog->allowPublish($data), $data) ?>>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--select"><?php // 選択 ?>
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->input('ListTool.batch_targets.' . $data['BlogPost']['id'], ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['BlogPost']['id']]) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--no"><?php // No ?><?php echo $data['BlogPost']['no']; ?></td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--title"><?php // アイキャッチ＋タイトル ?>
		<div class="eye_catch-wrap">
			<?php if (!empty($data['BlogPost']['eye_catch'])): ?>
				<div
					class="eye_catch"><?php echo $this->BcUpload->uploadImage('BlogPost.eye_catch', $data['BlogPost']['eye_catch'], ['imgsize' => 'mobile_thumb']) ?></div>
			<?php endif; ?>
			<?php $this->BcBaser->link($data['BlogPost']['name'], ['action' => 'edit', $data['BlogContent']['id'], $data['BlogPost']['id']], ['escape' => true]) ?>
		</div>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--category"><?php // カテゴリ ?>
		<?php if (!empty($data['BlogCategory']['title'])): ?>
			<?php echo h($data['BlogCategory']['title']) ?>
		<?php endif; ?>
	</td>

	<?php if ($data['BlogContent']['tag_use']): ?>
		<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--tag"><?php // タグ ?>
			<?php if (!empty($data['BlogTag'])): ?>
				<?php $tags = Hash::extract($data['BlogTag'], '{n}.name') ?>
				<span class="tag"><?php echo implode('</span><span class="tag">', h($tags)) ?></span>
			<?php endif ?>
		</td>
	<?php endif ?>

	<?php if ($data['BlogContent']['comment_use']): ?>
		<td class="bca-table-listup__tbody-td"><?php // コメント ?>
			<?php $comment = count($data['BlogComment']) ?>
			<?php if ($comment): ?>
				<?php $this->BcBaser->link($comment, ['controller' => 'blog_comments', 'action' => 'index', $data['BlogContent']['id'], $data['BlogPost']['id']]) ?>
			<?php else: ?>
				<?php echo $comment ?>
			<?php endif ?>
		</td>
	<?php endif ?>

	<td class="bca-table-listup__tbody-td"><?php // 作者 ?>
		<?php echo h($this->BcBaser->getUserName($data['User'])) ?>
	</td>

	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--date"><?php // 投稿日 ?>
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogPost']['posts_date']); ?>
	</td>

	<?php echo $this->BcListTable->dispatchShowRow($data) ?>

	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions"><?php // アクション ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_unpublish', $data['BlogContent']['id'], $data['BlogPost']['id']], ['title' => __d('baser', '非公開'), 'class' => 'btn-unpublish bca-btn-icon', 'data-bca-btn-type' => 'unpublish', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_publish', $data['BlogContent']['id'], $data['BlogPost']['id']], ['title' => __d('baser', '公開'), 'class' => 'btn-publish bca-btn-icon', 'data-bca-btn-type' => 'publish', 'data-bca-btn-size' => 'lg']) ?>
		<?php if ($this->Blog->allowPublish($data)): //公開状態であれば 公開ページヘのリンク ?>
			<?php $this->BcBaser->link('', $this->request->params['Content']['url'] . '/archives/' . $data['BlogPost']['no'], ['title' => __d('baser', '確認'), 'target' => '_blank', 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']); ?>
		<?php else: // 非公開であればボタンを押せなくする ?>
			<a title="確認" class="btn bca-btn-icon" data-bca-btn-type="preview" data-bca-btn-size="lg"
			   data-bca-btn-status="gray"></a>
		<?php endif ?>
		<?php $this->BcBaser->link('', ['action' => 'edit', $data['BlogContent']['id'], $data['BlogPost']['id']], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('',
			['action' => 'ajax_copy', $data['BlogContent']['id'], $data['BlogPost']['id']],
			['title' => __d('baser', 'コピー'), 'class' => 'btn-copy bca-icon--copy bca-btn-icon', 'data-bca-btn-type' => 'copy', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data['BlogContent']['id'], $data['BlogPost']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
	</td>
</tr>
