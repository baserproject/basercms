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
 * [PUBLISH] インストーラー Step5
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcAdmin->setTitle(__d('baser_core', 'baserCMSのインストール完了！'));
?>


<div class="step-5">

  <div class="em-box bca-em-box">
    <?php echo __d('baser_core', 'おめでとうございます！baserCMSのインストールが無事完了しました！ <br>管理用メールアドレスへインストール完了メールを送信しています。') ?>
  </div>

  <h2 class="bca-panel-box__title"><?php echo __d('baser_core', '次は何をしますか？') ?></h2>

  <div class="panel-box bca-panel-box corner10">
    <div class="section">
      <ul>
        <li>
          <?php $this->BcBaser->link(__d('baser_core', '管理者ダッシュボードに移動する'), ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'Dashboard', 'action' => 'index']) ?>
        </li>
        <li>
          <?php $this->BcBaser->link(__d('baser_core', 'トップページを確認する'), '/', ['target' => '_blank', 'class' => 'outside-link']) ?>
        </li>
        <li><a href="https://basercms.net" title="baserCMS公式サイト" target="_blank" class="outside-link">
            <?php echo __d('baser_core', 'baserCMS公式サイトで情報を探す') ?></a></li>
        <li><a href="https://baserproject.github.io/" title="baserCMS公式ガイド" target="_blank" class="outside-link">
            <?php echo __d('baser_core', 'baserCMS公式ガイドで学ぶ') ?></a></li>
        <li><a href="https://forum.basercms.net" title="baserCMSユーザーズフォーラム" target="_blank" class="outside-link">
            <?php echo __d('baser_core', 'フォーラムにインストールの不具合を報告する') ?></a></li>
        <li><a href="https://twitter.com/#!/basercms" title="baserCMS公式Twitter" target="_blank" class="outside-link">
            <?php echo __d('baser_core', '公式Twitterをフォローする') ?></a></li>
        <li><a href="https://facebook.com/basercms" title="baserCMS公式Facebookページ" target="_blank" class="outside-link">
            <?php echo __d('baser_core', 'Facebookでいいね！する') ?></a></li>
      </ul>
    </div>
  </div>
</div>
