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
 * [ADMIN] テーマフォルダ表示
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcThemeFile\Model\Entity\ThemeFile $themeFile
 * @var \BcThemeFile\Form\ThemeFolderForm $themeFolderForm
 * @var string $currentPath
 * @var bool $isWritable
 * @var string $path
 * @var string $theme
 * @var string $plugin
 * @var string $pageTitle
 * @var string $type
 * @var bool $isDefaultTheme
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js('BcThemeFile.admin/theme_files/form_folder.bundle', false);
$this->BcAdmin->setTitle($pageTitle);
$this->BcAdmin->setHelp('theme_files_form');
$params = explode('/', $path);
// コピー可能である条件
// - デフォルトテーマを表示している
// - 現在利用しているテーマがデフォルトテーマではない
// - 編集可能の設定となっている
$isCopyable = ($isDefaultTheme &&
  \Cake\Utility\Inflector::camelize(Cake\Core\Configure::read('BcApp.coreFrontTheme'), '-') !== \BaserCore\Utility\BcUtil::getCurrentTheme() &&
  Cake\Core\Configure::read('BcThemeFile.allowedThemeEdit')
);
?>


<!-- current -->
<div class="em-box bca-current-box">
  <?php echo __d('baser_core', '現在の位置') ?>：<?php echo h($currentPath) ?>
</div>

<?php echo $this->BcAdminForm->create($themeFolderForm, [
  'id' => 'TemplateForm'
]) ?>

<?php $this->BcBaser->element('ThemeFiles/form_folder') ?>

<div class="submit bca-actions">
  <div class="bca-actions__main">
  <?php $this->BcBaser->link(__d('baser_core', '一覧に戻る'),
    array_merge(['action' => 'index', $theme, $plugin, $type], explode('/', dirname($path))), [
      'class' => 'button bca-btn',
      'data-bca-btn-type' => 'back-to-list'
  ]); ?>
  <?php if ($isCopyable): ?>
    &nbsp;&nbsp;
    <?php echo $this->BcAdminForm->postLink(__d('baser_core', '現在のテーマにコピー'),
      array_merge(['action' => 'copy_folder_to_theme', $theme, $plugin, $type], $params), [
        'block' => true,
        'confirm' => __d('baser_core',
          "本当に現在のテーマ「{0}」にコピーしてもいいですか？\n既に存在するファイルは上書きされます。",
          Cake\Utility\Inflector::camelize(\BaserCore\Utility\BcUtil::getCurrentTheme())),
        'class' => 'bca-submit-token bca-btn',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
    ]) ?>
  <?php endif ?>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?php echo $this->fetch('postLink') ?>


