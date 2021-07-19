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

/**
 * Dblogs index
 * @var AppView $this
 */

$this->BcAdmin->setTitle(__d('baser', '最近の動き'));
$this->BcAdmin->setSearch('dblogs_index');
?>

<section id="DataList">
  <?php $this->BcBaser->element('Dblogs/index_list') ?>
</section>
