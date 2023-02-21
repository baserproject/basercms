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
 * カスタムテーブル / 行
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomField $entity
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<tr<?php $this->BcListTable->rowClass($entity->status, $entity) ?>>
  <td class="bca-table-listup__tbody-td"><?php echo $entity->id ?></td>
  <td class="bca-table-listup__tbody-td">
    <?php $this->BcBaser->link($entity->name, ['action' => 'edit', $entity->id]) ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo h($entity->title) ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo h($entity->getTypeTitle()) ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo $this->BcText->booleanMark($entity->required) ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo $this->BcText->booleanMark($entity->status) ?></td>

  <?php echo $this->BcListTable->dispatchShowRow($entity) ?>

  <td class="bca-table-listup__tbody-td" style="white-space:nowrap">
    <?php echo $this->BcTime->format($entity->created) ?>
    <br>
    <?php echo $this->BcTime->format($entity->modified) ?>
  </td>
  <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php $this->BcBaser->link('',
      ['action' => 'edit', $entity->id], [
        'title' => __d('baser', '編集'),
        'class' => ' bca-btn-icon',
        'data-bca-btn-type' => 'edit',
        'data-bca-btn-size' => 'lg'
      ]
    ) ?>
  </td>
</tr>
