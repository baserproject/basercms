<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] アクセス制限設定一覧　行
 *
 * @var BcAppView $this
 * @var bool $sortmode
 */
?>


<?php if (!$data['Permission']['status']): ?>
	<?php $class = ' class="disablerow unpublish sortable"'; ?>
<?php else: ?>
	<?php $class = ' class="publish sortable"'; ?>
<?php endif; ?>
<tr<?php echo $class; ?>>
	<td class="row-tools bca-table-listup__tbody-td ">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->input('ListTool.batch_targets.' . $data['Permission']['id'], ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['Permission']['id']]) ?>
		<?php endif ?>
		<?php if ($sortmode): ?>
			<span class="sort-handle"><i class="bca-btn-icon-text"
										 data-bca-btn-type="draggable"></i><?php echo __d('baser', 'ドラッグ可能') ?></span>
			<?php echo $this->BcForm->input('Sort.id' . $data['Permission']['id'], ['type' => 'hidden', 'class' => 'id', 'value' => $data['Permission']['id']]) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo $data['Permission']['no']; ?></td>
	<td class="bca-table-listup__tbody-td">
		<?php $this->BcBaser->link($data['Permission']['name'], ['action' => 'edit', $this->request->params['pass'][0], $data['Permission']['id']], ['escape' => true]) ?>
		<br>
		<?php echo h($data['Permission']['url']); ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcText->arrayValue($data['Permission']['auth'], [0 => '×', 1 => '〇']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="bca-table-listup__tbody-td">
		<?php echo $this->BcTime->format('Y-m-d', $data['Permission']['created']); ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['Permission']['modified']); ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php $this->BcBaser->link('', ['action' => 'ajax_unpublish', $data['Permission']['id']], ['title' => __d('baser', '無効'), 'class' => 'btn-unpublish bca-btn-icon', 'data-bca-btn-type' => 'unpublish', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_publish', $data['Permission']['id']], ['title' => __d('baser', '有効'), 'class' => 'btn-publish bca-btn-icon', 'data-bca-btn-type' => 'publish', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'edit', $this->request->params['pass'][0], $data['Permission']['id']], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_copy', $this->request->params['pass'][0], $data['Permission']['id']], ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy bca-icon--copy bca-btn-icon', 'data-bca-btn-type' => 'copy', 'data-bca-btn-size' => 'lg']) ?>
		<?php if ($data['Permission']['name'] != 'admins'): ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data['Permission']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
	</td>
</tr>
