<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマフォルダ登録・編集
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcThemeFile\Form\ThemeFolderForm $themeFolderForm
 * @var \BcThemeFile\Model\Entity\ThemeFolder $themeFolder
 * @var string $path
 * @var string $currentPath
 * @var string $theme
 * @var string $plugin
 * @var string $type
 * @var string $isWritable
 * @var string $pageTitle
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcBaser->js('BcThemeFile.admin/theme_files/form_folder.bundle', false);
$parentPrams = $params = explode('/', $path);
$this->BcAdmin->setHelp('theme_files_form_folder');
$this->BcAdmin->setTitle($pageTitle);
?>


<!-- current -->
<div class="em-box bca-current-box">
  <?php echo __d('baser', '現在の位置') ?>：<?php echo h($currentPath) ?>
</div>


<?php echo $this->BcAdminForm->create($themeFolderForm, [
  'id' => 'TemplateForm',
  'url' => array_merge(['controller' => 'theme_files', 'action' => 'add_folder', $theme, $plugin, $type], $params)
]) ?>

<?php $this->BcBaser->element('ThemeFiles/form_folder') ?>

<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php $this->BcBaser->link(__d('baser', '一覧に戻る'),
      array_merge(['action' => 'index', $theme, $plugin, $type], explode('/', $path)), [
        'class' => 'button bca-btn',
        'data-bca-btn-type' => 'back-to-list'
      ]); ?>
    &nbsp;&nbsp;
    <?php echo $this->BcAdminForm->button(__d('baser', '保存'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'id' => 'BtnSave'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
