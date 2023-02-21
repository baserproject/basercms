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
 * @var \BcCustomContent\Model\Entity\CustomEntry $entity
 * @var int $tableId
 * @checked
 * @noTodo
 * @unitTest
 */
$value = $this->CustomContent->getFieldValue($entity, $customLink->name, [
  'beforeLinefeed' => false,
  'afterLinefeed' => false
])
?>


<td class="bca-table-listup__tbody-td">
  <?php if($entity->custom_table->display_field === $customLink->name): ?>
  <?php $this->BcBaser->link($value, ['action' => 'edit', $tableId, $entity->id]) ?>
  <?php else : ?>
  <?php echo $value ?>
  <?php endif ?>
</td>

