<?php
/**
 * [ADMIN] ブログ記事 一覧　テーブル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<!-- pagination -->
<?php $this->BcBaser->element('pagination') ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
<thead>
	<tr>
		<th class="list-tool">
			<div>
			<?php if ($newCatAddable): ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array('action' => 'add', $blogContent['BlogContent']['id'])) ?>
			<?php endif ?>
			</div>
			<?php if ($this->BcBaser->isAdminUser()): ?>
			<div>
				<?php echo $this->BcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
				<?php echo $this->BcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('publish' => '公開', 'unpublish' => '非公開', 'del' => '削除'), 'empty' => '一括処理')) ?>
				<?php echo $this->BcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
			</div>
			<?php endif ?>
		</th>
		<th><?php echo $this->Paginator->sort('no', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' NO', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' NO'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
		<th><?php echo $this->Paginator->sort('posts_date', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 投稿日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 投稿日'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
		<?php $existEyeCatch = array_filter(Hash::extract($posts, '{n}.BlogPost.eye_catch')); ?>
		<th><?php echo $this->Paginator->sort('eye_catch', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' アイキャッチ', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' アイキャッチ'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
		<th>
			<?php echo $this->Paginator->sort('BlogCategory.name', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' カテゴリ', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' カテゴリ'), array('escape' => false, 'class' => 'btn-direction')) ?>
			<?php if ($blogContent['BlogContent']['tag_use']): ?>
			<span class="tag">タグ</span>
			<?php endif ?><br />
			<?php echo $this->Paginator->sort('name', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' タイトル', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' タイトル'), array('escape' => false, 'class' => 'btn-direction')) ?>
		</th>
		<th><?php echo $this->Paginator->sort('user_id', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 作成者', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 作成者'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
		<th><?php echo $this->Paginator->sort('status', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 公開状態', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 公開状態'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
		<?php if ($blogContent['BlogContent']['comment_use']): ?>
			<th>コメント</th>
		<?php endif ?>
		<th>
			<?php echo $this->Paginator->sort('created', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 登録日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 登録日'), array('escape' => false, 'class' => 'btn-direction')) ?><br />
			<?php echo $this->Paginator->sort('modified', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 更新日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 更新日'), array('escape' => false, 'class' => 'btn-direction')) ?>
		</th>
	</tr>
</thead>
<tbody>
<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $data): ?>
		<?php $this->BcBaser->element('blog_posts/index_row', array('data' => $data, 'existEyeCatch' => $existEyeCatch)) ?>
	<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="7"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
<?php endif; ?>
</tbody>
</table>

<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>
