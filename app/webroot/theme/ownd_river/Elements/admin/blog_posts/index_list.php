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
?>

<!-- 新規追加 -->
<!--
<div class="">
	<?php $this->BcBaser->link(
		'新規記事追加', array('action' => 'add', $blogContent['BlogContent']['id']), array('class'=>'bca-btn--small  bca-btn--add')) ?>
</div>
-->

<!-- 一括処理 -->
<?php if ($this->BcBaser->isAdminUser()): ?>
	<div class="bca-action-table-listup">
		<?php echo $this->BcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('publish' => '公開', 'unpublish' => '非公開', 'del' => '削除'), 'empty' => '一括処理')) ?>
		<?php echo $this->BcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled' , 'class' => 'bca-btn bca-btn--small')) ?>
	</div>
<?php endif ?>

<!-- pagination -->
<?php $this->BcBaser->element('pagination') ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="list-table bca-table-listup" id="ListTable">
<thead>
	<tr>
		<th class="list-tool "><?php // 一括選択 ?>
			<?php echo $this->BcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
		</th>
		<th><?php // No ?>
			<?php echo $this->Paginator->sort('no', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' NO', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' NO'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
		<th><?php // タイトル＋アイキャッチ ?>
			<?php //echo $this->Paginator->sort('eye_catch', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' アイキャッチ', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' アイキャッチ'), array('escape' => false, 'class' => 'btn-direction')) ?>
			<?php echo $this->Paginator->sort('name', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' タイトル', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' タイトル'), array('escape' => false, 'class' => 'btn-direction')) ?>
		</th>
		<th><?php // カテゴリ ?>
			<?php echo $this->Paginator->sort('BlogCategory.name', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' カテゴリ', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' カテゴリ'), array('escape' => false, 'class' => 'btn-direction')) ?>
		</th>
		<?php if ($blogContent['BlogContent']['tag_use']): ?>
		<th><?php // タグ ?>
			タグ
		</th>
		<?php endif ?>
		<?php if ($blogContent['BlogContent']['comment_use']): ?>
			<th>コメント</th>
		<?php endif ?>
		</th>
		<th><?php // 作成者 ?>
			<?php echo $this->Paginator->sort('user_id', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 作成者', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 作成者'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
		<th><?php // 投稿日 ?>
			<?php echo $this->Paginator->sort('posts_date', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 投稿日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 投稿日'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
		<?php /*
		<th><?php // 登録日・更新日 ?>
			<?php echo $this->Paginator->sort('created', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 登録日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 登録日'), array('escape' => false, 'class' => 'btn-direction')) ?><br />
			<?php echo $this->Paginator->sort('modified', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 更新日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 更新日'), array('escape' => false, 'class' => 'btn-direction')) ?>
 */ ?>
		<th><?php // アクション ?>
			アクション
		</th>
		<?php /*
		<th><?php // 公開状態 ?>
			<?php echo $this->Paginator->sort('status', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 公開状態', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 公開状態'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
 */ ?>
	</tr>
</thead>
<tbody>
<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $data): ?>
		<?php $this->BcBaser->element('blog_posts/index_row', array('data' => $data)) ?>
	<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>

<?php if (empty($posts)): ?>
<p class="no-data">データが見つかりませんでした。</p>
<?php endif; ?>

<!-- pagination -->
<?php $this->BcBaser->element('pagination') ?>
<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>
