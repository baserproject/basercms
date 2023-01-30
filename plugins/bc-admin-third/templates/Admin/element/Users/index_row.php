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

use BaserCore\Model\Entity\User;
use BaserCore\View\AppView;

/**
 * users index row
 * @var AppView $this
 * @var User $user
 * @var User $loginUser
 * @checked
 * @unitTest
 * @noTodo
 */
?>

<tr>
  <td class="bca-table-listup__tbody-td"><?php echo $user->id ?></td>
  <td
    class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($user->name, ['action' => 'edit', $user->id], ['escape' => true]) ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo h($user->email) ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo h($user->nickname) ?></td>
  <td class="bca-table-listup__tbody-td" style="white-space: nowrap">
    <?php if (!empty($user->user_groups)): ?>
      <ul class="user_group">
        <?php foreach($user->user_groups as $userGroups): ?>
          <li><?php echo h($userGroups->title); ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="white-space: nowrap"><?php echo h($user->real_name_1) ?>
    &nbsp;<?php echo h($user->real_name_2) ?></td>
  <?php echo $this->BcListTable->dispatchShowRow($user) ?>
  <td class="bca-table-listup__tbody-td" style="white-space: nowrap"><?php echo $this->BcTime->format($user->created, 'yyyy-MM-dd') ?><br>
    <?php echo $this->BcTime->format($user->modified, 'yyyy-MM-dd') ?></td>
  <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php if ($loginUser->isEditableUser($user)): ?>
    <?php $this->BcBaser->link('', ['action' => 'edit', $user->id], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
    <?php endif ?>
    <?php if ($loginUser->isDeletableUser($user)): ?>
    <?= $this->BcAdminForm->postLink(
      '',
      ['action' => 'delete', $user->id],
      [
        'confirm' => __d('baser', "{0} を本当に削除してもいいですか？", $user->name),
        'title' => __d('baser', '削除'),
        'class' => 'btn-delete bca-btn-icon',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?php endif ?>
    <?php if ($loginUser->isEnableLoginAgent($user)): ?>
      <?php $this->BcBaser->link('', ['action' => 'login_agent', $user->id], ['title' => __d('baser', 'ログイン'), 'class' => 'btn-login bca-btn-icon', 'data-bca-btn-type' => 'switch', 'data-bca-btn-size' => 'lg']) ?>
    <?php endif ?>
  </td>
</tr>
<?= $this->fetch('postLink') ?>
