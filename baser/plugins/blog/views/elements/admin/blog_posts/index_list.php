<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事 一覧　テーブル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<!-- pagination -->
<?php $bcBaser->element('pagination') ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<thead>
		<tr>
			<th class="list-tool">
				<div>
<?php if($newCatAddable): ?>
					<?php $bcBaser->link($bcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array('action' => 'add', $blogContent['BlogContent']['id'])) ?>
<?php endif ?>
				</div>
<?php if($bcBaser->isAdminUser()): ?>
				<div>
					<?php echo $bcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
					<?php echo $bcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('publish' => '公開', 'unpublish' => '非公開', 'del' => '削除'), 'empty' => '一括処理')) ?>
					<?php echo $bcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
				</div>
<?php endif ?>
			</th>
			<th><?php echo $paginator->sort(array('asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' NO', 'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' NO'), 'no', array('escape' => false, 'class' => 'btn-direction')) ?></th>
			<th>
				<?php echo $paginator->sort(array('asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' カテゴリー', 'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' カテゴリー'), 'BlogCategory.name', array('escape' => false, 'class' => 'btn-direction')) ?><?php if($blogContent['BlogContent']['tag_use']): ?><span class="tag">タグ</span><?php endif ?><br />
				<?php echo $paginator->sort(array('asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' タイトル', 'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' タイトル'), 'name', array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
			<th><?php echo $paginator->sort(array('asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 作成者', 'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 作成者'), 'user_id', array('escape' => false, 'class' => 'btn-direction')) ?></th>
			<th><?php echo $paginator->sort(array('asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 公開状態', 'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 公開状態'), 'status', array('escape' => false, 'class' => 'btn-direction')) ?></th>
<?php if($blogContent['BlogContent']['comment_use']): ?>
			<th>コメント</th>
<?php endif ?>
			<th>
				<?php echo $paginator->sort(array('asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 投稿日', 'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 投稿日'), 'posts_date', array('escape' => false, 'class' => 'btn-direction')) ?><br />
				<?php echo $paginator->sort(array('asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 更新日', 'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 更新日'), 'modified', array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
		</tr>
	</thead>
	<tbody>
<?php if(!empty($posts)): ?>
	<?php foreach($posts as $data): ?>
		<?php $bcBaser->element('blog_posts/index_row', array('data' => $data)) ?>
	<?php endforeach; ?>
<?php else: ?>
		<tr>
			<td colspan="9"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
<?php endif; ?>
	</tbody>
</table>

<!-- list-num -->
<?php $bcBaser->element('list_num') ?>
