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
 * カスタムテーブル / 利用中のフィールド
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomLink $customLink
 * @var bool $child
 * @var int $i
 * @checked
 * @noTodo
 * @unitTest
 */
if(!empty($child)) {
  $sortClass = 'bca-hidden__input custom-field-child-sort';
} else {
  $sortClass = 'bca-hidden__input custom-field-sort';
}
?>


<div class="custom-field-content sortable" id="InUseField<?php echo $i ?>">
  <div class="custom-field-content__head">
    <div class="custom-field-content__head-delete"><?php echo __d('baser', '削除') ?></div>
    <div class="custom-field-content__head-setting"><?php echo __d('baser', '設定') ?></div>
    <div class="custom-field-content__head-title">
      <span class="custom-field-content__head-text"><?php echo "{{ linkTitleById({$customLink->id}) }}" ?></span>
      <small>（<?php echo "{{ linkFieldTitleById({$customLink->id}) }}" ?>）</small>
    </div>
  </div>
  <div class="custom-field-setting__body" hidden>
    <?php echo $this->BcAdminForm->control("custom_links.{$customLink->id}.id", [
      'type' => 'hidden',
      'value' => $customLink->id
    ]) ?>
    <?php echo $this->BcAdminForm->control("custom_links.{$customLink->id}.name", [
      'type' => 'hidden',
      'value' => $customLink->name
    ]) ?>
    <?php echo $this->BcAdminForm->control("custom_links.{$customLink->id}.custom_field_id", [
      'type' => 'hidden',
      'value' => $customLink->custom_field_id
    ]) ?>
    <?php echo $this->BcAdminForm->control("custom_links.{$customLink->id}.sort", [
      'type' => 'hidden',
      'value' => $i,
      'class' => $sortClass
    ]) ?>
    <?php echo $this->BcAdminForm->control("custom_links.{$customLink->id}.title", [
      'type' => 'text',
      'size' => 40,
      'value' => $customLink->title,
      'class' => 'bca-textbox__input custom-field-setting__name',
      'v-model' => "linkTitleById({$customLink->id})"
    ]) ?>
    <?php $this->BcBaser->link(__d('baser', '詳細編集'), '#', [
      'class' => 'button-small',
      '@click' => "openLinkDetail({$customLink->id})"
    ]) ?>
  </div>
</div>
