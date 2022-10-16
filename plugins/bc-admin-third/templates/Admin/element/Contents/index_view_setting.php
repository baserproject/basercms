<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @unitTest
 * @noTodo
 */
echo $this->BcAdminForm->control('ViewSetting.mode', ['type' => 'hidden', 'value' => 'index']);
?>


<div class="panel-box bca-panel-box" id="ViewSetting">
  <div class="bca-panel-box__inline-fields">
    <div class="bca-panel-box__inline-fields-item">
      <label class="bca-panel-box__inline-fields-title"><?php echo __d('baser', '表示') ?></label>
      <?php echo $this->BcAdminForm->control('ViewSetting.list_type', ['type' => 'radio', 'options' => [
        1 => __d('baser', 'ツリー形式'),
        2 => __d('baser', '表形式')
      ]]) ?>
    </div>
    <div class="bca-panel-box__inline-fields-separator"></div>
    <div id="GrpChangeTreeOpenClose">
      <button id="BtnOpenTree" class="bca-btn"><?php echo __d('baser', '全て展開') ?></button>
      <button id="BtnCloseTree" class="bca-btn"><?php echo __d('baser', '全て閉じる') ?></button>
    </div>
  </div>
</div>
