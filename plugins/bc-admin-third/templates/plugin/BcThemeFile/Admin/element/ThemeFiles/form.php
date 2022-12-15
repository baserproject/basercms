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
 * @var string $currentPath
 * @var bool $isWritable
 * @var string $path
 * @var string $theme
 * @var string $plugin
 * @var string $pageTitle
 * @var string $type
 * @checked
 * @unitTest
 * @noTodo
 */
$action = $this->getRequest()->getParam('action');
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcAdminForm->control('fullpath', ['type' => 'hidden']) ?>
<?php echo $this->BcAdminForm->control('parent', ['type' => 'hidden']) ?>

<!-- form -->
<div class="section">
  <table id="FormTable" class="form-table bca-form-table">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('name', __d('baser', 'ファイル名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if ($action != 'view'): ?>
          <?php echo $this->BcAdminForm->control('base_name', ['type' => 'text', 'size' => 30, 'maxlength' => 255, 'autofocus' => true]) ?>
          <?php if ($themeFile->ext): ?>.<?php endif ?>
          <?php echo h($themeFile->ext) ?>
          <?php echo $this->BcAdminForm->control('ext', ['type' => 'hidden']) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('name') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser', 'ファイル名は半角で入力してください。') ?></li>
            </ul>
          </div>
        <?php else: ?>
          <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 30, 'readonly' => 'readonly']) ?> .<?php echo $themeFile->ext ?>
          <?php echo $this->BcAdminForm->control('ext', ['type' => 'hidden']) ?>
        <?php endif ?>
      </td>
    </tr>
    <?php if ($action == 'add' || (($action == 'edit' || $action == 'view') && in_array($themeFile->type, ['text', 'image']))): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('contents', __d('baser', '内容')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php if (($action == 'edit' || $action == 'view') && $themeFile->type == 'image'): ?>
            <div style="margin:20px auto">
              <?php $this->BcBaser->link(
                $this->BcBaser->getImg(array_merge(['action' => 'img_thumb', 550, 550, $theme, $plugin, $type], explode('/', $path)), ['alt' => basename($path)]), array_merge(['action' => 'img', $theme, $plugin, $type], explode('/', $path)), ['rel' => 'colorbox', 'title' => basename($path)]
              ); ?>
            </div>
          <?php elseif ($action == 'add' || $themeFile->type == 'text'): ?>
            <?php if ($action != 'view'): ?>
              <?php echo $this->BcAdminForm->control('contents', ['type' => 'textarea', 'cols' => 80, 'rows' => 30]) ?>
              <?php echo $this->BcAdminForm->error('contents') ?>
            <?php else: ?>
              <?php echo $this->BcAdminForm->control('contents', ['type' => 'textarea', 'cols' => 80, 'rows' => 30, 'readonly' => 'readonly']) ?>
            <?php endif ?>
          <?php endif ?>
        </td>
      </tr>
    <?php endif ?>
    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>
