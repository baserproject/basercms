<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツ一覧 テーブル行
 *
 * @var BcAppView $this
 */

$isSiteRelated = $this->BcContents->isSiteRelated($data);
$isPublish = $this->BcContents->isAllowPublish($data, true);
$isSiteRoot = $data['Content']['site_root'];
$isAlias = (boolean)$data['Content']['alias_id'];
if (!empty($this->BcContents->settings[$data['Content']['type']])) {
	$type = $data['Content']['type'];
} else {
	$type = 'Default';
}
if ($isAlias) {
	$editDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'edit', $data['Content']['entity_id']);
	$manageDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'manage', $data['Content']['entity_id']);
} else {
	$editDisabled = !$this->BcContents->isActionAvailable($data['Content']['type'], 'edit', $data['Content']['entity_id']);
	$manageDisabled = !$this->BcContents->isActionAvailable($data['Content']['type'], 'manage', $data['Content']['entity_id']);
}
$typeTitle = $this->BcContents->settings[$type]['title'];
if (!empty($this->BcContents->settings[$type]['icon'])) {
	$iconPath = $this->BcContents->settings[$type]['icon'];
} else {
	$iconPath = $this->BcContents->settings['Default']['icon'];
}
$isImageIcon = false;
if (preg_match('/^admin\//', $iconPath)) {
	$isImageIcon = true;
	if ($data['Content']['plugin'] != 'Core' && $type != 'Default') {
		$iconPath = $data['Content']['plugin'] . '.' . $iconPath;
	}
}
$urlParams = ['content_id' => $data['Content']['id']];
if ($data['Content']['entity_id']) {
	$urlParams = array_merge($urlParams, [$data['Content']['entity_id']]);
}
$fullUrl = $this->BcContents->getUrl($data['Content']['url'], true, $data['Site']['use_subdomain']);
$toStatus = 'publish';
if ($data['Content']['self_status']) {
	$toStatus = 'unpublish';
}
?>


<tr id="Row<?php echo $count + 1 ?>"<?php $this->BcListTable->rowClass($isPublish, $data) ?>>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--select"><?php // 選択 ?>
		<?php if ($this->BcBaser->isAdminUser() && empty($data['Content']['site_root'])): ?>
			<?php echo $this->BcForm->input('ListTool.batch_targets.' . $data['Content']['id'], ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">チェックする</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['Content']['id']]) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td" style="width:5%"><?php echo $data['Content']['id'] ?></td>
	<td class="bca-table-listup__tbody-td" style="width:5%">
		<?php if ($isImageIcon): ?>
			<?php $this->BcBaser->img($iconPath, ['title' => $typeTitle]) ?>
		<?php else: ?>
			<i class="<?php echo $iconPath ?>"></i>
		<?php endif ?>
		<?php if ($data['Content']['alias_id']): ?>
			<span class="alias"></span>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td" style="word-break: break-all;">
		<?php if ($isPublish): ?>
			<?php $this->BcBaser->link(urldecode($fullUrl), $fullUrl, ['target' => '_blank']) ?><br>
		<?php else: ?>
			<?php echo urldecode($fullUrl); ?><br>
		<?php endif; ?>
		<?php echo h($data['Content']['title']) ?>
	</td>
	<td class="bca-table-listup__tbody-td" style="width:8%;text-align:center">
		<?php echo $this->BcText->booleanMark($data['Content']['status']); ?>
	</td>
	<td class="bca-table-listup__tbody-td" style="width:8%;text-align:center">
		<?php echo h($this->BcText->arrayValue($data['Content']['author_id'], $authors)); ?>
	</td>

	<?php echo $this->BcListTable->dispatchShowRow($data) ?>

	<td class="bca-table-listup__tbody-td" style="width:8%;white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['Content']['created_date']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['Content']['modified_date']) ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php if ($isPublish): ?>
			<?php $this->BcBaser->link('', $fullUrl, ['title' => __d('baser', '確認'), 'class' => 'btn-check bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg', 'target' => '_blank']) ?>
		<?php else: ?>
			<a title="管理" class="btn bca-btn-icon" data-bca-btn-type="preview" data-bca-btn-size="lg"
			   data-bca-btn-status="gray"></a>
		<?php endif ?>
		<?php if (!$manageDisabled && !empty($this->BcContents->settings[$type]['routes']['manage'])): ?>
			<?php $this->BcBaser->link('', array_merge($this->BcContents->settings[$type]['routes']['manage'], $urlParams), ['title' => __d('baser', '管理'), 'class' => 'btn-check bca-btn-icon', 'data-bca-btn-type' => 'th-list', 'data-bca-btn-size' => 'lg']) ?>
		<?php else: ?>
			<a title="管理" class="btn bca-btn-icon" data-bca-btn-type="th-list" data-bca-btn-size="lg"
			   data-bca-btn-status="gray"></a>
		<?php endif ?>
		<?php if (!$isSiteRoot && !$isSiteRelated && !$editDisabled): ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_change_status'], ['title' => __d('baser', '非公開'), 'class' => 'btn-unpublish bca-btn-icon', 'data-bca-btn-type' => 'unpublish', 'data-bca-btn-size' => 'lg']) ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_change_status'], ['title' => __d('baser', '公開'), 'class' => 'btn-publish bca-btn-icon', 'data-bca-btn-type' => 'publish', 'data-bca-btn-size' => 'lg']) ?>
		<?php else: ?>
			<a title="非公開" class="btn bca-btn-icon" data-bca-btn-type="unpublish" data-bca-btn-size="lg"
			   data-bca-btn-status="gray"></a>
		<?php endif ?>
		<?php if (!$editDisabled && $type != 'ContentFolder' && !empty($this->BcContents->settings[$type]['routes']['copy'])): ?>
			<?php $this->BcBaser->link('', array_merge($this->BcContents->settings[$type]['routes']['copy'], $urlParams), ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy bca-btn-icon', 'data-bca-btn-type' => 'copy', 'data-bca-btn-size' => 'lg']) ?>
		<?php else: ?>
			<a title="コピー" class="bca-btn-icon" data-bca-btn-type="copy" data-bca-btn-size="lg"
			   data-bca-btn-status="gray"></a>
		<?php endif ?>
		<?php if (!$editDisabled): ?>
			<?php $this->BcBaser->link('', array_merge($this->BcContents->settings[$type]['routes']['edit'], $urlParams), ['title' => __d('baser', '編集'), 'class' => 'btn-edit bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
		<?php if (!$editDisabled && !$isSiteRoot): ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data['Content']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
		<form>
			<input type="hidden" name="data[contentId]" value="<?php echo $data['Content']['id'] ?>">
			<input type="hidden" name="data[type]" value="<?php echo $data['Content']['type'] ?>">
			<input type="hidden" name="data[entityId]" value="<?php echo $data['Content']['entity_id'] ?>">
			<input type="hidden" name="data[parentId]" value="<?php echo $data['Content']['parent_id'] ?>">
			<input type="hidden" name="data[title]" value="<?php echo h($data['Content']['title']) ?>">
			<input type="hidden" name="data[siteId]" value="<?php echo $data['Content']['site_id'] ?>">
			<input type="hidden" name="data[status]" value="<?php echo $toStatus ?>">
			<input type="hidden" name="data[alias]" value="<?php echo (bool)$data['Content']['alias_id'] ?>">
		</form>
	</td>
</tr>

