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
 * [ADMIN] テーマファイル登録・編集
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcThemeFile\Model\Entity\ThemeFile $themeFile
 * @var \BcThemeFile\Form\ThemeFileForm $themeFileForm
 * @var string $currentPath
 * @var bool $isWritable
 * @var string $path
 * @var string $theme
 * @var string $plugin
 * @var string $pageTitle
 * @var string $type
 * @var bool $isDefaultTheme
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcBaser->js('BcThemeFile.admin/theme_files/form.bundle', false);
$params = explode('/', $path);
$parentPrams = explode('/', $path);
unset($parentPrams[count($parentPrams) - 1]);
$this->BcAdmin->setTitle($pageTitle);
$this->BcAdmin->setHelp('theme_files_form');
?>


  <!-- current -->
  <div class="em-box bca-current-box">
    <?php echo __d('baser_core', '現在の位置') ?>：<?php echo h($currentPath) ?>
  </div>

<?php if (!$isDefaultTheme && !$isWritable): ?>
  <div id="AlertMessage"><?php echo __d('baser_core', 'ファイルに書き込み権限がないので編集できません。') ?></div>
<?php endif ?>

<?php echo $this->BcAdminForm->create($themeFileForm, [
  'id' => 'ThemeFileForm',
  'url' => array_merge(['action' => 'edit'], [$theme, $plugin, $type], explode('/', $path))
]) ?>
<?php echo $this->BcAdminForm->control('parent', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('ThemeFiles/form') ?>

<div class="submit bca-actions">
  <div class="bca-actions__before">
    <?php $this->BcBaser->link(__d('baser_core', '一覧に戻る'),
      array_merge(['action' => 'index', $theme, $plugin, $type], $parentPrams), [
        'class' => 'button bca-btn',
        'data-bca-btn-type' => 'back-to-list'
      ]); ?>
  </div>
  <div class="bca-actions__main">
    <?php if ($isWritable): ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
        'div' => false,
        'class' => 'button bca-btn',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave'
      ]) ?>
    <?php endif ?>
  </div>
  <?php if ($isWritable): ?>
  <div class="bca-actions__sub">
    <?php echo $this->BcAdminForm->postLink(__d('baser_core', '削除'),
      array_merge(['action' => 'delete', $theme, $plugin, $type], $params), [
        'block' => true,
        'confirm' => __d('baser_core', '{0} を本当に削除してもいいですか？', basename($path)),
        'class' => 'bca-submit-token button bca-btn',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'sm'
      ]) ?>
  </div>
  <?php endif ?>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?php echo $this->fetch('postLink') ?>
