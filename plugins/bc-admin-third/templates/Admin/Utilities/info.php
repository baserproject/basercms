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
 * 環境情報
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $datasource
 * @var string $baserVersion
 * @var string $cakeVersion
 * @var string $sqlMode
 */
$this->BcAdmin->setTitle(__d('baser_core', '環境情報'));
?>

<section class="bca-section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'baserCMS環境') ?></h2>
  <ul>
    <li><?php echo __d('baser_core', '設置フォルダ') ?>： <?php echo ROOT . DS ?></li>
    <li><?php echo __d('baser_core', 'データベース') ?>： <?php echo $datasource ?></li>
    <?php if($sqlMode): ?>
    <li><?php echo __d('baser_core', 'SQLモード') ?>： <?php echo $sqlMode ?></li>
    <?php endif ?>
    <li><?php echo __d('baser_core', 'baserCMSバージョン') ?>： <?php echo $baserVersion ?></li>
    <li><?php echo __d('baser_core', 'CakePHPバージョン') ?>： <?php echo $cakeVersion ?></li>
  </ul>
</section>

<section class="bca-section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'PHP環境') ?></h2>
  <iframe
    src="<?php $this->BcBaser->url(['action' => 'phpinfo']) ?>"
    class="phpinfo"
    width="100%"
    height="100%"
    style="min-height:600px">
  </iframe>
</section>
