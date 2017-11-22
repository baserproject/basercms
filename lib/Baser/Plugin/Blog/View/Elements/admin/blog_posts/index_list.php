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
 * [ADMIN] ブログ記事 一覧　テーブル
 * @var \BcAppView $this
 */
$this->BcListTable->setColumnNumber(9);
?>


<!-- pagination -->
<?php $this->BcBaser->element('pagination') ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
<thead>
	<tr>
		<th class="list-tool">
			<div>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', ['width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn']), ['action' => 'add', $blogContent['BlogContent']['id']]) ?>
			</div>
			<?php if ($this->BcBaser->isAdminUser()): ?>
			<div>
				<?php echo $this->BcForm->checkbox('ListTool.checkall', ['title' => '一括選択']) ?>
				<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['publish' => '公開', 'unpublish' => '非公開', 'del' => '削除'], 'empty' => '一括処理']) ?>
				<?php echo $this->BcForm->button('適用', ['id' => 'BtnApplyBatch', 'disabled' => 'disabled']) ?>
			</div>
			<?php endif ?>
		</th>
		<th><?php echo $this->Paginator->sort('no', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => '昇順', 'title' => '昇順']) . ' NO', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => '降順', 'title' => '降順']) . ' NO'], ['escape' => false, 'class' => 'btn-direction']) ?></th>
		<th><?php echo $this->Paginator->sort('posts_date', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => '昇順', 'title' => '昇順']) . ' 投稿日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => '降順', 'title' => '降順']) . ' 投稿日'], ['escape' => false, 'class' => 'btn-direction']) ?></th>
		<th><?php echo $this->Paginator->sort('eye_catch', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => '昇順', 'title' => '昇順']) . ' アイキャッチ', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => '降順', 'title' => '降順']) . ' アイキャッチ'], ['escape' => false, 'class' => 'btn-direction']) ?></th>
		<th>
			<?php echo $this->Paginator->sort('BlogCategory.name', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => '昇順', 'title' => '昇順']) . ' カテゴリ', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => '降順', 'title' => '降順']) . ' カテゴリ'], ['escape' => false, 'class' => 'btn-direction']) ?>
			<?php if ($blogContent['BlogContent']['tag_use']): ?>
			<span class="tag">タグ</span>
			<?php endif ?><br />
			<?php echo $this->Paginator->sort('name', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => '昇順', 'title' => '昇順']) . ' タイトル', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => '降順', 'title' => '降順']) . ' タイトル'], ['escape' => false, 'class' => 'btn-direction']) ?>
		</th>
		<th>
            <?php echo $this->Paginator->sort('user_id', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => '昇順', 'title' => '昇順']) . ' 作成者', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => '降順', 'title' => '降順']) . ' 作成者'], ['escape' => false, 'class' => 'btn-direction']) ?><br>
			<?php echo $this->Paginator->sort('status', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => '昇順', 'title' => '昇順']) . ' 公開状態', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => '降順', 'title' => '降順']) . ' 公開状態'], ['escape' => false, 'class' => 'btn-direction']) ?>
        </th>
		<?php if ($blogContent['BlogContent']['comment_use']): ?>
			<th>コメント</th>
		<?php endif ?>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th>
			<?php echo $this->Paginator->sort('created', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => '昇順', 'title' => '昇順']) . ' 登録日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => '降順', 'title' => '降順']) . ' 登録日'], ['escape' => false, 'class' => 'btn-direction']) ?><br />
			<?php echo $this->Paginator->sort('modified', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => '昇順', 'title' => '昇順']) . ' 更新日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => '降順', 'title' => '降順']) . ' 更新日'], ['escape' => false, 'class' => 'btn-direction']) ?>
		</th>
	</tr>
</thead>
<tbody>
<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $data): ?>
		<?php $this->BcBaser->element('blog_posts/index_row', ['data' => $data]) ?>
	<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
<?php endif; ?>
</tbody>
</table>

<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>
