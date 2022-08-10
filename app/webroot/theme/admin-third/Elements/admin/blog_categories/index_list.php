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
 * [ADMIN] ブログカテゴリ 一覧　テーブル
 */
$this->BcListTable->setColumnNumber(6);
?>


<div class="bca-data-list__top">
	<!-- 一括処理 -->
	<?php if ($this->BcBaser->isAdminUser()): ?>
		<div class="bca-action-table-listup">
			<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['del' => __d('baser', '削除')], 'empty' => __d('baser', '一括処理'), 'data-bca-select-size' => 'lg']) ?>
			<?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled', 'class' => 'bca-btn', 'data-bca-btn-size' => 'lg']) ?>
		</div>
	<?php endif ?>
</div>


<!-- list -->
<table cellpadding="0" cellspacing="0" class="list-table bca-table-listup" id="ListTable">
	<thead class="bca-table-listup__thead">
	<tr>
		<th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select" title="<?php echo __d('baser', '一括選択') ?>">
			<?php echo $this->BcForm->input('ListTool.checkall', ['type' => 'checkbox', 'label' => ' ', 'title' => __d('baser', '一括選択')]) ?>
		</th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'No') ?></th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'カテゴリ名') ?>
			<?php if ($this->BcBaser->siteConfig['category_permission']): ?>
				<br/><?php echo __d('baser', '管理グループ') ?>
			<?php endif ?>
		</th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'カテゴリタイトル') ?></th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', '登録日') ?><br/><?php echo __d('baser', '更新日') ?>
		</th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
	</tr>
	</thead>
	<tbody class="bca-table-listup__tbody">
	<?php if (!empty($dbDatas)): ?>
		<?php $currentDepth = 0 ?>
		<?php foreach($dbDatas as $data): ?>
			<?php
			$rowIdTmps[$data['BlogCategory']['depth']] = $data['BlogCategory']['id'];
			// 階層が上がったタイミングで同階層よりしたのIDを削除
			if ($currentDepth > $data['BlogCategory']['depth']) {
				$i = $data['BlogCategory']['depth'] + 1;
				while(isset($rowIdTmps[$i])) {
					unset($rowIdTmps[$i]);
					$i++;
				}
			}
			$currentDepth = $data['BlogCategory']['depth'];
			$rowGroupId = [];
			foreach($rowIdTmps as $rowIdTmp) {
				$rowGroupId[] = 'row-group-' . $rowIdTmp;
			}
			$rowGroupClass = ' class="depth-' . $data['BlogCategory']['depth'] . ' ' . implode(' ', $rowGroupId) . '"';
			?>
			<?php $currentDepth = $data['BlogCategory']['depth'] ?>
			<?php $this->BcBaser->element('blog_categories/index_row', ['data' => $data, 'rowGroupClass' => $rowGroupClass]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td"><p
					class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
