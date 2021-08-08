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
$isSiteRoot = $data->site_root;
$isAlias = (boolean)$data->alias_id;
if (!empty($this->BcContents->settings[$data->type])) {
  $type = $data->type;
} else {
  $type = 'Default';
}
if ($isAlias) {
  $editDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'edit', $data->entity_id);
  $manageDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'manage', $data->entity_id);
} else {
  $editDisabled = !$this->BcContents->isActionAvailable($data->type, 'edit', $data->entity_id);
  $manageDisabled = !$this->BcContents->isActionAvailable($data->type, 'manage', $data->entity_id);
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
  if ($data->plugin != 'Core' && $type != 'Default') {
    $iconPath = $data->plugin . '.' . $iconPath;
  }
}
$urlParams = ['content_id' => $data->id];
if ($data->entity_id) {
  $urlParams = array_merge($urlParams, [$data->entity_id]);
}
$fullUrl = $this->BcContents->getUrl($data->url, true, $data['Site']['use_subdomain']);
$toStatus = 'publish';
if ($data->self_status) {
  $toStatus = 'unpublish';
}
?>


<tr id="Row<?= $count + 1 ?>"<?php $this->BcListTable->rowClass($isPublish, $data) ?>>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--select"><?php // 選択 ?>
    <?php if ($this->BcBaser->isAdminUser() && empty($data->site_root)): ?>
      <?= $this->BcAdminForm->control('ListTool.batch_targets.' . $data->id, ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">チェックする</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data->id, 'escape' => false]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:5%"><?= $data->id; ?></td>
  <td class="bca-table-listup__tbody-td" style="width:5%">
    <?php if ($isImageIcon): ?>
      <?php $this->BcBaser->img($iconPath, ['title' => $typeTitle]) ?>
    <?php else: ?>
      <i class="<?= $iconPath ?>"></i>
    <?php endif ?>
    <?php if ($data->alias_id): ?>
      <span class="alias"></span>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="word-break: break-all;">
    <?php if ($isPublish): ?>
      <?php $this->BcBaser->link(urldecode($fullUrl), $fullUrl, ['target' => '_blank']) ?><br>
    <?php else: ?>
      <?= urldecode($fullUrl); ?><br>
    <?php endif; ?>
    <?= h($data->title) ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:8%;text-align:center">
    <?= $this->BcText->booleanMark($data->status); ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:8%;text-align:center">
    <?= h($this->BcText->arrayValue($data->author_id, $authors)); ?>
  </td>

  <?= $this->BcListTable->dispatchShowRow($data) ?>

  <td class="bca-table-listup__tbody-td" style="width:8%;white-space: nowrap">
    <?= $this->BcTime->format($data->created_date, 'yyyy-MM-dd') ?><br/>
    <?= $this->BcTime->format($data->modified_date, 'yyyy-MM-dd') ?>
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
      <?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data->id], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
    <?php endif ?>
    <form>
      <input type="hidden" name="data[contentId]" value="<?= $data->id ?>">
      <input type="hidden" name="data[type]" value="<?= $data->type ?>">
      <input type="hidden" name="data[entityId]" value="<?= $data->entity_id ?>">
      <input type="hidden" name="data[parentId]" value="<?= $data->parent_id ?>">
      <input type="hidden" name="data[title]" value="<?= h($data->title) ?>">
      <input type="hidden" name="data[siteId]" value="<?= $data->site_id ?>">
      <input type="hidden" name="data[status]" value="<?= $toStatus ?>">
      <input type="hidden" name="data[alias]" value="<?= (bool)$data->alias_id ?>">
    </form>
  </td>
</tr>

