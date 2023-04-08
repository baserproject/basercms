<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.2.0
 * @license         https://basercms.net/license/index.html
 */
/**
 * @var bool $isAvailablePreview プレビュー機能が利用可能かどうか
 * @var bool $isAvailableDelete 削除機能が利用可能かどうか
 * @var string $currentAction 現在の画面のアクションボタン
 * @var bool $isAlias
 */
$deleteButtonText = __d('baser_core', 'ゴミ箱');
if ($isAlias) {
  $deleteButtonText = __d('baser_core', '削除');
}
?>


<div class="bca-actions">
  <div class="bca-actions__before">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), ['plugin' => 'BaserCore', 'controller' => 'contents', 'action' => 'index', '?' => ['site_id' => $currentSiteId]], [
      'class' => 'bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
  </div>
  <div class="bca-actions__main">
    <?php if ($isAvailablePreview): ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', 'プレビュー'), [
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-type' => 'preview',
        'id' => 'BtnPreview'
      ]) ?>
    <?php endif ?>
    <?php echo $currentAction ?>
  </div>
  <div class="bca-actions__sub">
    <?php if ($isAvailableDelete): ?>
      <?php echo $this->BcAdminForm->button($deleteButtonText, [
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'sm',
        'data-bca-btn-color' => 'danger',
        'class' => 'button bca-btn bca-actions__item',
        'id' => 'BtnDelete'
      ]) ?>
    <?php endif ?>
  </div>
</div>
