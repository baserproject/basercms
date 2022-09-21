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
 * [ADMIN] データメンテナンス
 * @var \BaserCore\View\BcAdminAppView $this
 * @noTodo
 * @checked
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', 'データメンテナンス'));
$this->BcAdmin->setHelp('tools_maintenance');
?>


<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'データのバックアップ') ?></h2>
  <p class="bca-main__text">
    <?php echo __d('baser', 'データベースのデータをバックアップファイルとしてPCにダウンロードします。') ?>
  </p>
  <?php echo $this->BcAdminForm->create(null, [
    'type' => 'get',
    'url' => ['action' => 'maintenance', 'backup'],
    'target' => '_blank'
  ]) ?>
  <p class="bca-main__text">
    <?php echo $this->BcAdminForm->control('backup_encoding', [
      'type' => 'radio',
      'options' => ['UTF-8' => 'UTF-8', 'SJIS-win' => 'SJIS'],
      'value' => 'UTF-8'
    ]) ?>
    <?php echo $this->BcAdminForm->error('backup_encoding') ?>
  </p>
  <p class="bca-main__text">
    <?php echo $this->BcAdminForm->submit(__d('baser', 'ダウンロード'), [
      'div' => false,
      'class' => 'bca-btn',
      'id' => 'BtnDownload'
    ]) ?>
  </p>
  <?php echo $this->BcAdminForm->end() ?>
</div>

<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'データの復元') ?></h2>
  <p class="bca-main__text">
    <?php echo __d('baser', 'バックアップファイルをアップロードし、データベースのデータを復元します。') ?><br/>
    <small><?php echo __d('baser', 'ダウンロードしたバックアップファイルをZIPファイルのままアップロードします。') ?></small>
  </p>
  <?php echo $this->BcAdminForm->create(null, ['url' => ['action' => 'maintenance', 'restore'], 'type' => 'file']) ?>
  <p class="bca-main__text">
    <?php echo $this->BcAdminForm->control('encoding', [
      'type' => 'radio',
      'options' => ['auto' => __d('baser', '自動判別'), 'UTF-8' => 'UTF-8', 'SJIS-win' => 'SJIS'],
      'value' => 'auto'
    ]) ?>
    <?php echo $this->BcAdminForm->error('encoding') ?>
  </p>
  <p class="bca-main__text"><?php echo $this->BcAdminForm->control('backup', ['type' => 'file']) ?>
    <?php echo $this->BcAdminForm->error('backup') ?></p>
  <p class="bca-main__text">
    <?php echo $this->BcAdminForm->submit(__d('baser', 'アップロード'), [
      'div' => false,
      'class' => 'bca-loading bca-btn',
      'id' => 'BtnUpload'
    ]) ?>
  </p>
  <?php echo $this->BcAdminForm->end() ?>
</div>

<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'データの初期化') ?></h2>
  <p class="bca-main__text"><?php echo __d('baser', '現在のデータを、baserCMSコアの初期データでリセットします。') ?></p>
  <p><?php echo $this->BcAdminForm->postLink(__d('baser', 'データリセット'),
    ['action' => 'reset_data'],
    [
      'class' => 'bca-btn',
      // ↓↓↓ 改行すると、実行時にJavascript 側でエラーとなるので注意
      'confirm' => __d('baser', "現在のデータを、baserCMSコアの初期データでリセットします。よろしいですか？\\n\\n※ 初期データを読み込むと現在登録されている記事データや設定は全て上書きされますのでご注意ください。\\n※ 管理ログは読み込まれず、ユーザー情報はログインしているユーザーのみに初期化されます。")
    ]) ?>
  </p>
</div>
