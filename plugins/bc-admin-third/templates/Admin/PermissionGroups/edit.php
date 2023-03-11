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
 * アクセスルールグループ編集
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var int $userGroupId
 * @var \BaserCore\Model\Entity\PermissionGroup $entity
 * @var string $userGroupTitle
 * @var array $permissionMethodList
 * @var array $permissionAuthList
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(sprintf(__d('baser_core', '%s｜アクセスルールグループ編集'), $userGroupTitle));
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => [
    'action' => 'add',
    $userGroupId,
    $entity->type
  ],
  'title' => __d('baser_core', '新規追加'),
]);
?>


<?= $this->BcAdminForm->create($entity, ['novalidate' => true]) ?>
<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>
<?php echo $this->BcAdminForm->control('name', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('PermissionGroups/form') ?>

<div class="submit section bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'),
      ['action' => 'index', $userGroupId], [
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list'
      ]) ?>
    <?= $this->BcAdminForm->button(
      __d('baser_core', '保存'),
      ['div' => false,
        'class' => 'button bca-btn bca-actions__item bca-loading',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave']
    ) ?>
  </div>
  <div class="bca-actions__sub">
      <?= $this->BcAdminForm->postLink(
        __d('baser_core', '削除'),
        ['action' => 'delete', $userGroupId, $entity->id],
        ['block' => true,
          'confirm' => __d('baser_core', '{0} を本当に削除してもいいですか？', $entity->name),
          'class' => 'bca-submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm'
        ]
      ) ?>
  </div>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
