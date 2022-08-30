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
 * Dblogs index
 * @var AppView $this
 * @checked
 * @unitTest
 * @noTodo
 */

$this->BcAdmin->setTitle(__d('baser', '最近の動き'));
$this->BcAdmin->setSearch('dblogs_index');
?>

<section id="DataList">
  <?php $this->BcBaser->element('Dblogs/index_list') ?>
</section>
