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
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $customTable
 * @checked
 * @noTodo
 * @unitTest
 */
$creators = $this->BcAdminForm->getControlSource('BcCustomContent.CustomEntries.creator_id');

if($customTable->isContentTable()) {
  $titleLabel = __d('baser', 'タイトル・スラッグ');
} else {
  $titleLabel = __d('baser', 'タイトル');
}
?>


<?php echo $this->BcAdminForm->create(null, ['novalidate' => true, 'type' => 'get', 'url' => ['action' => 'index', $customTable->id]]) ?>

<p class="bca-search__input-list">

  <span class="bca-search__input-item">
  <?php echo $this->BcAdminForm->label('title', $titleLabel, ['class' => 'bca-search__input-item-label']) ?>
  <?php echo $this->BcAdminForm->control('title', [
    'type' => 'text',
  ]) ?>
  </span>

<?php if($customTable->isContentTable()): ?>
  <span class="bca-search__input-item">
  <?php echo $this->BcAdminForm->label('status', __d('baser', '公開状態'), ['class' => 'bca-search__input-item-label']) ?>
  <?php echo $this->BcAdminForm->control('status', [
    'type' => 'select',
    'options' => ['publish' => __d('baser', '○'), 'unpublish' => __d('baser', 'ー')],
    'empty' => __d('baser', '指定なし')
  ]) ?>
  </span>
<?php endif ?>

  <span class="bca-search__input-item">
  <?php echo $this->BcAdminForm->label('creator_id', __d('baser', '作成者'), ['class' => 'bca-search__input-item-label']) ?>
  <?php echo $this->BcAdminForm->control('creator_id', [
    'type' => 'select',
    'options' => $creators,
    'empty' => __d('baser', '指定なし')
  ]) ?>
  </span>

  <?php if ($customTable->custom_links): ?>
    <?php foreach($customTable->custom_links as $customLink): ?>
      <?php if ($this->CustomContentAdmin->isDisplayEntrySearch($customLink, 'admin')): ?>
        <span class="bca-search__input-item">
  <?php echo $this->BcAdminForm->label($customLink->name, $customLink->title, ['class' => 'bca-search__input-item-label']) ?>
  <?php echo $this->CustomContentAdmin->searchControl($customLink) ?>
      </span>
      <?php endif ?>
    <?php endforeach ?>
  <?php endif ?>

  <?php echo $this->BcSearchBox->dispatchShowField() ?>

</p>

<div class="button bca-search__btns">
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser', '検索'), ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn bca-loading', 'data-bca-btn-type' => 'search']) ?>
  </div>
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser', 'クリア'), ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
