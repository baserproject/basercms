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
 * 現在のバージョン状況
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \Cake\Datasource\EntityInterface $plugin
 * @var int $scriptNum
 * @var array $scriptMessages
 * @var string $dbVersion
 * @var string $programVersion
 * @var string $availableVersion
 * @var int $dbVerPoint
 * @var int $programVerPoint
 */
$isNotSupported = ($programVerPoint === false || $dbVerPoint === false);
$dbMessage = '';
if ($isNotSupported) {
  $dbMessage = __d('baser_core', '開発版の場合はアップデートサポート外です。');
} elseif ($programVersion === $dbVersion) {
  if (!$availableVersion) {
    $dbMessage = __d('baser_core', 'データベースのバージョンは最新です。');
  }
} else {
  $dbMessage = __d('baser_core', 'データベースのバージョンが古い状態です。');
}
?>


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

  <div class="em-box">
    <?php if ($dbMessage): ?>
      <p><?php echo $dbMessage ?></p>
    <?php endif ?>
    <?php if (!$isNotSupported && $scriptNum): ?>
      <p><?php echo __d('baser_core', 'アップデートプログラムが {0} つあります。', $scriptNum) ?></p>
    <?php endif ?>

    <?php if ($scriptMessages): ?>
      <table class="bca-table-listup">
        <?php foreach($scriptMessages as $key => $scriptMessage): ?>
          <tr>
            <td class="bca-table-listup__tbody-td"><?php echo $key ?></td>
            <td class="bca-table-listup__tbody-td">
              <?php
              if(is_array($scriptMessage)) {
                $scriptMessage = implode("\n", $scriptMessage);
              }
              ?>
              <p class="error-message"><?php echo nl2br(h($scriptMessage)) ?></p>
            </td>
          </tr>
        <?php endforeach ?>
      </table>
    <?php endif ?>
  </div>

</div>
