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

use BaserCore\Model\Entity\UserGroup;
use BaserCore\View\AppView;

/**
 * user groups index row
 * @var AppView $this
 * @var UserGroup $userGroup
 */
?>

<tr>
  <td class="bca-table-listup__tbody-td"><?php echo $userGroup->id ?></td>
  <td
    class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($userGroup->name, ['action' => 'edit', $userGroup->id], ['escape' => true]) ?>
    <?php if ($userGroup->users): ?><br>
      <?php foreach($userGroup->users as $user): ?>
        <span
          class="tag"><?php $this->BcBaser->link($this->BcBaser->getUserName($user), ['controller' => 'users', 'action' => 'edit', $user->id, ['escape' => true]]) ?></span>
      <?php endforeach ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo h($userGroup->title) ?></td>
  <?php echo $this->BcListTable->dispatchShowRow($userGroup) ?>
  <td class="bca-table-listup__tbody-td"><?php echo $this->BcTime->format($userGroup->created, 'yyyy-MM-dd') ?><br/>
    <?php echo $this->BcTime->format($userGroup->modified, 'yyyy-MM-dd') ?></td>
  <td class="bca-table-listup__tbody-td">
    <?php if ($userGroup->name != 'admins'): ?>
      <?php $this->BcBaser->link('', ['controller' => 'permissions', 'action' => 'index', $userGroup->id], ['title' => __d('baser', '制限'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'permission', 'data-bca-btn-size' => 'lg']) ?>
    <?php endif ?>
    <?php $this->BcBaser->link('', ['action' => 'edit', $userGroup->id], ['title' => __d('baser', '編集'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
    <?php echo $this->BcAdminForm->postLink(
      '',
      ['action' => 'copy', $userGroup->id],
      ['title' => __d('baser', 'コピー'),
        'class' => 'btn-copy bca-btn-icon',
        'data-bca-btn-type' => 'copy',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?php if ($userGroup->name != 'admins'): ?>
      <?= $this->BcAdminForm->postLink(
        '',
        ['action' => 'delete', $userGroup->id],
        ['block' => true,
          'confirm' => __d('baser', "{0} を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセス制限設定は全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。", $userGroup->name),
          'title' => __d('baser', '削除'),
          'class' => 'btn-delete bca-btn-icon',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'lg']
      ) ?>
    <?php endif ?>
  </td>
</tr>
<?= $this->fetch('postLink') ?>
