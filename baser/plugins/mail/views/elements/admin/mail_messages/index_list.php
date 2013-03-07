<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] 受信メール一覧　テーブル
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
<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
		<tr>
			<th style="white-space: nowrap" class="list-tool">
<?php if($bcBaser->isAdminUser()): ?>
				<div>
					<?php echo $bcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
					<?php echo $bcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('del' => '削除'), 'empty' => '一括処理')) ?>
					<?php echo $bcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
				</div>
<?php endif ?>
		</th>
			<th style="white-space: nowrap"><?php echo $paginator->sort(array('asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' NO', 'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' NO'), 'id', array('escape' => false, 'class' => 'btn-direction')) ?></th>
			<th style="white-space: nowrap" colspan="2"><?php echo $paginator->sort(array('asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 受信日時', 'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 受信日時'), 'created', array('escape' => false, 'class' => 'btn-direction')) ?></th>
			<th style="white-space: nowrap">受信内容</th>
		</tr>
	</thead>
	<tbody>
	<?php if($messages): ?>
		<?php $count=0; ?>
		<?php foreach ($messages as $data): ?>
			<?php $bcBaser->element('mail_messages/index_row', array('data' => $data, 'count' => $count)) ?>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr><td colspan="5"><p class="no-data">データが見つかりませんでした。</p></td></tr>
	<?php endif ?>
	</tbody>
</table>

<!-- list-num -->
<?php $bcBaser->element('list_num') ?>
