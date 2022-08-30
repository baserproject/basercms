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
 * @var array $folderTemplateList フォルダテンプレートリスト
 * @var array $pageTemplateList ページテンプレートリスト
 */

use BaserCore\Model\Entity\ContentFolder;
use BaserCore\View\BcAdminAppView;

/**
 * ContentFolders Edit
 * @var BcAdminAppView $this
 * @var ContentFolder $contentFolder
 * @var array $folderTemplateList
 * @var array $pageTemplateList
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcAdmin->setTitle(__d('baser', 'フォルダ編集'));
$this->BcBaser->js('admin/content_folders/edit.bundle', false);
?>


<?php echo $this->BcAdminForm->create($contentFolder, ['novalidate' => true]) ?>
<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcAdminForm->hidden('ContentFolders.id') ?>
<table class="form-table bca-form-table" data-bca-table-type="type2">
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcAdminForm->label('ContentFolders.folder_template', __d('baser', 'フォルダーテンプレート')) ?></th>
    <td class="bca-form-table__input">
      <?php echo $this->BcAdminForm->control('ContentFolders.folder_template', ['type' => 'select', 'options' => $folderTemplateList]) ?>
    </td>
  </tr>
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcAdminForm->label('ContentFolders.page_template', __d('baser', '固定ページテンプレート')) ?></th>
    <td class="bca-form-table__input">
      <?php echo $this->BcAdminForm->control('ContentFolders.page_template', ['type' => 'select', 'options' => $pageTemplateList]) ?>
    </td>
  </tr>
  <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
    'class' => 'button bca-btn bca-actions__item',
    'data-bca-btn-type' => 'save',
    'data-bca-btn-size' => 'lg',
    'data-bca-btn-width' => 'lg',
    'div' => false,
    'id' => 'BtnSave'
  ]) ?>
<?php echo $this->BcAdminForm->end() ?>
