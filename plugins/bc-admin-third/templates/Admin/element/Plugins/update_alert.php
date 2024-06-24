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
 * プラグインアップデートのアラート
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var bool $isUpdatable
 * @var bool $isCore
 * @var bool $coreDownloaded
 */
?>


<?php if ($isUpdatable && $coreDownloaded): ?>
  <div class="bca-panel-box">
    <div class="section">
      <h2 class="bca-main__heading" data-bca-heading-size="lg">
        <?php echo __d('baser_core', 'データベースのバックアップは行いましたか？') ?>
      </h2>
      <p>
        <?php echo __d('baser_core', 'バックアップを行われていない場合は、アップデートを実行する前にデータベースのバックアップを行いましょう。') ?>
        <small>※ <?php echo __d('baser_core', 'アップデート処理は必ず自己責任で行ってください。') ?></small><br/>
      </p>
    </div>
  </div>
  <?php if($isCore): ?>
    <div class="bca-panel-box">
      <div class="section">
        <h2 class="bca-main__heading" data-bca-heading-size="lg">
          <?php echo __d('baser_core', 'リリースノートのアップデート時の注意事項は読まれましたか？') ?>
        </h2>
        <p>
          <?php echo __d('baser_core', 'リリースバージョンによっては、追加作業が必要となる場合があるので注意が必要です。<br>
          公式サイトの <a href="https://basercms.net/news/archives/category/release" target="_blank" class="outside-link">リリースノート</a> を必ず確認してください。'
          ) ?>
        </p>
      </div>
    </div>
  <?php endif ?>
<?php endif ?>
