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
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $customTable
 * @var \BcCustomContent\Model\Entity\CustomEntry $entity
 * @var int $tableId
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', '{0}｜エントリー編集', $customTable->title));
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $tableId],
  'title' => __d('baser', '新規追加'),
]);
$entryUrl = '';
if($customTable->isContentTable()) {
  $entryUrl = $this->CustomContentAdmin->getEntryUrl($entity);
}
$this->BcBaser->js('BcCustomContent.admin/custom_entries/form.bundle', false, [
  'defer' => true,
  'id' => 'AdminCustomEntriesFormScript',
  'data-fullUrl' => $entryUrl,
]);
?>


<?php echo $this->BcAdminForm->create($entity, ['type' => 'file', 'novalidate' => true]) ?>
<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>
<?php echo $this->BcAdminForm->control('custom_table_id', ['type' => 'hidden']) ?>

<?php if($customTable->isContentTable()): ?>
<div class="bca-section bca-section__post-top">
  <span class="bca-post__no">
    <?php echo $this->BcAdminForm->label('id', 'No') ?> : <strong><?php echo $entity->id ?></strong>
  </span>
  <span class="bca-post__url">
    <a href="<?php echo $entryUrl ?>"
       class="bca-text-url" target="_blank" data-toggle="tooltip" data-placement="top" title="公開URLを開きます">
      <i class="bca-icon--globe"></i>
      <?php echo $entryUrl ?>
    </a>
    <?php echo $this->BcAdminForm->button('', [
      'id' => 'BtnCopyUrl',
      'class' => 'bca-btn',
      'type' => 'button',
      'data-bca-btn-type' => 'textcopy',
      'data-bca-btn-category' => 'text',
      'data-bca-btn-size' => 'sm'
    ]) ?>
  </span>
</div>
<?php endif ?>

<?php $this->BcBaser->element('CustomEntries/form') ?>

<div class="bca-actions">
  <div class="bca-actions__main">
    <?php $this->BcBaser->link(__d('baser', '一覧に戻る'),
      ['action' => 'index', $tableId], [
        'class' => 'button bca-btn',
        'data-bca-btn-type' => 'back-to-list'
      ]) ?>&nbsp;&nbsp;
    <?php echo $this->BcAdminForm->submit(__d('baser', 'プレビュー'), [
        'id' => 'BtnPreview',
        'div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'preview',
      ]) ?>&nbsp;&nbsp;
    <?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
      'div' => false,
      'class' => 'bca-btn bca-loading',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'id' => 'BtnSave'
    ]) ?>
  </div>
  <div class="bca-actions__sub">
    <?php echo $this->BcAdminForm->postLink(__d('baser', '削除'),
    ['action' => 'delete', $tableId, $entity->id], [
      'block' => true,
      'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $entity->{$entity->custom_table->display_field}),
      'class' => 'bca-submit-token button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'sm',
      'data-bca-btn-color' => 'danger'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end(); ?>

<?php echo $this->fetch('postLink') ?>
