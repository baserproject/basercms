<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\Model\Entity\Content;
use BaserCore\View\BcAdminAppView;

/**
 * コンテンツ一覧 テーブル行
 *
 * @var BcAdminAppView $this
 * @var array $authorList
 * @var Content $content
 * @var int $count
 * @checked
 * @noTodo
 * @unitTest
 */
$isSiteRelated = $this->BcContents->isSiteRelated($content);
$isPublish = $this->BcContents->isAllowPublish($content, true);
$isSiteRoot = $content->site_root;
$isAlias = (boolean)$content->alias_id;
$items = $this->BcContents->getConfig('items');
if (!empty($items[$content->type])) {
  $type = $content->type;
} else {
  $type = 'Default';
}
if ($isAlias) {
  $editDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'edit', $content->entity_id);
  $manageDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'manage', $content->entity_id);
} else {
  $editDisabled = !$this->BcContents->isActionAvailable($content->type, 'edit', $content->entity_id);
  $manageDisabled = !$this->BcContents->isActionAvailable($content->type, 'manage', $content->entity_id);
}
$typeTitle = @$items[$type]['title'];
if (!empty($items[$type]['icon'])) {
  $iconPath = $items[$type]['icon'];
} else {
  $iconPath = @$items['Default']['icon'];
}
$isImageIcon = false;
if (preg_match('/^admin\//', $iconPath)) {
  $isImageIcon = true;
  if ($content->plugin != 'BaserCore' && $type != 'Default') {
    $iconPath = $content->plugin . '.' . $iconPath;
  }
}
$urlParams = ['content_id' => $content->id];
if ($content->entity_id) {
  $urlParams = array_merge($urlParams, [$content->entity_id]);
}
$fullUrl = $this->BcContents->getUrl($content->url, true, @$content['Site']['use_subdomain']);
$toStatus = 'publish';
if ($content->self_status) {
  $toStatus = 'unpublish';
}
?>


<tr id="Row<?= $count + 1 ?>"<?php echo $this->BcListTable->rowClass($isPublish, $content) ?>>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--select">
    <?php if ($this->BcBaser->isAdminUser() && empty($content->site_root)): ?>
      <?php echo $this->BcAdminForm->control(
        'batch_targets.' . $content->id, [
        'type' => 'checkbox',
        'label' => '<span class="bca-visually-hidden">' . __d('baser_core', 'チェックする') . '</span>',
        'class' => 'batch-targets bca-checkbox__input',
        'value' => $content->id,
        'escape' => false
      ]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:5%"><?= $content->id; ?></td>
  <td class="bca-table-listup__tbody-td" style="width:5%">
    <?php if ($isImageIcon): ?>
      <?php $this->BcBaser->img($iconPath, ['title' => $typeTitle]) ?>
    <?php else: ?>
      <i class="<?= $iconPath ?>"></i>
    <?php endif ?>
    <?php if ($content->alias_id): ?>
      <span class="alias"></span>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="word-break: break-all;">
    <?php if ($isPublish): ?>
      <?php $this->BcBaser->link(rawurldecode($fullUrl), $fullUrl, ['target' => '_blank']) ?><br>
    <?php else: ?>
      <?= rawurldecode($fullUrl); ?><br>
    <?php endif; ?>
    <?= h($content->title) ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:8%;text-align:center">
    <?= $this->BcText->booleanMark($content->status); ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:8%;text-align:center">
    <?= h($this->BcText->arrayValue($content->author_id, $authorList)); ?>
  </td>

  <?= $this->BcListTable->dispatchShowRow($content) ?>

  <td class="bca-table-listup__tbody-td" style="width:8%;white-space: nowrap">
    <?= $this->BcTime->format($content->created_date, 'yyyy-MM-dd') ?><br/>
    <?= $this->BcTime->format($content->modified_date, 'yyyy-MM-dd') ?>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">

    <!-- 確認 -->
    <?php if ($isPublish): ?>
      <?php $this->BcBaser->link('',
        $fullUrl, [
          'title' => __d('baser_core', '確認'),
          'class' => 'btn-check bca-btn-icon',
          'data-bca-btn-type' => 'preview',
          'data-bca-btn-size' => 'lg',
          'target' => '_blank'
        ]) ?>
    <?php else: ?>
      <a title="<?php echo __d('baser_core', '確認') ?>"
         class="btn bca-btn-icon"
         data-bca-btn-type="preview"
         data-bca-btn-size="lg"
         data-bca-btn-status="gray">
      </a>
    <?php endif ?>

    <!-- 管理 -->
    <?php if (!$manageDisabled && !empty($items[$type]['routes']['manage'])): ?>
      <?php $this->BcBaser->link('',
        array_merge($items[$type]['routes']['manage'], $urlParams), [
          'title' => __d('baser_core', '管理'),
          'class' => 'btn-check bca-btn-icon',
          'data-bca-btn-type' => 'th-list',
          'data-bca-btn-size' => 'lg'
        ]) ?>
    <?php else: ?>
      <a title="<?php echo __d('baser_core', '管理') ?>"
         class="btn bca-btn-icon"
         data-bca-btn-type="th-list"
         data-bca-btn-size="lg"
         data-bca-btn-status="gray">
      </a>
    <?php endif ?>

    <!-- 公開・非公開 -->
    <?php if (!$isSiteRoot && !$isSiteRelated && !$editDisabled): ?>
      <?php $this->BcBaser->link('',
        ['prefix' => 'Api', 'action' => 'change_status', '_ext' => 'json'], [
          'title' => __d('baser_core', '非公開'),
          'class' => 'btn-unpublish bca-btn-icon',
          'data-bca-btn-type' => 'unpublish',
          'data-bca-btn-size' => 'lg'
        ]) ?>
      <?php $this->BcBaser->link('',
        ['prefix' => 'Api', 'action' => 'change_status', '_ext' => 'json'], [
          'title' => __d('baser_core', '公開'),
          'class' => 'btn-publish bca-btn-icon',
          'data-bca-btn-type' => 'publish',
          'data-bca-btn-size' => 'lg'
        ]) ?>
    <?php else: ?>
      <a title="<?php echo __d('baser_core', '非公開') ?>"
         class="btn bca-btn-icon"
         data-bca-btn-type="unpublish"
         data-bca-btn-size="lg"
         data-bca-btn-status="gray">
      </a>
    <?php endif ?>

    <!-- コピー -->
    <?php if (!$editDisabled && $type != 'ContentFolder' && !empty($items[$type]['routes']['copy'])): ?>
      <?php $this->BcBaser->link('',
        array_merge($items[$type]['routes']['copy'], $urlParams, ['_ext' => 'json']), [
          'title' => __d('baser_core', 'コピー'),
          'class' => 'btn-copy bca-btn-icon',
          'data-bca-btn-type' => 'copy',
          'data-bca-btn-size' => 'lg'
        ]) ?>
    <?php else: ?>
      <a title="<?php echo __d('baser_core', 'コピー') ?>"
         class="bca-btn-icon"
         data-bca-btn-type="copy"
         data-bca-btn-size="lg"
         data-bca-btn-status="gray">
      </a>
    <?php endif ?>

    <!-- 編集 -->
    <?php if (!$editDisabled): ?>
      <?php $this->BcBaser->link('',
        array_merge($items[$type]['routes']['edit'], $urlParams), [
          'title' => __d('baser_core', '編集'),
          'class' => 'btn-edit bca-btn-icon',
          'data-bca-btn-type' => 'edit',
          'data-bca-btn-size' => 'lg'
        ]) ?>
    <?php endif ?>

    <!-- 削除 -->
    <?php if (!$editDisabled && !$isSiteRoot): ?>
      <?php $this->BcBaser->link('',
        ['prefix' => 'Api', 'action' => 'delete', $content->id, '_ext' => 'json'], [
          'title' => __d('baser_core', '削除'),
          'class' => 'btn-delete bca-btn-icon',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'lg',
          'data-confirm-message' => __d('baser_core', '{0} を本当に削除してもいいですか？', $content->title)
        ]) ?>
    <?php endif ?>

    <form>
      <input type="hidden" name="id" value="<?= $content->id ?>">
      <input type="hidden" name="type" value="<?= $content->type ?>">
      <input type="hidden" name="entity_id" value="<?= $content->entity_id ?>">
      <input type="hidden" name="parent_id" value="<?= $content->parent_id ?>">
      <input type="hidden" name="title" value="<?= h($content->title) ?>">
      <input type="hidden" name="site_id" value="<?= $content->site_id ?>">
      <input type="hidden" name="status" value="<?= $toStatus ?>">
      <input type="hidden" name="alias_id" value="<?= (bool)$content->alias_id ?>">
    </form>

  </td>
</tr>

