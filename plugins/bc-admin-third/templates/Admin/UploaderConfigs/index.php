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
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcUploader\Model\Entity\UploaderConfig $uploaderConfig
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', 'アップローダープラグイン設定'));
?>


<!-- form -->
<?php echo $this->BcAdminForm->create($uploaderConfig, ['url' => ['action' => 'index']]) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<h2><?php echo __d('baser', '画像サイズ設定') ?></h2>

<div class="section bca-section">
  <table class="list-table bca-form-table" id="ListTable">
    <tr>
      <th class="bca-form-table__label">
        <span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>&nbsp;
        <?php echo $this->BcAdminForm->label('large_width', __d('baser', 'PCサイズ（大）')) ?>
      </th>
      <td class="bca-form-table__input">
        <small>[<?php echo __d('baser', '幅') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('large_width', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8,
          'autofocus' => true
        ]) ?>
        &nbsp;px　×　
        <small>[<?php echo __d('baser', '高さ') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('large_height', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8
        ]) ?>
        &nbsp;px
        <?php echo $this->BcAdminForm->error('large_width') ?>
        <?php echo $this->BcAdminForm->error('large_height') ?>
      </td>
    </tr>
    <tr>
      <th class="bca-form-table__label"><span class="bca-label" data-bca-label-type="required">
      <?php echo __d('baser', '必須') ?></span>&nbsp;
        <?php echo $this->BcAdminForm->label('midium_width', __d('baser', 'PCサイズ（中）')) ?>
      </th>
      <td class="bca-form-table__input">
        <small>[<?php echo __d('baser', '幅') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('midium_width', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8
        ]) ?>
        &nbsp;px　×　
        <small>[<?php echo __d('baser', '高さ') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('midium_height', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8
        ]) ?>
        &nbsp;px
        <?php echo $this->BcAdminForm->error('midium_width') ?>
        <?php echo $this->BcAdminForm->error('midium_height') ?>
      </td>
    </tr>
    <tr>
      <th class="bca-form-table__label"><span class="bca-label" data-bca-label-type="required">
        <?php echo __d('baser', '必須') ?></span>&nbsp;
        <?php echo $this->BcAdminForm->label('small_width', __d('baser', 'PCサイズ（小）')) ?>
      </th>
      <td class="bca-form-table__input">
        <small>[<?php echo __d('baser', '幅') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('small_width', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8
        ]) ?>
        &nbsp;px　×　
        <small>[<?php echo __d('baser', '高さ') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('small_height', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8
        ]) ?>
        &nbsp;px　
        <?php echo $this->BcAdminForm->control('small_thumb', [
          'type' => 'checkbox',
          'label' => __d('baser', '正方形に切り抜く'),
          'between' => '&nbsp;'
        ]) ?>
        <?php echo $this->BcAdminForm->error('small_width') ?>
        <?php echo $this->BcAdminForm->error('small_height') ?>
        <?php echo $this->BcAdminForm->error('small_thumb') ?>
      </td>
    </tr>
    <tr>
      <th class="bca-form-table__label"><span class="bca-label" data-bca-label-type="required">
        <?php echo __d('baser', '必須') ?></span>&nbsp;
        <?php echo $this->BcAdminForm->label('mobile_large_width', __d('baser', '携帯サイズ（大）')) ?>
      </th>
      <td class="bca-form-table__input">
        <small>[<?php echo __d('baser', '幅') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('mobile_large_width', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8
        ]) ?>
        &nbsp;px　×　
        <small>[<?php echo __d('baser', '高さ') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('mobile_large_height', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8
        ]) ?>
        &nbsp;px
        <?php echo $this->BcAdminForm->error('mobile_large_width') ?>
        <?php echo $this->BcAdminForm->error('mobile_large_height') ?>
      </td>
    </tr>
    <tr>
      <th class="bca-form-table__label"><span class="bca-label" data-bca-label-type="required">
        <?php echo __d('baser', '必須') ?></span>&nbsp;
        <?php echo $this->BcAdminForm->label('mobile_small_width', __d('baser', '携帯サイズ（小）')) ?>
      </th>
      <td class="bca-form-table__input">
        <small>[<?php echo __d('baser', '幅') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('mobile_small_width', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8
        ]) ?>
        &nbsp;px　×　
        <small>[<?php echo __d('baser', '高さ') ?>]</small>&nbsp;
        <?php echo $this->BcAdminForm->control('mobile_small_height', [
          'type' => 'text',
          'size' => 8,
          'maxlength' => 8
        ]) ?>
        &nbsp;px　
        <?php echo $this->BcAdminForm->control('mobile_small_thumb', [
          'type' => 'checkbox',
          'label' => __d('baser', '正方形に切り抜く'),
          'between' => '&nbsp;'
        ]) ?>
        <?php echo $this->BcAdminForm->error('mobile_small_width') ?>
        <?php echo $this->BcAdminForm->error('mobile_small_height') ?>
        <?php echo $this->BcAdminForm->error('mobile_small_thumb') ?>
      </td>
    </tr>

    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>

  </table>
</div>

<?php if (\BaserCore\Utility\BcUtil::isAdminUser()): ?>

  <div id="FormOptionBody" class="section">
    <h3><?php echo __d('baser', '詳細設定') ?></h3>
    <table class="form-table bca-form-table">
      <tr>
        <th class="col-head bca-form-table__label">
            <?php echo $this->BcAdminForm->label('layout_type', __d('baser', 'レイアウトタイプ')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('layout_type', [
            'type' => 'radio',
            'options' => ['panel' => __d('baser', 'パネル'), 'table' => __d('baser', 'テーブル')]
          ]) ?>
          <?php echo $this->BcAdminForm->error('layout_type') ?>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('use_permission', __d('baser', '制限設定')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('use_permission', [
            'type' => 'checkbox',
            'label' => __d('baser', '編集/削除を制限する'),
            'between' => '&nbsp;'
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('use_permission') ?>
          <div class="bca-helptext">
            <?php echo __d('baser', '管理者以外のユーザーは、自分がアップロードしたファイル以外、編集・削除をできないようにします。') ?>
          </div>
        </td>
      </tr>
      <?php echo $this->BcAdminForm->dispatchAfterForm('option') ?>
    </table>
  </div>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->submit(__d('baser', '更新'), [
      'div' => false,
      'class' => 'bca-btn bca-actions__item bca-loading',
      'data-bca-btn-type' => 'add',
      'data-bca-btn-width' => 'lg',
      'data-bca-btn-size' => 'lg',
      'id' => 'btnSubmit'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
