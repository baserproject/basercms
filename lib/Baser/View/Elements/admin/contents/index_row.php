<?php
/**
 * [ADMIN] メールフィールド 一覧　行
 *
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright        Copyright 2008 - 2015, baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
$priorities = ['0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', '0.4' => '0.4', '0.5' => '0.5',
	'0.6' => '0.6', '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', '1.0' => '1.0'];
?>


<?php if (!$data['Content']['status']): ?>
	<?php $class = ' class="disablerow"' ?>
<?php else: ?>
	<?php $class = '' ?>
<?php endif; ?>
<tr id="Row<?php echo $count + 1 ?>" <?php echo $class; ?>>
	<td class="row-tools" style="width:22%">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['Content']['id'], ['type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['Content']['id']]) ?>
		<?php endif ?>
		<?php
		echo $this->BcForm->input('Content.priority' . '_' . $data['Content']['id'], [
			'type' => 'select',
			'options' => $priorities,
			'empty' => false,
			'class' => 'priority',
			'value' => $data['Content']['priority']])
		?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn']), ['action' => 'ajax_delete', $data['Content']['id']], ['title' => '削除', 'class' => 'btn-delete']) ?>
		<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['id' => 'PriorityAjaxLoader' . $data['Content']['id'], 'style' => "vertical-align:middle;display:none"]) ?>
	</td>
	<td><?php echo $data['Content']['id'] ?></td>
	<td style="width:15%"><?php echo $data['Content']['type'] ?><br/><?php echo $data['Content']['category'] ?></td>
	<td style="width:15%">
		<?php echo $this->BcBaser->link($this->BcText->noValue($data['Content']['title'], __d('baser', '設定なし')), siteUrl() . preg_replace('/^\//', '', $data['Content']['url']), ['target' => '_blank']) ?></td>
	<td><?php echo $this->Text->truncate($data['Content']['detail'], 50) ?></td>
	<td style="width:10%;text-align:center">
		<?php echo $this->BcText->booleanMark($data['Content']['status']); ?><br/>
	</td>
	<td style="width:10%;white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['Content']['created']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['Content']['modified']) ?>
	</td>
</tr>
