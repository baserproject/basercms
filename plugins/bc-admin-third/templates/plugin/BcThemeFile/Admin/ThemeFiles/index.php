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
 * [ADMIN] テーマファイル一覧
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $themeFiles
 * @var string $currentPath
 * @var string $fullpath
 * @var string $path
 * @var string $theme
 * @var string $plugin
 * @var string $type
 * @var string $pageTitle
 * @var bool $isDefaultTheme
 * @checked
 * @unitTest
 * @noTodo
 */
$writable = true;
if ((is_dir($fullpath) && !is_writable($fullpath)) || $isDefaultTheme) {
  $writable = false;
}
$params = explode('/', $path);
$this->BcBaser->js('BcThemeFile.admin/theme_files/index.bundle', false);
$this->BcAdmin->setHelp('theme_files_index');
$this->BcAdmin->setTitle($pageTitle);
?>


<?php $this->BcBaser->element('ThemeFiles/submenus'); ?>

<!-- current -->
<div class="em-box bca-current-box"><?php echo __d('baser', '現在の位置') ?>：<?php echo h($currentPath) ?>
  <?php if (!$writable): ?>
    　<span style="color:#FF3300">[<?php echo __d('baser', '書込不可') ?>]</span>
  <?php endif ?>
</div>

<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('ThemeFiles/index_list') ?></div>

<div class="bca-actions" data-bca-type="type2">
  <?php if ($writable): ?>
    <div class="bca-actions__form">
      <?php echo $this->BcAdminForm->create(null, [
        'id' => 'ThemeFileUpload',
        'url' => array_merge(['action' => 'upload', $theme, $plugin, $type], $params),
        'enctype' => 'multipart/form-data']
      ) ?>
      <?php echo $this->BcAdminForm->control('file', ['type' => 'file']) ?>
      <?php echo $this->BcAdminForm->end() ?>
    </div>
  <?php endif ?>
  <div class="bca-actions__adds">
    <?php if ($writable): ?>
      <?php $this->BcBaser->link('<i class="bca-icon--folder"></i> ' . __d('baser', 'フォルダ新規作成'),
        array_merge(['action' => 'add_folder', $theme, $plugin, $type], $params), [
          'class' => 'bca-btn',
          'data-bca-btn-type' => 'add',
          'escape' => false
      ]) ?>
    <?php endif ?>
    <?php if (($path || $type != 'etc') && $type != 'img' && $writable): ?>
      <?php $this->BcBaser->link('<i class="bca-icon--file"></i> ' . __d('baser', 'ファイル新規作成'),
        array_merge(['action' => 'add', $theme, $plugin, $type], $params), [
          'class' => 'bca-btn',
          'data-bca-btn-type' => 'add',
          'escape' => false
      ]) ?>
    <?php endif ?>
  </div>
</div>
