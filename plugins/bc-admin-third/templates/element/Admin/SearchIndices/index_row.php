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
 * [ADMIN] メールフィールド 一覧　行
 *
 * @var BcAppView $this
 * @var array $data
 * @var int $count
 */
$priorities = [
	'0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', '0.4' => '0.4', '0.5' => '0.5',
	'0.6' => '0.6', '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', '1.0' => '1.0'
];
?>


<tr id="Row<?php echo $count + 1 ?>"<?php $this->BcListTable->rowClass($this->BcSearchIndex->allowPublish($data), $data) ?>>
	<td class="row-tools bca-table-listup__tbody-td">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->input('ListTool.batch_targets.' . $data['SearchIndex']['id'], ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['SearchIndex']['id']]) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo $data['SearchIndex']['id'] ?></td>
	<td class="bca-table-listup__tbody-td" style="width:15%">
		<?php echo $data['SearchIndex']['type'] ?><br>
		<?php $this->BcBaser->link($this->BcText->noValue($data['SearchIndex']['title'], __d('baser', '設定なし')), siteUrl() . preg_replace('/^\//', '', $data['SearchIndex']['url']), ['target' => '_blank', 'escape' => true]) ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo h($this->Text->truncate($data['SearchIndex']['detail'], 50)) ?></td>
	<td class="bca-table-listup__tbody-td" style="width:10%;text-align:center">
		<?php echo $this->BcText->booleanMark($data['SearchIndex']['status']); ?><br>
	</td>
	<td class="bca-table-listup__tbody-td" nowrap>
		<?php echo $this->BcTime->format('Y-m-d', $data['SearchIndex']['publish_begin']) ?><br>
		<?php echo $this->BcTime->format('Y-m-d', $data['SearchIndex']['publish_end']) ?>
	</td>
	<td class="bca-table-listup__tbody-td" style="width:10%;white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['SearchIndex']['created']) ?><br>
		<?php echo $this->BcTime->format('Y-m-d', $data['SearchIndex']['modified']) ?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="bca-table-listup__tbody-td">
		<?php
		echo $this->BcForm->input('SearchIndex.priority' . '_' . $data['SearchIndex']['id'], [
			'type' => 'select',
			'options' => $priorities,
			'empty' => false,
			'class' => 'priority bca-select__select',
			'value' => $data['SearchIndex']['priority']
		]) ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data['SearchIndex']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['id' => 'PriorityAjaxLoader' . $data['SearchIndex']['id'], 'style' => "vertical-align:middle;display:none"]) ?>
	</td>
</tr>
