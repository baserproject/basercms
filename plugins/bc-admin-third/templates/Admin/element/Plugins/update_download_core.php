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

use BaserCore\View\BcAdminAppView;

/**
 * プラグインアップデート
 *
 * @var BcAdminAppView $this
 * @var \Cake\Datasource\EntityInterface $plugin
 * @var string $programVersion
 * @var string $availableVersion
 * @var bool $isUpdatable
 * @var string $php
 */
$this->BcAdmin->setTitle(__d('baser_core', 'baserCMSコア｜アップデート'));
?>


<div class="bca-panel-box">
  <h2 class="bca-main__heading" data-bca-heading-size="lg">
    <?php echo __d('baser_core', '最新版をダウンロード') ?>
  </h2>

  <?php if($availableVersion > $programVersion): ?>
  <p><?php echo __d('baser_core', 'アップデートを実行する前に、最新版をダウンロードしてください。') ?></p>
  <?php endif ?>

  <?php echo $this->BcAdminForm->create($plugin, [
    'url' => ['action' => 'get_core_update'],
    'id' => 'PluginUpdateForm'
  ]) ?>
  <?php echo $this->BcAdminForm->control('update', ['type' => 'hidden', 'value' => true]) ?>
  <?php echo $this->BcAdminForm->control('currentVersion', ['type' => 'hidden', 'value' => $programVersion]) ?>
  <?php echo $this->BcAdminForm->control('targetVersion', ['type' => 'hidden', 'value' => $availableVersion]) ?>

  <p>
    <?php echo __d('baser_core', 'PHP CLI の実行パス') ?>
    <?php echo $this->BcAdminForm->control('php', [
      'type' => 'text',
      'value' => $php,
      'size' => 40
    ]) ?>
    <br>
    <small class="php-notice"><?php echo __d('baser_core', 'PHPのパスが取得できないためアップデートを実行できません。確認の上、手動で入力してください。') ?></small>
  </p>

  <?php if(Cake\Core\Configure::read('debug')): ?>
  <p>
    <?php echo $this->BcAdminForm->control('force', [
      'type' => 'checkbox',
      'label' => __d('baser_core', '利用可能なバージョンに関わらず、composer.json の内容でダウンロードする')
    ]) ?>
  </p>
  <?php endif ?>

  <div class="bca-actions">
    <div class="bca-actions__before">
      <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), [
        'plugin' => 'BaserCore',
        'controller' => 'Plugins',
        'action' => 'index',
      ], [
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list'
      ]) ?>
    </div>
    <div class="bca-actions__main">
      <?php echo $this->BcAdminForm->submit(__d('baser_core', '最新版をダウンロード'), [
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'data-bca-btn-type' => 'save',
        'id' => 'BtnDownload',
        'div' => false,
      ]) ?>
    </div>
  </div>
  <?php echo $this->BcAdminForm->end() ?>
</div>
