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
 * [ADMIN] ブログ記事 一覧　行
 */

/*
記事表示列の順番
・一括選択
・No
・タイトル＋アイキャッチ
・カテゴリ
・タグ
・コメント
・作者
・投稿日
・アクション
 */

$classies = array();
if (!$this->Blog->allowPublish($data)) {
	$classies = array('unpublish', 'disablerow');
} else {
	$classies = array('publish');
}
$class = ' class="' . implode(' ', $classies) . '"';
?>


<tr<?php echo $class; ?>>
	<td><?php // 選択 ?>
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['BlogPost']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['BlogPost']['id'])) ?>
		<?php endif ?>
	</td>
	<td><?php // No ?><?php echo $data['BlogPost']['no']; ?></td>
	<td><?php // アイキャッチ＋タイトル ?>
		<div class="eye_catch"><?php echo $this->BcUpload->uploadImage('BlogPost.eye_catch',  $data['BlogPost']['eye_catch'], array('imgsize' => 'mobile_thumb')) ?></div>
		<?php $this->BcBaser->link($data['BlogPost']['name'], array('action' => 'edit', $data['BlogContent']['id'], $data['BlogPost']['id'])) ?>
	</td>
	<td><?php // カテゴリ ?>
		<?php if (!empty($data['BlogCategory']['title'])): ?>
			<?php echo $data['BlogCategory']['title']; ?>
		<?php endif; ?>
	</td>

	<?php if ($data['BlogContent']['tag_use'] ): ?>
	<td><?php // タグ ?>
		<?php if (!empty($data['BlogTag'])): ?>
		<?php $tags = Hash::extract($data['BlogTag'], '{n}.name') ?>
		<span class="tag"><?php echo implode('</span><span class="tag">', $tags) ?></span>
		<?php endif ?>
	</td>
	<?php endif ?>

	<?php if ($data['BlogContent']['comment_use']): ?>
		<td><?php // コメント ?>
			<?php $comment = count($data['BlogComment']) ?>
			<?php if ($comment): ?>
				<?php $this->BcBaser->link($comment, array('controller' => 'blog_comments', 'action' => 'index', $data['BlogContent']['id'], $data['BlogPost']['id'])) ?>
			<?php else: ?>
				<?php echo $comment ?>
			<?php endif ?>
		</td>
	<?php endif ?>

	<td><?php // 作者 ?>
		<?php echo $this->BcBaser->getUserName($data['User']) ?>
	</td>

	<td><?php // 投稿日 ?>
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogPost']['posts_date']); ?>
	</td>

<?php /*
	<td class="status"><?php // 公開状態 ?>
		<?php echo $this->BcText->booleanMark($data['BlogPost']['status']); ?>
	</td>
	<td style="white-space:nowrap"><?php // 登録日、更新日 ?>
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogPost']['created']); ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogPost']['modified']); ?>
	</td>
*/ ?>
	<td class="row-tools bca-table-listup-actions"><?php // アクション ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_unpublish.png', array('alt' => '非公開', 'class' => 'btn')), array('action' => 'ajax_unpublish', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => '非公開', 'class' => 'btn-unpublish')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_publish.png', array('alt' => '公開', 'class' => 'btn')), array('action' => 'ajax_publish', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => '公開', 'class' => 'btn-publish')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', array('alt' => '確認', 'class' => 'btn')), $this->request->params['Content']['url'] . '/archives/' . $data['BlogPost']['no'], array('title' => '確認', 'target' => '_blank')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => '編集')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => 'コピー', 'class' => 'btn-copy bca-icon--copy')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
</tr>
