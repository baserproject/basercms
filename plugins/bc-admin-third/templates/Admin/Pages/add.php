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
 * [ADMIN] ページ登録フォーム
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Page $page
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->css('admin/ckeditor/editor', true);
$this->BcBaser->js('admin/pages/edit.bundle', false);
$this->BcAdmin->setTitle(__d('baser_core', '固定ページ情報新規登録'));
$this->BcAdmin->setHelp('pages_form');
?>


<?php echo $this->BcAdminForm->create($page, ['id' => 'PageAdminAddForm', 'novalidate' => true]) ?>
<?php echo $this->BcAdminForm->control('mode', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('BaserCore.Pages/form') ?>

<?php echo $this->BcAdminForm->submit(__d('baser_core', '保存'), [
  'div' => false,
  'class' => 'button bca-btn',
  'data-bca-btn-type' => 'save',
  'data-bca-btn-size' => 'lg',
  'data-bca-btn-width' => 'lg',
  'id' => 'BtnSave'
]) ?>

<?php echo $this->BcAdminForm->end(); ?>
