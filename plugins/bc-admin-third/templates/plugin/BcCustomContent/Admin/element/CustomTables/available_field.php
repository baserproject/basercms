<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * カスタムテーブル / 利用できるフィールド
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomField $field
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdminForm->unlockField('template');
?>


<div class="custom-field-content draggable available-field-<?php echo h($field->name) ?>">
  <div class="custom-field-content__head">
    <div class="custom-field-content__head-title">
      <span class="custom-field-content__head-text"><?php echo h($field->title) ?></span>
      <small>（<?php echo h($field->getTypeTitle()) ?>）</small>
    </div>
  </div>
</div>

<!-- template -->
<div class="custom-field-content sortable template template-field-<?php echo h($field->name) ?>">
  <div class="custom-field-content__head">
    <div class="custom-field-content__head-delete"><?php echo __d('baser', '削除') ?></div>
    <div class="custom-field-content__head-setting"><?php echo __d('baser', '設定') ?></div>
    <div class="custom-field-content__head-title">
      <span class="custom-field-content__head-text"><?php echo h($field->title) ?></span>
      <small>（<?php echo h($field->getTypeTitle()) ?>）</small>
    </div>
  </div>
  <div class="custom-field-setting__body" hidden>
    <?php echo $this->BcAdminForm->control('template.name', [
      'type' => 'hidden',
      'value' => $field->name]
    ) ?>
    <?php echo $this->BcAdminForm->control('template.custom_field_id', [
      'type' => 'hidden',
      'value' => $field->id
    ]) ?>
    <?php echo $this->BcAdminForm->control('template.sort', [
      'type' => 'hidden',
      'class' => 'bca-textbox__input custom-field-sort'
    ]) ?>
    <?php echo $this->BcAdminForm->control('template.display_front', [
      'type' => 'hidden',
      'value' => true
    ]) ?>
    <?php echo $this->BcAdminForm->control('template.status', [
      'type' => 'hidden',
      'value' => true
    ]) ?>
    <?php echo $this->BcAdminForm->control('template.title', [
      'type' => 'text',
      'size' => 40,
      'value' => $field->title,
      'class' => 'bca-textbox__input custom-field-setting__name',
    ]) ?>
  </div>
</div>
