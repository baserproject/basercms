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
 * @var string $dbVersion
 * @var string $programVersion
 * @var string $availableVersion
 * @var string $php
 * @var bool $isUpdatable
 */
?>


<div class="bca-panel-box">
  <h2 class="bca-main__heading" data-bca-heading-size="lg">
    <?php echo __d('baser_core', 'アップデート実行') ?>
  </h2>

  <?php if ($isUpdatable): ?>
    <?php if ($dbVersion !== $programVersion): ?>
      <p><?php echo __d('baser_core', 'データベースのバージョンの整合性がありません。<br>アップデートの前にプログラムのバージョンに合わせて データベースの site_configs テーブルの version フィールドを更新してください。') ?></p>
    <?php else: ?>
      <p><?php echo __d('baser_core', '「アップデート実行」をクリックしてプラグインのアップデートを完了させてください。') ?></p>
      <p>
        <?php echo sprintf(
          __d('baser_core', 'baserCMSコアのアップデートがうまくいかない場合は、%sにご相談されるか、前のバージョンの baserCMS に戻す事をおすすめします。'),
          $this->BcBaser->getLink('baserCMSの制作・開発パートナー', 'https://basercms.net/partners/', ['target' => '_blank'])
        ) ?>
      </p>
    <?php endif ?>
  <?php else: ?>
    <p><?php echo __d('baser_core', '利用可能なアップデートはありません。') ?></p>
  <?php endif ?>

  <?php echo $this->BcAdminForm->create($plugin, [
    'id' => 'PluginUpdateForm'
  ]) ?>
  <?php echo $this->BcAdminForm->control('update', ['type' => 'hidden', 'value' => true]) ?>
  <?php echo $this->BcAdminForm->control('currentVersion', ['type' => 'hidden', 'value' => $programVersion]) ?>
  <?php echo $this->BcAdminForm->control('targetVersion', ['type' => 'hidden', 'value' => $availableVersion]) ?>

  <p>
    <?php echo __d('baser_core', 'PHP CLI の実行パス') ?>
    <?php echo $this->BcAdminForm->control('php', ['type' => 'text',
      'value' => $php,
      'size' => 40]) ?>
    <br>
    <small class="php-notice"><?php echo __d('baser_core', 'PHPのパスが取得できないためアップデートを実行できません。確認の上、手動で入力してください。') ?></small>
  </p>

  <div class="bca-actions">
    <div class="bca-actions__before">
      <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), ['plugin' => 'BaserCore',
        'controller' => 'Plugins',
        'action' => 'index',], ['class' => 'bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list']) ?>
    </div>
    <div class="bca-actions__main">
      <?php echo $this->BcAdminForm->submit(__d('baser_core', 'アップデート実行'), ['class' => 'bca-btn bca-actions__item',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'data-bca-btn-type' => 'save',
        'id' => 'BtnUpdate',
        'div' => false,]) ?>
    </div>
  </div>
  <?php echo $this->BcAdminForm->end() ?>
</div>
