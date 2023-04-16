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
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Dblog $dblog
 * @checked
 * @unitTest
 * @noTodo
 */
?>

<tr>
  <td class="bca-table-listup__tbody-td" style="width:140px;"><?php echo h($dblog->id) ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo nl2br(h($dblog->message)) ?></td>
  <td class="bca-table-listup__tbody-td" style="width:140px;">
    <?php if ($dblog->user): ?>
    <?php echo h($dblog->user->getDisplayName()) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:200px;"><?php echo $this->BcTime->format($dblog->created, 'yyyy-MM-dd HH:mm:ss') ?></td>
</tr>
