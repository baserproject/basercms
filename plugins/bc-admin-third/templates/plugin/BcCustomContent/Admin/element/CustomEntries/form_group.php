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
 * @var \BcCustomContent\Model\Entity\CustomLink $customLink
 * @checked
 * @noTodo
 * @unitTest
 */
if(empty($customLink->children)) return;
?>

<table class="bca-form-table">
  <?php foreach($customLink->children as $child): ?>
    <tr>
      <th class="bca-form-table__label">
        <?php echo $this->CustomContentAdmin->label($child, [
          'fieldName' => "{$customLink->name}.{$key}.{$child->name}"
        ]) ?>&nbsp;&nbsp;
        <?php echo $this->CustomContentAdmin->required($child) ?>
      </th>
      <td class="bca-form-table__input">
        <?php echo $this->CustomContentAdmin->control($child, [
          'fieldName' => "{$customLink->name}.{$key}.{$child->name}",
        ]) ?>
        <?php echo $this->CustomContentAdmin->error($child, [
          'fieldName' => "{$customLink->name}_{$key}_{$child->name}",
        ]) ?>
        <?php $this->BcAdminForm->unlockField("{$customLink->name}.{$child->name}") ?>
      </td>
    </tr>
  <?php endforeach ?>
</table>
<?php echo $this->CustomContentAdmin->getGroupErrors($customLink) ?>
