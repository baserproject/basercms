<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\View\AppView;

/**
 * プラグインいストール
 * @var AppView $this
 * @var string $installMessage インストールメッセージ
 * @var bool $isInstallable インストール可能かどうか
 * @var \BaserCore\Model\Entity\Plugin $plugin
 * @var bool $dbInit
 */
$this->BcBaser->i18nScript([
    'message1' => __d('baser', 'プラグインのデータを初期化します。よろしいですか？'),
]);
$this->BcBaser->js('admin/plugins/install.bundle', false);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
    'url' => ['action' => 'add'],
    'title' => __d('baser', '新規追加'),
]);
?>


<?php echo $this->BcAdminForm->control('ResetDbUrl', ['type' => 'hidden', 'value' => $this->BcBaser->getUrl(['action' => 'reset_db'])]) ?>

<?php if ($installMessage): ?>
    <div id="UpdateMessage"><?php echo $installMessage ?></div>
<?php endif ?>

<?php if ($isInstallable): ?>
    <?php echo $this->BcAdminForm->create($plugin, ['url' => [$plugin->name]]) ?>
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
            'options' => ['1' => __d('baser', '全てのユーザーで利用'), '2' => __d('baser', '管理ユーザーのみ利用')]
        ]) ?>
    </section>

    <div>
        <?php echo $this->BcAdminForm->error('name') ?>
        <?php echo $this->BcAdminForm->error('title') ?>
    </div>


    <div class="bca-actions">
        <?php if ($dbInit): ?>
            <?php echo $this->BcAdminForm->submit(__d('baser', '有効化'), ['div' => false, 'class' => 'button bca-btn', 'id' => 'BtnSave']) ?>&nbsp;&nbsp;
            <?php echo $this->BcAdminForm->submit(__d('baser', 'プラグインのデータを初期化する'), ['div' => false, 'class' => 'button bca-btn', 'id' => 'BtnReset']) ?>
        <?php else: ?>
            <?php echo $this->BcAdminForm->submit(__d('baser', 'インストール'), ['div' => false, 'class' => 'button bca-btn', 'id' => 'BtnSave']) ?>
        <?php endif; ?>
    </div>

    <?php echo $this->BcAdminForm->end() ?>
<?php endif ?>
