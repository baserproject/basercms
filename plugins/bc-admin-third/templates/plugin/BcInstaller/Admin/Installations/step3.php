<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] インストーラー Step3
 * @var \BaserCore\View\BcAdminAppView $this
 * @var bool $blDBSettingsOK
 * @var array $dbDataPatterns
 * @var array $dbsource
 */
$this->BcBaser->i18nScript([
  'message1' => __d('baser_core', 'データベースのホスト名を入力してください。'),
  'message2' => __d('baser_core', 'データベースユーザー名を入力してください。'),
  'message3' => __d('baser_core', 'データベース名を入力してください。'),
  'message4' => __d('baser_core', '他のアプリケーションと重複しないプレフィックスを入力してください。（例）mysite_'),
  'message5' => __d('baser_core', 'プレフィックスの末尾はアンダースコアにしてください。（例）mysite_'),
  'message6' => __d('baser_core', 'プレフィックスは英数字とアンダースコアの組み合わせにしてください。（例）mysite_'),
  'message7' => __d('baser_core', 'ドット（.）を含むデータベース名にはインストールできません。'),
  'message8' => __d('baser_core', 'データベースのポートナンバーを入力してください。')
]);
$this->BcBaser->js('BcInstaller.admin/installations/step3.bundle', false, [
  'id' => 'AdminInstallersScript',
  'data-dbSettingOk' => $blDBSettingsOK
]);
$this->BcAdmin->setTitle(__d('baser_core', 'baserCMSのインストール｜ステップ３'));
?>


<?php echo $this->BcAdminForm->create(null, ['url' => ['controller' => 'installations', 'action' => 'step3'], 'id' => 'DbSettingForm']) ?>
<?php echo $this->BcAdminForm->control('mode', ['style' => 'display:none', 'type' => 'hidden', 'id' => 'mode']); ?>
<?php $this->BcAdminForm->unlockField('mode') ?>

<div class="step-3">

  <div class="em-box bca-em-box">
    <?php echo __d('baser_core', 'データベースサーバーの場合は、データベースの接続情報を入力し接続テストを実行してください。<br>MySQL / PostgreSQLの場合は、データベースが存在し初期化されている必要があります。<br><strong>既に用意したデータベースにデータが存在する場合は、初期データで上書きされてしまうので注意してください。</strong>') ?>
  </div>

  <h2 class="bca-main__heading"><?php echo __d('baser_core', 'データベース設定') ?></h2>

  <div class="panel-box bca-panel-box corner10">
    <div class="section">

      <h3 class="bca-panel-box__title"><?php echo __d('baser_core', '接続情報') ?></h3>

      <ul>
        <li id="liDbType">
          <?php echo $this->BcAdminForm->label('dbType', __d('baser_core', 'データベースタイプ')); ?>
          <?php echo $this->BcAdminForm->control('dbType', ['type' => 'select', 'options' => $dbsource, 'id' => 'dbType']) ?>
          <br>
          <small>※ <?php echo __d('baser_core', 'MySQL・PostgreSQL・SQLiteの中で、このサーバーで利用できるものが表示されています。') ?></small>
        </li>
        <li id="liDbHost">
          <?php echo $this->BcAdminForm->label('dbHost', __d('baser_core', 'データベースホスト名')); ?>
          <?php echo $this->BcAdminForm->control('dbHost', ['type' => 'text', 'maxlength' => '300', 'size' => 45, 'id' => 'dbHost']); ?>
        </li>
        <li id="liDbUser" class="clearfix">
          <label><?php echo __d('baser_core', 'ログイン情報') ?></label>
          <div class="float-left">
            <?php echo $this->BcAdminForm->control('dbUsername', ['type' => 'text', 'maxlength' => '100', 'id' => 'dbUserName']); ?>
            <br>
            <small><?php echo __d('baser_core', 'ユーザー名') ?></small></div>
          <div class="float-left">
            <?php echo $this->BcAdminForm->control('dbPassword', ['type' => 'password', 'maxlength' => '100', 'id' => 'dbPassword']); ?>
            <br>
            <small><?php echo __d('baser_core', 'パスワード') ?></small></div>
        </li>
        <li id="liDbInfo" class="clearfix">
          <label><?php echo __d('baser_core', 'データベース情報') ?></label>
          <div class="float-left">
            <?php echo $this->BcAdminForm->control('dbName', ['type' => 'text', 'maxlength' => '100', 'id' => 'dbName']); ?>
            <br>
            <small><?php echo __d('baser_core', 'データベース名') ?></small>
          </div>
          <div class="float-left">
            <?php echo $this->BcAdminForm->control('dbPrefix', ['type' => 'text', 'size' => '10', 'id' => 'dbPrefix', 'placeholder' => 'mysite_']); ?>
            <br>
            <small><?php echo __d('baser_core', 'プレフィックス') ?></small>
          </div>
          <div class="float-left">
            <?php echo $this->BcAdminForm->control('dbPort', ['type' => 'text', 'maxlength' => '5', 'size' => 5, 'id' => 'dbPort']); ?>
            <br>
            <small><?php echo __d('baser_core', 'ポート') ?></small>
          </div>
          <br style="clear:both"/><br>
          <small>※ <?php echo __d('baser_core', 'プレフィックスは英数字とアンダースコアの組み合わせとし末尾はアンダースコアにしてください。<br />※ ホスト名、データベース名、ポートは実際の環境に合わせて書き換えてください。') ?></small>
        </li>
      </ul>
    </div>

  </div>
  <div class="panel-box bca-panel-box corner10">

    <div class="section">
      <h3 class="bca-panel-box__title"><?php echo __d('baser_core', 'オプション') ?></h3>

      <ul>
        <li><label><?php echo __d('baser_core', 'フロントテーマと初期データ') ?></label>
          <p class="bca-main__text">
            <?php echo __d('baser_core', '利用するフロント側のデザインテーマと、コアパッケージやデザインテーマが保有するデモンストレーション用データを選択します。') ?>
          </p>
          <?php echo $this->BcAdminForm->control('dbDataPattern', ['type' => 'select', 'options' => $dbDataPatterns, 'id' => 'dbDataPattern']) ?>
          <br>
          <small>
            <?php if (isset($dbDataPatterns[$this->request->getData('dbDataPattern')])): ?>
            ※ <?php echo sprintf(__d('baser_core', '初めてインストールされる方は、%s を選択してください。'), $dbDataPatterns[$this->request->getData('dbDataPattern')]) ?></small>
        <?php endif; ?>
        </li>
      </ul>
    </div>

  </div>

  <div class="submit bca-actions">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '戻る'), ['type' => 'button', 'class' => 'button bca-btn bca-actions__item', 'id' => 'BtnBack']) ?>
    <?php if (!$blDBSettingsOK): ?>
    <?php echo $this->BcAdminForm->button(__d('baser_core', '接続テスト'), [
      'type' => 'button',
      'class' => 'btn-orange button bca-btn bca-actions__item',
      'id' => 'BtnCheckDb',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'data-bca-btn-type' => 'save']) ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '次のステップへ'), [
        'type' => 'button',
        'class' => 'button bca-btn bca-actions__item',
        'id' => 'BtnNext',
        'disabled' => 'disabled'
      ]) ?>
    <?php else: ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '次のステップへ'), [
        'type' => 'button',
        'class' => 'button bca-btn bca-actions__item',
        'id' => 'BtnNext',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'data-bca-btn-type' => 'save']) ?>
    <?php endif ?>
  </div>

  <?php echo $this->BcAdminForm->end() ?>

</div>
