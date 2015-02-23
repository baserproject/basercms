<?php
/**
 * [ADMIN] Ajax 一覧
 * 
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<thead>
		<tr>
			<th class="list-tool">
	<div>
		<?php if ($newCatAddable): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array('action' => 'add')) ?>
		<?php endif ?>
	</div>
	<?php if ($this->BcBaser->isAdminUser()): ?>
		<div>
			<?php echo $this->BcForm->checkbox('ListTool.checkall') ?>&nbsp;
			<?php echo $this->BcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('del' => '削除'), 'empty' => '一括処理')) ?>
			<?php echo $this->BcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
		</div>
	<?php endif ?>
</th>
<th>NO</th>
<th>ページカテゴリー名
	<?php if ($this->BcBaser->siteConfig['category_permission']): ?>
		<br />管理グループ
	<?php endif ?>
</th>
<th>ページカテゴリータイトル</th>
<th>登録日<br />更新日</th>
</tr>
</thead>
<tbody>
	<?php if (!empty($datas)): ?>
		<?php $currentDepth = 0 ?>
		<?php foreach ($datas as $key => $data): ?>
			<?php
			$rowIdTmps[$data['PageCategory']['depth']] = $data['PageCategory']['id'];

// 階層が上がったタイミングで同階層よりしたのIDを削除
			if ($currentDepth > $data['PageCategory']['depth']) {
				$i = $data['PageCategory']['depth'] + 1;
				while (isset($rowIdTmps[$i])) {
					unset($rowIdTmps[$i]);
					$i++;
				}
			}
			$currentDepth = $data['PageCategory']['depth'];
			$rowGroupId = array();
			foreach ($rowIdTmps as $rowIdTmp) {
				$rowGroupId[] = 'row-group-' . $rowIdTmp;
			}
			$rowGroupClass = ' class="depth-' . $data['PageCategory']['depth'] . ' ' . implode(' ', $rowGroupId) . '"';
			?>
			<?php $currentDepth = $data['PageCategory']['depth'] ?>
			<?php $this->BcBaser->element('page_categories/index_row', array('datas' => $datas, 'data' => $data, 'count' => ($key + 1), 'rowGroupClass' => $rowGroupClass)) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="5"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>