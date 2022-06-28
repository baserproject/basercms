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

use BaserCore\Utility\BcUtil;
use BaserCore\View\BcAdminAppView;

/**
 * プラグインアップデート
 *
 * @var BcAdminAppView $this
 * @var \Cake\Datasource\EntityInterface $plugin
 * @var int $scriptNum
 * @var array $scriptMessages
 * @var string $siteVer
 * @var string $baserVer
 * @var int $siteVerPoint
 * @var int $baserVerPoint
 * @var string $log
 */
if (!($baserVerPoint === false || $siteVerPoint === false) && ($baserVer !== $siteVer || $scriptNum)) {
  $requireUpdate = true;
} else {
  $requireUpdate = false;
}
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
    <li><?php echo sprintf(__d('baser', '%1$sのバージョン： <strong>%2$s</strong>'), $plugin->title . 'プラグイン', $baserVer) ?></li>
    <li><?php echo sprintf(__d('baser', '現在のデータベースのバージョン：<strong> %s </strong>'), $siteVer) ?></li>
  </ul>
  <?php if ($scriptNum || $scriptMessages): ?>
    <div class="em-box">
      <?php if ($baserVerPoint === false || $siteVerPoint === false): ?>
        <h3><?php echo __d('baser', 'α版の場合はアップデートサポート外です。') ?></h3>
      <?php elseif ($baserVer !== $siteVer || $scriptNum): ?>
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
    <div class="bca-actions">
      <?php echo $this->BcAdminForm->submit(__d('baser', 'アップデート実行'), [
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'data-bca-btn-type' => 'save',
        'id' => 'BtnUpdate',
        'div' => false
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
          ['class' => 'button-small']
        ) ?>
      <?php endif ?>
    <?php else: ?>
      <?php $this->BcBaser->link(
        __d('baser', 'プラグイン一覧に移動する'),
        ['controller' => 'plugins', 'action' => 'index'],
        ['class' => 'button-small']
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
