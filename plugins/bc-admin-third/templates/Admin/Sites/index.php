<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * サイト一覧
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcAdmin->setTitle(__d('baser', 'サイト一覧'));
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add'],
  'title' => __d('baser', '新規追加'),
]);
?>


<div id="DataList" class="bca-data-list">
  <?php $this->BcBaser->element('Sites/index_list') ?>
</div>
