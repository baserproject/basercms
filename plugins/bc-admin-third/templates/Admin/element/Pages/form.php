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
 * [ADMIN] ページ登録・編集フォーム
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Page $page
 * @var array $pageTemplateList
 * @var string $editor
 * @var array $editorOptions
 * @var string $editorEnterBr
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>
<div class="bca-section bca-section-editor-area">
  <?php echo $this->BcAdminForm->editor('contents', array_merge([
    'editor' => $editor,
    'editorUseDraft' => true,
    'editorDraftField' => 'draft',
    'editorWidth' => 'auto',
    'editorHeight' => '480px',
    'editorEnterBr' => $editorEnterBr
  ], $editorOptions));
  ?>
  <?php echo $this->BcAdminForm->error('contents') ?>
  <?php echo $this->BcAdminForm->error('draft') ?>
</div>

<?php if (\BaserCore\Utility\BcUtil::isAdminUser()): ?>
  <section class="bca-section" data-bca-section-type="form-group">
    <div class="bca-collapse__action">
      <button type="button"
        class="bca-collapse__btn"
        data-bca-collapse="collapse"
        data-bca-target="#pageSettingBody"
        aria-expanded="false"
        aria-controls="pageSettingBody">
        詳細設定&nbsp;&nbsp;<i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
      </button>
    </div>
    <div class="bca-collapse" id="pageSettingBody" data-bca-state="">
      <table class="form-table bca-form-table" data-bca-table-type="type2">
        <tr>
          <th class="bca-form-table__label">
            <?php echo $this->BcAdminForm->label('page_template', __d('baser', '固定ページテンプレート')) ?>
          </th>
          <td class="col-input bca-form-table__input">
            <?php echo $this->BcAdminForm->control('page_template', ['type' => 'select', 'options' => $pageTemplateList]) ?>
            <i class="bca-icon--question-circle bca-help"></i>
            <div class="bca-helptext">
              <?php echo __d('baser', 'テーマフォルダ内の、templates/Page/ にテンプレートを配置する事で、ここでテンプレートを選択できます。') ?>
            </div>
            <?php echo $this->BcAdminForm->error('page_template') ?>
          </td>
        </tr>
        <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
      </table>
    </div>
  </section>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>
