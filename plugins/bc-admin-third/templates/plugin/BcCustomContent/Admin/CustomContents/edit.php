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
 * カスタムコンテンツ編集
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomContent $entity
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', 'カスタムコンテンツ設定編集'));
?>


<?php echo $this->BcAdminForm->create($entity, ['novalidate' => true]) ?>
<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('CustomContents/form') ?>

<?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
  'div' => false,
  'class' => 'button bca-btn',
  'data-bca-btn-type' => 'save',
  'data-bca-btn-size' => 'lg',
  'data-bca-btn-width' => 'lg',
  'id' => 'BtnSave'
]) ?>

<?php echo $this->BcAdminForm->end(); ?>
