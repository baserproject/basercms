<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アクセス制限設定一覧
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


<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
		<tr>
			<th class="list-tool">
				<div>
					<?php $bcBaser->link($bcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array('action' => 'add', $this->params['pass'][0])) ?>
	<?php if(!$sortmode): ?>
					<?php $bcBaser->link($bcBaser->getImg('admin/btn_sort.png', array('width' => 65, 'height' => 14, 'alt' => '並び替え', 'class' => 'btn')), array('sortmode' => 1, $this->params['pass'][0])) ?>
	<?php else: ?>
					<?php $bcBaser->link($bcBaser->getImg('admin/btn_normal.png', array('width' => 65, 'height' => 14, 'alt' => 'ノーマル', 'class' => 'btn')), array('sortmode' => 0, $this->params['pass'][0])) ?>
	<?php endif ?>
				</div>
	<?php if($bcBaser->isAdminUser()): ?>
				<div>
					<?php echo $bcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
					<?php echo $bcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('publish' => '有効', 'unpublish' => '無効', 'del' => '削除'), 'empty' => '一括処理')) ?>
					<?php echo $bcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
				</div>
	<?php endif ?>
			</th>
			<th>NO</th>
			<th>ルール名<br />URL設定</th>
			<th>アクセス</th>
			<th>登録日<br />更新日</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($datas)): ?>
		<?php foreach($datas as $data): ?>
			<?php $bcBaser->element('permissions/index_row', array('data' => $data)) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
