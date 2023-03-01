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
 */
$this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜データベースアップデート'), ($plugin->name === 'BaserCore')? 'baserCMSコア' : $plugin->title . 'プラグイン'));
$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser', 'アップデートを実行します。よろしいですか？'),
]);
$this->BcBaser->js('admin/plugins/update.bundle', false);
?>


<div class="corner10 panel-box bca-panel-box section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg">
    <?php echo __d('baser', '現在のバージョン状況') ?>
  </h2>
  <ul class="version">
    <?php if($availableVersion): ?>
    <li><?php echo __d('baser', '{0} の利用可能なバージョン： <strong>{1}</strong>', $plugin->title, $availableVersion) ?></li>
    <li><?php echo __d('baser', '{0} の現在のバージョン： <strong>{1}</strong>', $plugin->title, $programVersion) ?></li>
    <?php else: ?>
    <li><?php echo __d('baser', '{0} の現在のプログラムバージョン： <strong>{1}</strong>', $plugin->title, $programVersion) ?></li>
    <li><?php echo __d('baser', '{0} の現在のデータベースのバージョン：<strong>{1}</strong>', $plugin->title, $dbVersion) ?></li>
    <?php endif ?>
  </ul>
  <?php if ($scriptNum || $scriptMessages): ?>
    <div class="em-box">
      <?php if ($programVerPoint === false || $dbVerPoint === false): ?>
        <h3><?php echo __d('baser', 'α版の場合はアップデートサポート外です。') ?></h3>
      <?php elseif ($programVersion !== $dbVersion || $scriptNum): ?>
        <?php if ($scriptNum): ?>
          <h3><?php echo sprintf(__d('baser', 'アップデートプログラムが <strong>%s つ</strong> あります。'), $scriptNum) ?></h3>
        <?php endif ?>
      <?php else: ?>
        <h3><?php echo __d('baser', 'データベースのバージョンは最新です。') ?></h3>
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

<?php if ($scriptNum): ?>
  <div class="bca-panel-box">
    <div class="section">
      <h2 class="bca-main__heading" data-bca-heading-size="lg">
        <?php echo __d('baser', 'データベースのバックアップは行いましたか？') ?>
      </h2>
      <p>
        <?php if ($plugin->name === 'BaserCore'): ?>
          <?php echo __d('baser', 'バックアップを行われていない場合は、アップデートを実行する前にプログラムファイルを前のバージョンに戻し、システム設定よりデータベースのバックアップを行いましょう。') ?>
          <br>
        <?php else: ?>
          <?php echo __d('baser', 'バックアップを行われていない場合は、アップデートを実行する前にデータベースのバックアップを行いましょう。') ?><br/>
        <?php endif ?>
        <small>※ <?php echo __d('baser', 'アップデート処理は必ず自己責任で行ってください。') ?></small><br/>
      </p>
    </div>
  </div>
  <div class="bca-panel-box">
    <div class="section">
      <h2 class="bca-main__heading" data-bca-heading-size="lg">
        <?php echo __d('baser', 'リリースノートのアップデート時の注意事項は読まれましたか？') ?>
      </h2>
      <p>
      <?php echo __d('baser', 'リリースバージョンによっては、追加作業が必要となる場合があるので注意が必要です。<br />
          公式サイトの <a href="https://basercms.net/news/archives/category/release" target="_blank" class="outside-link">リリースノート</a> を必ず確認してください。'
      ) ?>
      </p>
    </div>
  </div>
<?php endif ?>

<div class="corner10 panel-box bca-panel-box section">
  <?php if ($requireUpdate): ?>
    <h2 class="bca-main__heading" data-bca-heading-size="lg">
      <?php echo __d('baser', 'アップデート実行') ?>
    </h2>
    <p><?php echo __d('baser', '「アップデート実行」をクリックしてプラグインのアップデートを完了させてください。') ?></p>
    <?php echo $this->BcAdminForm->create($plugin) ?>
    <?php echo $this->BcAdminForm->control('update', ['type' => 'hidden', 'value' => true]) ?>
    <?php echo $this->BcAdminForm->control('currentVersion', ['type' => 'hidden', 'value' => $programVersion]) ?>
    <?php echo $this->BcAdminForm->control('targetVersion', ['type' => 'hidden', 'value' => $availableVersion]) ?>
    <p>
      <?php echo __d('baser', 'PHP CLI の実行パス') ?>
      <?php echo $this->BcAdminForm->control('php', [
        'type' => 'text',
        'value' => $php,
        'size' => 40
      ]) ?>
      <br>
      <small class="php-notice"><?php echo __d('baser', 'PHPのパスが取得できないためアップデートを実行できません。確認の上、手動で入力してください。') ?></small>
    </p>
    <div class="bca-actions">
      <?php echo $this->BcAdminForm->submit(__d('baser', 'アップデート実行'), [
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'data-bca-btn-type' => 'save',
        'id' => 'BtnUpdate',
        'div' => false,
      ]) ?>
    </div>
    <?php echo $this->BcAdminForm->end() ?>
  <?php else: ?>
    <p>
    <?php if ($plugin->name === 'BaserCore'): ?>
      <p>
        <?php echo sprintf(
          __d('baser', 'baserCMSコアのアップデートがうまくいかない場合は、%sにご相談されるか、前のバージョンの baserCMS に戻す事をおすすめします。'),
          $this->BcBaser->getLink('baserCMSの制作・開発パートナー', 'https://basercms.net/partners/', ['target' => '_blank'])
        ) ?>
      </p>
      <?php if (!$requireUpdate): ?>
        <?php $this->BcBaser->link(
          __d('baser', '管理画面に移動する'),
          ['plugin' => 'BaserCore', 'prefix' => 'Admin', 'controller' => 'Dashboard', 'action' => 'index'],
          ['class' => 'bca-btn']
        ) ?>
      <?php endif ?>
    <?php else: ?>
      <?php $this->BcBaser->link(
        __d('baser', 'プラグイン一覧に移動する'),
        ['controller' => 'plugins', 'action' => 'index'],
        ['class' => 'bca-btn']
      ) ?>
    <?php endif ?>
    </p>
  <?php endif ?>
</div>

<?php if ($log): ?>
  <div class="corner10 panel-box bca-panel-box section" id="UpdateLog">
    <h2 class="bca-main__heading" data-bca-heading-size="lg">
      <?php echo __d('baser', 'アップデートログ') ?>
    </h2>
    <?php echo $this->BcAdminForm->control('log', [
      'type' => 'textarea',
      'value' => $log,
      'style' => 'width:99%;height:200px;font-size:12px',
      'readonly' => 'readonly'
    ]); ?>
  </div>
<?php endif; ?>
