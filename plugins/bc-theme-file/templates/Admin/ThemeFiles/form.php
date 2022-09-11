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

use BaserCore\View\BcAdminAppView;

/**
 * [ADMIN] テーマファイル登録・編集
 *
 * @var BcAdminAppView $this
 */

$this->BcBaser->js('BcThemeFile.admin/themes/form.bundle', false);
$params = explode('/', $path);
$parentPrams = explode('/', $path);
if ($this->request->action !== 'admin_add') {
  unset($parentPrams[count($parentPrams) - 1]);
}
?>
<!-- current -->
<div class="em-box bca-current-box">
  <?php echo __d('baser', '現在の位置') ?>：<?php echo h($currentPath) ?>
</div>

<?php if ($theme != 'core' && !$isWritable): ?>
  <div id="AlertMessage"><?php echo __d('baser', 'ファイルに書き込み権限がないので編集できません。') ?></div>
<?php endif ?>

<?php if ($this->request->action == 'admin_add'): ?>
  <?php echo $this->BcAdminForm->create('ThemeFile', ['id' => 'ThemeFileForm', 'url' => array_merge(['action' => 'add'], [$theme, $plugin, $type], explode('/', $path))]) ?>
<?php elseif ($this->request->action == 'admin_edit'): ?>
  <?php echo $this->BcAdminForm->create('ThemeFile', ['id' => 'ThemeFileForm', 'url' => array_merge(['action' => 'edit'], [$theme, $plugin, $type], explode('/', $path))]) ?>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcAdminForm->control('ThemeFile.parent', ['type' => 'hidden']) ?>

<!-- form -->
<div class="section">
  <table id="FormTable" class="form-table bca-form-table">
    <tr>
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcForm->label('ThemeFile.name', __d('baser', 'ファイル名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if ($this->request->action != 'admin_view'): ?>
          <?php echo $this->BcAdminForm->control('ThemeFile.name', ['type' => 'text', 'size' => 30, 'maxlength' => 255, 'autofocus' => true]) ?>
          <?php if ($this->BcForm->getSourceValue('ThemeFile.ext')): ?>.<?php endif ?>
          <?php echo h($this->BcForm->getSourceValue('ThemeFile.ext')) ?>
          <?php echo $this->BcAdminForm->control('ThemeFile.ext', ['type' => 'hidden']) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcForm->error('ThemeFile.name') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser', 'ファイル名は半角で入力してください。') ?></li>
            </ul>
          </div>
        <?php else: ?>
          <?php echo $this->BcAdminForm->control('ThemeFile.name', ['type' => 'text', 'size' => 30, 'readonly' => 'readonly']) ?> .<?php echo $this->BcForm->getSourceValue('ThemeFile.ext') ?>
          <?php echo $this->BcAdminForm->control('ThemeFile.ext', ['type' => 'hidden']) ?>
        <?php endif ?>
      </td>
    </tr>
    <?php if ($this->request->action == 'admin_add' || (($this->request->action == 'admin_edit' || $this->request->action == 'admin_view') && in_array($this->request->getData('ThemeFile.type'), ['text', 'image']))): ?>
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcForm->label('ThemeFile.contents', __d('baser', '内容')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php if (($this->request->action == 'admin_edit' || $this->request->action == 'admin_view') && $this->request->getData('ThemeFile.type') == 'image'): ?>
            <div style="margin:20px auto">
              <?php $this->BcBaser->link(
                $this->BcBaser->getImg(array_merge(['action' => 'img_thumb', 550, 550, $theme, $plugin, $type], explode('/', $path)), ['alt' => basename($path)]), array_merge(['action' => 'img', $theme, $plugin, $type], explode('/', $path)), ['rel' => 'colorbox', 'title' => basename($path)]
              ); ?>
            </div>
          <?php elseif ($this->request->action == 'admin_add' || $this->request->getData('ThemeFile.type') == 'text'): ?>
            <?php if ($this->request->action != 'admin_view'): ?>
              <?php echo $this->BcAdminForm->control('ThemeFile.contents', ['type' => 'textarea', 'cols' => 80, 'rows' => 30]) ?>
              <?php echo $this->BcForm->error('ThemeFile.contents') ?>
            <?php else: ?>
              <?php echo $this->BcAdminForm->control('ThemeFile.contents', ['type' => 'textarea', 'cols' => 80, 'rows' => 30, 'readonly' => 'readonly']) ?>
            <?php endif ?>
          <?php endif ?>
        </td>
      </tr>
    <?php endif ?>
    <?php echo $this->BcForm->dispatchAfterForm() ?>
  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">
  <?php if ($this->request->action == 'admin_add'): ?>
    <div class="bca-actions__main">
      <?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn', 'data-bca-btn-type' => 'save', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg', 'id' => 'BtnSave']) ?>
    </div>
  <?php elseif ($this->request->action == 'admin_edit'): ?>
    <?php if ($isWritable): ?>
      <div class="bca-actions__main">
        <?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn', 'data-bca-btn-type' => 'save', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg', 'id' => 'BtnSave']) ?>
      </div>
      <div class="bca-actions__sub">
        <?php $this->BcBaser->link(__d('baser', '削除'), array_merge(['action' => 'del', $theme, $plugin, $type], $params), ['class' => 'bca-submit-token button bca-btn', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'sm'], sprintf(__d('baser', '%s を本当に削除してもいいですか？'), basename($path)), false) ?>
      </div>
    <?php endif ?>
  <?php else: ?>
    <?php // プラグインのアセットの場合はコピーできない ?>
    <?php if (!$safeModeOn): ?>
      <?php //if($theme == 'core' && !(($type == 'css' || $type == 'js' || $type == 'img') && $plugin)): ?>
      <?php // テーマ編集が許可されていない場合コピー不可 ?>
      <?php if ($theme == 'core' && Configure::read('BcApp.allowedThemeEdit')): ?>
        <?php $this->BcBaser->link(__d('baser', '現在のテーマにコピー'), array_merge(['action' => 'copy_to_theme', $theme, $plugin, $type], explode('/', $path)), ['class' => 'bca-submit-token button bca-btn'], sprintf(__d('baser', '本当に現在のテーマ「%s」にコピーしてもいいですか？\n既に存在するファイルは上書きされます。'), Inflector::camelize($siteConfig['theme']))); ?>
      <?php endif; ?>
    <?php else: ?>
      <?php echo __d('baser', '機能制限のセーフモードで動作していますので、現在のテーマへのコピーはできません。') ?>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php if ($this->request->action == 'admin_add' || $this->request->action == 'admin_edit'): ?>
  <?php $this->BcBaser->link(__d('baser', '一覧に戻る'), array_merge(['action' => 'index', $theme, $plugin, $type], $parentPrams), ['class' => 'button bca-btn', 'data-bca-btn-type' => 'back-to-list']); ?>
<?php else: ?>
  <?php $this->BcBaser->link(__d('baser', '一覧に戻る'), array_merge(['action' => 'index', $theme, $plugin, $type], explode('/', dirname($path))), ['class' => 'button bca-btn', 'data-bca-btn-type' => 'back-to-list']); ?>
<?php endif; ?>


<?php echo $this->BcAdminForm->end() ?>
