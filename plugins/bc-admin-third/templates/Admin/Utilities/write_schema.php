<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] スキーマ生成 フォーム
 */
?>


<?php echo $this->BcAdminForm->create('Tool', ['url' => ['action' => 'write_schema']]) ?>

<table class="form-table bca-form-table">
  <tr>
    <th class="col-head bca-form-table__label"><span class="bca-label"
                                                     data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>&nbsp;<?php echo $this->BcAdminForm->label('Tool.baser', __d('baser_core', 'コアテーブル名')) ?>
    </th>
    <td class="col-input bca-form-table__input">
      <?php echo $this->BcAdminForm->control('Tool.core', [
        'type' => 'select',
        'options' => $this->BcAdminForm->getControlSource('Tool.core'),
        'multiple' => true,
        'style' => 'width:400px;height:250px']); ?>
      <?php echo $this->BcAdminForm->error('Tool.core') ?>
    </td>
  </tr>
  <tr>
    <th class="col-head bca-form-table__label"><span class="bca-label"
                                                     data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>&nbsp;<?php echo $this->BcAdminForm->label('Tool.plugin', __d('baser_core', 'プラグインテーブル名')) ?>
    </th>
    <td class="col-input bca-form-table__input">
      <?php echo $this->BcAdminForm->control('Tool.plugin', [
        'type' => 'select',
        'options' => $this->BcAdminForm->getControlSource('Tool.plugin'),
        'multiple' => true,
        'style' => 'width:400px;height:250px']); ?>
      <?php echo $this->BcAdminForm->error('Tool.plugin') ?>
    </td>
  </tr>
</table>
<p><?php echo __d('baser_core', 'テーブルを選択して「生成」ボタンを押してください。') ?></p>
<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->submit(__d('baser_core', '生成'), ['div' => false, 'class' => 'button bca-btn bca-actions__item', 'data-bca-btn-size' => 'lg']) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
