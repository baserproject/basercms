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
 * [PUBLISH] インストーラー Step4
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcBaser->i18nScript([
  'message0' => __d('baser', 'サイト名を入力してください。'),
  'message1' => __d('baser', '管理用メールアドレスを入力してください。'),
  'message4' => __d('baser', 'あなたのパスワードを６文字以上で入力してください。'),
  'message5' => __d('baser', 'パスワードが確認欄のパスワードと同じではありません。'),
  'message6' => __d('baser', 'パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。')
]);
$this->BcAdmin->setTitle(__d('baser', 'baserCMSのインストール｜ステップ４'));
$this->BcBaser->js('BcInstaller.admin/installations/step4.bundle', false);
?>


<div class="step-4">

  <div class="em-box bca-em-box">
    <?php echo __d('baser', '最後に管理情報を登録します。<br />ここで入力した管理者アカウント名やパスワードは忘れないようにしておいてください。') ?>
  </div>

  <h2 class="bca-main__heading"><?php echo __d('baser', 'サイト名と管理ユーザー登録') ?></h2>

  <?php echo $this->BcAdminForm->create(null, [
    'url' => ['controller' => 'installations', 'action' => 'step4'],
    'id' => 'AdminSettingForm',
    'method' => 'post'
  ]) ?>
  <?php echo $this->BcAdminForm->control('mode', ['type' => 'hidden']) ?>
  <?php $this->BcAdminForm->unlockField('mode') ?>

  <div class="panel-box bca-panel-box corner10">
    <div class="section">
      <ul>
        <li><label><?php echo __d('baser', 'サイト名') ?></label>
          <?php echo $this->BcAdminForm->control('site_name', ['type' => 'text', 'size' => 44]); ?>
        </li>
        <li><label><?php echo __d('baser', 'Eメールアドレス') ?></label>
          <?php echo $this->BcAdminForm->control('admin_email', ['type' => 'text', 'size' => 44]); ?>
        </li>
        <li class="clearfix">
          <label><?php echo __d('baser', 'パスワード') ?></label>&nbsp;<small><?php echo __d('baser', '半角英数字（英字は大文字小文字を区別）とスペース、記号（._-:/()#,@[]+=&amp;;{}!$*）') ?></small><br>
          <div class="float-left">
            <?php echo $this->BcAdminForm->control('admin_password', ['type' => 'password']); ?>
          </div>
          <div class="float-left">
            <?php echo $this->BcAdminForm->control('admin_confirm_password', ['type' => 'password']); ?>
            <br>
            <small><?php echo __d('baser', '確認のためもう一度入力してください') ?></small>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <div class="submit bca-actions">
    <?php echo $this->BcAdminForm->button(__d('baser', '戻る'), ['type' => 'button', 'class' => 'button bca-btn bca-actions__item', 'id' => 'BtnBack']) ?>
    <?php echo $this->BcAdminForm->button(__d('baser', '登録'), [
      'type' => 'button',
      'class' => 'bca-btn bca-actions__item',
      'id' => 'BtnFinish',
      'name' => 'step5',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'data-bca-btn-type' => 'save'
    ]) ?>
  </div>

  <?php echo $this->BcAdminForm->end() ?>

</div>
