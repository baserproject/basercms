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
 * プラグインインストール
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Plugin $plugin
 * @var string $installStatusMessage
 */
$this->BcBaser->i18nScript([
  'message1' => __d('baser', 'プラグインのデータを初期化します。よろしいですか？'),
]);
$this->BcBaser->js('admin/plugins/install.bundle', false, [
  'id' => 'AdminPluginInstallScript',
  'data-resetDbUrl' => $this->BcBaser->getUrl(['action' => 'reset_db'])
]);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add'],
  'title' => __d('baser', '新規追加'),
]);
$this->BcAdmin->setTitle(__d('baser', '新規プラグイン登録'));
$this->BcAdmin->setHelp('plugins_install');
?>


<?php if ($installStatusMessage): ?>
  <div id="UpdateMessage"><?php echo $installStatusMessage ?></div>
<?php endif ?>

<?php if (!$installStatusMessage): ?>
  <?php echo $this->BcAdminForm->create($plugin, ['url' => [$plugin->name], 'id' => 'AdminPluginInstallForm']) ?>
  <?php echo $this->BcAdminForm->control('name', ['type' => 'hidden']) ?>
  <?php echo $this->BcAdminForm->control('title', ['type' => 'hidden']) ?>
  <?php echo $this->BcAdminForm->control('status', ['type' => 'hidden']) ?>
  <?php echo $this->BcAdminForm->control('version', ['type' => 'hidden']) ?>

  <div class="bca-em-box">
    <?php echo h($plugin->name) . ' ' . $plugin->version ?>
    <?php if ($plugin->title): ?>
      （<?php echo h($plugin->title) ?>）
    <?php endif ?>
  </div>

  <section class="bca-section align-center" data-bca-section-type="form-group">
    <?php echo $this->BcAdminForm->control('permission', [
      'type' => 'radio',
      'options' => ['1' => __d('baser', '全てのユーザーで利用'), '0' => __d('baser', '管理ユーザーのみ利用')]
    ]) ?>
  </section>

  <div>
    <?php echo $this->BcAdminForm->error('name') ?>
    <?php echo $this->BcAdminForm->error('title') ?>
  </div>


  <div class="bca-actions">
    <?php if ($plugin->db_init): ?>
      <?php echo $this->BcAdminForm->submit(__d('baser', 'プラグインのデータを初期化する'), [
        'div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'id' => 'BtnReset'
      ]) ?>
      <?php echo $this->BcAdminForm->submit(__d('baser', '有効化'), [
        'div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave'
      ]) ?>&nbsp;&nbsp;
    <?php else: ?>
      <?php echo $this->BcAdminForm->submit(__d('baser', 'インストール'), [
        'div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave'
      ]) ?>
    <?php endif; ?>
  </div>

  <?php echo $this->BcAdminForm->end() ?>
<?php endif ?>
