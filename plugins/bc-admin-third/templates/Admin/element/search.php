<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\View\BcAdminAppView;

/**
 * Search
 *
 * BcAdminHelper::search() より呼び出される
 *
 * @var BcAdminAppView $this
 * @var string $search 検索条件テンプレート
 * @var bool $adminSearchOpened 検索エリアが開いているかどうか
 * @var string $adminSearchOpenedSaveUrl 検索エリアの開閉状態を保存するためのURL
 */
$this->BcBaser->js('admin/search.bundle', true, [
  'id' => 'AdminSearchScript',
  'data-adminSearchOpened' => $adminSearchOpened,
  'data-adminSearchOpenedSaveUrl' => $adminSearchOpenedSaveUrl
]);
?>


<div class="bca-search">
  <h2 class="head bca-search__head"><a href="javascript:void(0)" id="BtnMenuSearch"
                                       class="bca-icon--search"><?php echo __d('baser', '絞り込み検索') ?></a></h2>
  <div id="Search" class="body bca-search__body">
    <?php $this->BcBaser->element('search/' . $search) ?>
  </div>
  <!-- / #Search clearfix --></div>
