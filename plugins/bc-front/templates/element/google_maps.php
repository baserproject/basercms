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
 * グールグルマップ
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var string $mapId
 * @var string $width
 * @var string $height
 * @var string $zoom
 * @var string $address
 * @var string $title
 * @var string $markerText
 * @var string $latitude
 * @var string $longitude
 * @var string $apiKey
 * @var string $apiUrl
 */
if ($apiKey) {
  $this->BcBaser->js($apiUrl, false);
  $this->BcBaser->js('google_maps.bundle', false, [
    'id' => 'BsGoogleMapsScript',
    'defer' => 'defer',
    'data-zoom' => $zoom,
    'data-address' => $address,
    'data-mapId' => $mapId,
    'data-title' => $title,
    'data-markerText' => $markerText,
    'data-latitude' => $latitude,
    'data-longitude' => $longitude
  ]);
}
?>


<?php if (empty($apiKey)): ?>
  <p>
    <?php echo __d('baser_core', 'Googleマップを利用するには、Google Maps APIのキーの登録が必要です。') ?>
    <a href="https://developers.google.com/maps/web/" target="_blank">キーを取得</a>して、
    <?php echo __d('baser_core',
      '{0} より設定してください。',
      $this->BcBaser->getLink(__d('baser_core', 'システム管理'), [
        'prefix' => 'Admin',
        'plugin' => 'BaserCore',
        'controller' => 'SiteConfigs',
        'action' => 'index'
      ])
    ) ?>
  </p>
<?php else: ?>
  <div id="<?php echo $mapId ?>" style="width: <?php echo $width ?>px; height:<?php echo $height ?>px">
    <noscript>※ <?php echo __d('baser_core', 'JavaScript を有効にしてください。') ?></noscript>
  </div>
<?php endif ?>
