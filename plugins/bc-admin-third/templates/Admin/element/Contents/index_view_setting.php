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

use BaserCore\View\BcAdminAppView;

/**
 * @var BcAdminAppView $this
 * @var array $sites
 */

$listTypes = [1 => __d('baser', 'ツリー形式'), 2 => __d('baser', '表形式')];

$this->request = $this->request
  ->withData('ViewSetting.site_id', $this->BcAdminSite->getCurrentSite()->id)
  ->withData('ViewSetting.list_type', $this->request->getQuery('list_type'));

if ($this->request->getParam('action') == 'index') {
  echo $this->BcAdminForm->control('ViewSetting.mode', ['type' => 'hidden', 'value' => 'index']);
} elseif ($this->request->getParam('action') == 'trash_index') {
  echo $this->BcAdminForm->control('ViewSetting.mode', ['type' => 'hidden', 'value' => 'trash']);
}
?>

<?php if ($this->request->getParam('action') == 'index'): ?>
  <div class="panel-box bca-panel-box" id="ViewSetting">
    <div class="bca-panel-box__inline-fields">
      <?php if (count($sites) >= 2): ?>
        <div class="bca-panel-box__inline-fields-item">
          <label class="bca-panel-box__inline-fields-title"><?php echo __d('baser', 'サイト') ?></label>
          <?php echo $this->BcAdminForm->control('ViewSetting.site_id', ['type' => 'select', 'options' => $sites]) ?>
        </div>
        <div class="bca-panel-box__inline-fields-separator"></div>
      <?php else : ?>
        <?php echo $this->BcAdminForm->control('ViewSetting.site_id', ['type' => 'hidden', 'options' => $sites]) ?>
      <?php endif ?>
      <div class="bca-panel-box__inline-fields-item">
        <label class="bca-panel-box__inline-fields-title"><?php echo __d('baser', '表示') ?></label>
        <?php echo $this->BcAdminForm->control('ViewSetting.list_type', ['type' => 'radio', 'options' => $listTypes]) ?>
      </div>
      <div class="bca-panel-box__inline-fields-separator"></div>
      <div id="GrpChangeTreeOpenClose">
        <button id="BtnOpenTree" class="button-small"><?php echo __d('baser', '全て展開') ?></button>
        <button id="BtnCloseTree" class="button-small"><?php echo __d('baser', '全て閉じる') ?></button>
      </div>
    </div>
  </div>
<?php endif ?>
