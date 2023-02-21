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
 * カスタムエントリー一覧　検索ボックス
 * @var \BcCustomContent\View\CustomContentFrontAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $customTable
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<div class="bs-search">

<?php echo $this->BcForm->create(null, ['novalidate' => true, 'type' => 'get']) ?>

<div class="bs-search__input-list">

  <span class="bs-search__input-item">
  <?php echo $this->BcForm->label('title', __d('baser', 'タイトル'), ['class' => 'bca-search__input-item-label']) ?>
  <?php echo $this->BcForm->control('title', [
    'type' => 'text',
  ]) ?>
  </span>

  <?php if ($customTable->custom_links): ?>
    <?php foreach($customTable->custom_links as $customLink): ?>
      <?php if ($this->CustomContent->isDisplayEntrySearch($customLink, 'front')): ?>
      <span class="bs-search__input-item">
  <?php echo $this->BcForm->label($customLink->name, $customLink->title, ['class' => 'bca-search__input-item-label']) ?>
  <?php echo $this->CustomContent->searchControl($customLink) ?>
      </span>
      <?php endif ?>
    <?php endforeach ?>
  <?php endif ?>

</div>

<div class="button bs-search__btns">
  <div class="bs-search__btns-item">
    <?php echo $this->BcForm->button(__d('baser', '検索'), ['id' => 'BtnSearchSubmit', 'class' => 'bs-button', 'data-bca-btn-type' => 'search']) ?>
  </div>
</div>

<?php echo $this->BcForm->end() ?>

</div>
