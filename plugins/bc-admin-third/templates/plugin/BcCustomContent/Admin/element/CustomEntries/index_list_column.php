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
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $customTable
 * @var \BcCustomContent\Model\Entity\CustomLink $customLink
 * @checked
 * @noTodo
 * @unitTest
 */
?>

<th class="bca-table-listup__thead-th">
  <?php if ($customTable->has_child): ?>
    <?php echo h($customLink->title) ?>
  <?php else: ?>
  <?php echo $this->Paginator->sort($customLink->name, [
    'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', $customLink->title),
    'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', $customLink->title)
  ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
  <?php endif ?>
</th>

