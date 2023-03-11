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
 * @checked
 * @unitTest
 * @noTodo
 */
?>


<div class="panel-box bca-panel-box" id="ViewSetting">
  <div class="bca-panel-box__inline-fields">

    <div class="bca-panel-box__inline-fields-item">
      <label class="bca-panel-box__inline-fields-title"><?php echo __d('baser_core', 'ユーザーグループ') ?></label>
      <?php echo $this->BcAdminForm->control('filter_user_group_id', [
        'type' => 'select',
        'options' => $this->BcAdminForm->getControlSource('BaserCore.PermissionGroups.user_group_id')
      ]) ?>
    </div>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <div class="bca-panel-box__inline-fields-item">
      <label class="bca-panel-box__inline-fields-title"><?php echo __d('baser_core', 'タイプ') ?></label>
      <?php echo $this->BcAdminForm->control('list_type', [
        'type' => 'radio',
        'options' => \BaserCore\Utility\BcUtil::getAuthPrefixList()
      ]) ?>
    </div>

  </div>
</div>
