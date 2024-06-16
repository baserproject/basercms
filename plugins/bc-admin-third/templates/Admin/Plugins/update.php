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
 * @var int $scriptNum
 * @var array $scriptMessages
 * @var string $dbVersion
 * @var string $programVersion
 * @var string $availableVersion
 * @var int $dbVerPoint
 * @var int $programVerPoint
 * @var string $log
 * @var bool $requireUpdate
 * @var string $php
 * @var bool $isWritablePackage
 * @var bool $coreDownloaded
 */
$this->BcAdmin->setTitle(sprintf(__d('baser_core', '%s｜アップデート'), ($plugin->name === 'BaserCore')? __d('baser_core', 'baserCMSコア') : $plugin->title . __d('baser_core', 'プラグイン')));
$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser_core', 'アップデートを実行します。よろしいですか？'),
  'updateMessage1' => __d('baser_core', '続いてアップデートを実行します。しばらくお待ちください。'),
]);
$this->BcBaser->js('admin/plugins/update.bundle', false, [
  'id' => 'AdminPluginsUpdateScript',
  'defer' => true,
  'data-plugin' => $plugin->name,
  'data-isWritablePackage' => $isWritablePackage
]);
if ($plugin->name !== 'BaserCore' || $coreDownloaded) {
  $formAction = 'update';
  $h2 = __d('baser_core', 'アップデート実行');
  $description = __d('baser_core', '「アップデート実行」をクリックしてプラグインのアップデートを完了させてください。');
} else {
  $formAction = 'get_core_update';
  $h2 = __d('baser_core', '最新版をダウンロード');
  $description = __d('baser_core', 'アップデートを実行する前に、最新版をダウンロードしてください。');
}
?>


<div class="bca-plugin-update">
  <div class="bca-panel-box">
    <h2 class="bca-main__heading" data-bca-heading-size="lg">
      <?php echo __d('baser_core', '現在のバージョン状況') ?>
    </h2>
    <ul class="version">
      <?php if ($availableVersion): ?>
        <li><?php echo __d('baser_core', '{0} の利用可能なバージョン： <strong>{1}</strong>', $plugin->title, $availableVersion) ?></li>
      <?php endif ?>
        <li><?php echo __d('baser_core', '{0} の現在のプログラムのバージョン： <strong>{1}</strong>', $plugin->title, $programVersion) ?></li>
        <li><?php echo __d('baser_core', '{0} の現在のデータベースのバージョン：<strong>{1}</strong>', $plugin->title, $dbVersion) ?></li>
    </ul>
    <?php if ($scriptNum || $scriptMessages): ?>
      <div class="em-box">
        <?php if ($programVerPoint === false || $dbVerPoint === false): ?>
          <h3><?php echo __d('baser_core', 'α版の場合はアップデートサポート外です。') ?></h3>
        <?php elseif ($programVersion !== $dbVersion || $scriptNum): ?>
          <?php if ($scriptNum): ?>
            <h3><?php echo __d('baser_core', 'アップデートプログラムが {0} つあります。', $scriptNum) ?></h3>
          <?php endif ?>
        <?php else: ?>
          <h3><?php echo __d('baser_core', 'データベースのバージョンは最新です。') ?></h3>
        <?php endif ?>
        <?php if ($scriptMessages): ?>
          <table class="bca-table-listup">
            <?php foreach($scriptMessages as $key => $scriptMessage): ?>
              <tr>
                <td class="bca-table-listup__tbody-td"><?php echo $key ?></td>
                <td class="bca-table-listup__tbody-td"><?php echo $scriptMessage ?></td>
              </tr>
            <?php endforeach ?>
          </table>
        <?php endif ?>
      </div>
    <?php endif ?>
  </div>

  <?php if ($scriptNum && $coreDownloaded): ?>
    <div class="bca-panel-box">
      <div class="section">
        <h2 class="bca-main__heading" data-bca-heading-size="lg">
          <?php echo __d('baser_core', 'データベースのバックアップは行いましたか？') ?>
        </h2>
        <p>
          <?php if ($plugin->name === 'BaserCore'): ?>
            <?php echo __d(
              'baser_core',
              'バックアップを行われていない場合は、アップデートを実行する前にプログラムファイルを前のバージョンに戻し、{0} よりデータベースのバックアップを行いましょう。',
              $this->BcBaser->getLink(__d('baser_core', 'データベースメンテナンス'), ['controller' => 'Utilities', 'action' => 'maintenance'])
            ) ?>
            <br>
          <?php else: ?>
            <?php echo __d('baser_core', 'バックアップを行われていない場合は、アップデートを実行する前にデータベースのバックアップを行いましょう。') ?>
            <br/>
          <?php endif ?>
          <small>※ <?php echo __d('baser_core', 'アップデート処理は必ず自己責任で行ってください。') ?></small><br/>
        </p>
      </div>
    </div>
    <div class="bca-panel-box">
      <div class="section">
        <h2 class="bca-main__heading" data-bca-heading-size="lg">
          <?php echo __d('baser_core', 'リリースノートのアップデート時の注意事項は読まれましたか？') ?>
        </h2>
        <p>
          <?php echo __d('baser_core', 'リリースバージョンによっては、追加作業が必要となる場合があるので注意が必要です。<br />
          公式サイトの <a href="https://basercms.net/news/archives/category/release" target="_blank" class="outside-link">リリースノート</a> を必ず確認してください。'
          ) ?>
        </p>
      </div>
    </div>
  <?php endif ?>

  <div class="bca-panel-box">
    <h2 class="bca-main__heading" data-bca-heading-size="lg">
      <?php echo $h2 ?>
    </h2>
    <?php if ($requireUpdate): ?>
      <p><?php echo $description ?></p>
      <?php if ($plugin->name === 'BaserCore'): ?>
        <p>
          <?php echo sprintf(
            __d('baser_core', 'baserCMSコアのアップデートがうまくいかない場合は、%sにご相談されるか、前のバージョンの baserCMS に戻す事をおすすめします。'),
            $this->BcBaser->getLink('baserCMSの制作・開発パートナー', 'https://basercms.net/partners/', ['target' => '_blank'])
          ) ?>
        </p>
      <?php endif ?>
      <?php echo $this->BcAdminForm->create($plugin, [
        'url' => ['action' => $formAction, $this->getRequest()->getParam('pass.0')],
        'id' => 'PluginUpdateForm'
      ]) ?>
      <?php echo $this->BcAdminForm->control('update', ['type' => 'hidden', 'value' => true]) ?>
      <?php echo $this->BcAdminForm->control('currentVersion', ['type' => 'hidden', 'value' => $programVersion]) ?>
      <?php echo $this->BcAdminForm->control('targetVersion', ['type' => 'hidden', 'value' => $availableVersion]) ?>
      <?php if ($availableVersion): ?>
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
          <?php if ($plugin->name !== 'BaserCore' || $coreDownloaded): ?>
            <?php echo $this->BcAdminForm->submit(__d('baser_core', 'アップデート実行'), [
              'class' => 'bca-btn bca-actions__item',
              'data-bca-btn-size' => 'lg',
              'data-bca-btn-width' => 'lg',
              'data-bca-btn-type' => 'save',
              'id' => 'BtnUpdate',
              'div' => false,
            ]) ?>
          <?php else: ?>
            <?php echo $this->BcAdminForm->submit(__d('baser_core', '最新版をダウンロード'), [
              'class' => 'bca-btn bca-actions__item',
              'data-bca-btn-size' => 'lg',
              'data-bca-btn-width' => 'lg',
              'data-bca-btn-type' => 'save',
              'id' => 'BtnDownload',
              'div' => false,
            ]) ?>
          <?php endif ?>
        </div>
      </div>
      <?php echo $this->BcAdminForm->end() ?>
    <?php else: ?>
      <div>
        <?php if($plugin->name === 'BaserCore' && $dbVersion !== $programVersion): ?>
          <p><?php echo __d('baser_core', 'データベースのバージョンの整合性がありません。<br>プログラムのバージョンに合わせて データベースの site_configs テーブルの version フィールドを更新してください。') ?></p>
        <?php else: ?>
          <p><?php echo __d('baser_core', '利用可能なアップデートはありません。') ?></p>
        <?php endif ?>
      </div>
      <div class="bca-actions">
        <div class="bca-actions__main">
          <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), [
            'plugin' => 'BaserCore',
            'controller' => 'Plugins',
            'action' => 'index',
          ], [
            'class' => 'bca-btn bca-actions__item',
            'data-bca-btn-type' => 'back-to-list'
          ]) ?>
        </div>
      </div>
    <?php endif ?>
  </div>

  <div class="bca-panel-box" id="UpdateLog">
    <h2 class="bca-main__heading" data-bca-heading-size="lg">
      <?php echo __d('baser_core', 'ログ') ?>
    </h2>
    <?php echo $this->BcAdminForm->control('log', [
      'type' => 'textarea',
      'value' => $log,
      'style' => 'width:99%;height:200px;font-size:12px',
      'readonly' => 'readonly'
    ]); ?>
  </div>

</div>
