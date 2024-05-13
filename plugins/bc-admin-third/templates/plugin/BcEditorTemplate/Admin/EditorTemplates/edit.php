<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] エディタテンプレートー登録・編集
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcEditorTemplate\Model\Entity\EditorTemplate $editorTemplate
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js('BcEditorTemplate.admin/editor_templates/form.bundle', false);
$this->BcAdmin->setTitle(__d('baser_core', 'エディタテンプレート編集'));
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add'],
  'title' => __d('baser_core', '新規追加'),
]);
$this->BcAdmin->setHelp('editor_templates_form');
$this->BcBaser->css('admin/ckeditor/editor', true);
?>


<?php echo $this->BcAdminForm->create($editorTemplate, ['type' => 'file']) ?>
<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('EditorTemplates/form') ?>

<!-- button -->
<div class="bca-actions">
  <div class="bca-actions__before">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), [
      'plugin' => 'BcEditorTemplate',
      'controller' => 'EditorTemplates',
      'action' => 'index'
    ], [
      'class' => 'bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
  </div>
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
      'type' => 'submit',
      'id' => 'BtnSave',
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
    ]) ?>
  </div>
  <div class="bca-actions__sub">
    <?php echo $this->BcAdminForm->postLink(__d('baser_core', '削除'), ['action' => 'delete', $editorTemplate->id], [
      'block' => true,
      'confirm' => __d('baser_core', '{0} を本当に削除してもいいですか？', $editorTemplate->name),
      'class' => 'bca-btn bca-actions__item',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'sm',
      'data-bca-btn-color' => 'danger'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?php echo $this->fetch('postLink') ?>
