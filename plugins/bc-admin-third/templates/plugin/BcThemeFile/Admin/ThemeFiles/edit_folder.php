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
unset($parentPrams[count($parentPrams) - 1]);
$this->BcAdmin->setHelp('theme_files_form_folder');
$this->BcAdmin->setTitle($pageTitle);
?>


<!-- current -->
<div class="em-box bca-current-box">
  <?php echo __d('baser_core', '現在の位置') ?>：<?php echo h($currentPath) ?>
</div>

<?php echo $this->BcAdminForm->create($themeFolderForm, [
  'id' => 'TemplateForm',
  'url' => array_merge(
    ['controller' => 'theme_files', 'action' => 'edit_folder', $theme, $plugin, $type],
    $params
  )]) ?>

<?php $this->BcBaser->element('ThemeFiles/form_folder') ?>

<div class="submit bca-actions">
  <div class="bca-actions__before">
    <?php $this->BcBaser->link(__d('baser_core', '一覧に戻る'),
      array_merge(['action' => 'index', $theme, $plugin, $type], explode('/', $path)), [
        'class' => 'button bca-btn',
        'data-bca-btn-type' => 'back-to-list'
      ]); ?>
  </div>
  <div class="bca-actions__main">
    <?php if ($isWritable): ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
        'div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave'
      ]) ?>
    <?php endif ?>
  </div>
  <?php if ($isWritable): ?>
    <div class="bca-actions__sub">
      <?= $this->BcAdminForm->postLink(__d('baser_core', '削除'),
        array_merge(['action' => 'delete_folder', $theme, $plugin, $type], $params),
        ['block' => true,
          'confirm' => __d('baser_core', '{0} を本当に削除してもいいですか？', $this->BcAdminForm->getSourceValue('name')),
          'class' => 'bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm',
          'data-bca-btn-color' => "danger"
        ]
      ) ?>
    </div>
  <?php endif ?>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
