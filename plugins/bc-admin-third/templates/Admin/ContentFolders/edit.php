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

/**
 * @var array $folderTemplateList フォルダテンプレートリスト
 * @var array $pageTemplateList ページテンプレートリスト
 */
use BaserCore\View\BcAdminAppView;

/**
 * ContentFolders Edit
 * @var BcAdminAppView $this
 */
$this->BcAdmin->setTitle(__d('baser', 'フォルダ編集'));
?>


<?php echo $this->BcAdminForm->create($contentFolder, ['novalidate' => true]) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcForm->hidden('ContentFolder.id') ?>
<table class="form-table bca-form-table" data-bca-table-type="type2">
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcForm->label('ContentFolder.folder_template', __d('baser', 'フォルダーテンプレート')) ?></th>
    <td class="bca-form-table__input">
      <?php echo $this->BcAdminForm->control('ContentFolder.folder_template', ['type' => 'select', 'options' => $folderTemplateList]) ?>
    </td>
  </tr>
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcForm->label('ContentFolder.page_template', __d('baser', '固定ページテンプレート')) ?></th>
    <td class="bca-form-table__input">
      <?php echo $this->BcAdminForm->control('ContentFolder.page_template', ['type' => 'select', 'options' => $pageTemplateList]) ?>
    </td>
  </tr>
  <?php echo $this->BcForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
    'class' => 'button bca-btn',
    'data-bca-btn-type' => 'save',
    'data-bca-btn-size' => 'lg',
    'data-bca-btn-width' => 'lg',
    'div' => false
  ]) ?>
<?php echo $this->BcAdminForm->end() ?>
