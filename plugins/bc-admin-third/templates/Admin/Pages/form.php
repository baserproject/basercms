<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
use BaserCore\Utility\BcUtil;
use BaserCore\View\BcAdminAppView;
/**
 * [ADMIN] ページ登録・編集フォーム
 * @var BcAdminAppView $this
 */
$this->BcBaser->css('admin/ckeditor/editor', ['inline' => true]);
$this->BcAdmin->setTitle(__d('baser', '固定ページ情報編集'));
$this->BcAdmin->setHelp('pages_form');
?>


<div hidden="hidden">
  <div id="Action"><?php echo $this->request->action ?></div>
</div>

<?php echo $this->BcAdminForm->create($contentEntities, ['novalidate' => true]) ?>
<?php echo $this->BcAdminForm->control('Page.mode', ['type' => 'hidden']) ?>
<?php echo $this->BcAdminForm->control('Page.id', ['type' => 'hidden']) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>
<div class="bca-section bca-section-editor-area">
  <?php echo $this->BcAdminForm->editor('Page.contents', array_merge([
    'editor' => $editor,
    'editorUseDraft' => true,
    'editorDraftField' => 'draft',
    'editorWidth' => 'auto',
    'editorHeight' => '480px',
    'editorEnterBr' => $editor_enter_br
  ], $editorOptions));
  ?>
  <?php echo $this->BcAdminForm->error('Page.contents') ?>
  <?php echo $this->BcAdminForm->error('Page.draft') ?>
</div>

<?php if (BcUtil::isAdminUser()): ?>
  <section class="bca-section" data-bca-section-type="form-group">
    <div class="bca-collapse__action">
      <button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
              data-bca-target="#pageSettingBody" aria-expanded="false" aria-controls="pageSettingBody">
        詳細設定&nbsp;&nbsp;<i
          class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
    </div>
    <div class="bca-collapse" id="pageSettingBody" data-bca-state="">
      <table class="form-table bca-form-table" data-bca-table-type="type2">
        <tr>
          <th
            class="bca-form-table__label"><?php echo $this->BcAdminForm->label('Page.page_template', __d('baser', '固定ページテンプレート')) ?></th>
          <td class="col-input bca-form-table__input">
            <?php echo $this->BcAdminForm->control('Page.page_template', ['type' => 'select', 'options' => $pageTemplateList]) ?>
            <div
              class="helptext"><?php echo __d('baser', 'テーマフォルダ内の、templates/Pages テンプレートを配置する事で、ここでテンプレートを選択できます。') ?></div>
            <?php echo $this->BcAdminForm->error('Page.page_template') ?>
          </td>
        </tr>
        <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
      </table>
    </div>
  </section>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
  'div' => false,
  'class' => 'button bca-btn',
  'data-bca-btn-type' => 'save',
  'data-bca-btn-size' => 'lg',
  'data-bca-btn-width' => 'lg',
  'id' => 'BtnSave'
]) ?>

<?php echo $this->BcAdminForm->end(); ?>
