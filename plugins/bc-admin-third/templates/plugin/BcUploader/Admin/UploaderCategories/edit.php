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
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcUploader\Model\Entity\UploaderCategory $uploaderCategory
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add'],
  'title' => __d('baser_core', '新規追加'),
]);
$this->BcAdmin->setTitle(__d('baser_core', 'アップロードカテゴリ編集'));
?>


<?php echo $this->BcAdminForm->create($uploaderCategory) ?>
<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('UploaderCategories/form') ?>

<div class="submit bca-actions">
  <div class="bca-actions__before">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), [
      'plugin' => 'BcUploader',
      'controller' => 'UploaderCategories',
      'action' => 'index'
    ], [
      'class' => 'bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
  </div>
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
      'div' => false,
      'class' => 'bca-loading bca-btn bca-actions__item',
      'data-bca-btn-type' =>
      'save', 'data-bca-btn-size' =>
      'lg', 'data-bca-btn-width' => 'lg'
    ]) ?>
  </div>
  <div class="bca-actions__sub">
    <?php echo $this->BcAdminForm->postLink(__d('baser_core', '削除'),
      ['action' => 'delete', $this->BcAdminForm->getSourceValue('id')], [
        'confirm' => sprintf(__d('baser_core', '%s を本当に削除してもいいですか？'), $this->BcAdminForm->getSourceValue('name')),
        'block' => true,
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'sm',
        'data-bca-btn-color' => "danger"
      ]
    ) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
