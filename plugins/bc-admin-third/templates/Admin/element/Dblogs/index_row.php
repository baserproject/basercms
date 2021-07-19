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

?>

<tr>
  <td class="bca-table-listup__tbody-td" style="width:140px;"><?php echo h($dblog->id) ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo nl2br(h($dblog->message)) ?></td>
  <td class="bca-table-listup__tbody-td" style="width:140px;">
    <?php if ($dblog->user): ?>
    <?php echo h($dblog->user->name) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:200px;"><?php echo $this->BcTime->format($dblog->created, 'YYYY-MM-dd HH:mm:ss') ?></td>
</tr>
