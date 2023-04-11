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
 * @var ArrayObject $entities
 * @var \BcCustomContent\Model\Entity\CustomEntry $entity
 * @var \BcCustomContent\Model\Entity\CustomTable $customTable
 * @var int $tableId
 * @var array $creators
 * @checked
 * @noTodo
 * @unitTest
 */
$isAllowPublish = $this->CustomContentAdmin->isAllowPublishEntry($entity);
if(!empty($customTable->custom_content)) {
  $fullUrl = $this->CustomContentAdmin->getEntryUrl($this->request->getAttribute('currentContent'), $entity);
}
?>


<tr<?php $this->BcListTable->rowClass($isAllowPublish, $entity) ?>>

  <td class="bca-table-listup__tbody-td"><?php echo $entity->id ?></td>

  <td class="bca-table-listup__tbody-td">
    <?php echo $this->CustomContentAdmin->getEntryIndexTitle($customTable, $entity) ?>
    <?php if($entity->name): ?>（<?php echo h($entity->name) ?>）<?php endif ?>
  </td>

  <?php if ($customTable->custom_links): ?>
    <?php foreach($customTable->custom_links as $customLink): ?>
      <?php if (!$this->CustomContentAdmin->isDisplayEntryList($customLink)) continue ?>
      <?php $this->BcBaser->element('CustomEntries/index_row_column', ['entity' => $entity, 'customLink' => $customLink]) ?>
    <?php endforeach ?>
  <?php endif ?>

  <td class="bca-table-listup__tbody-td" style="white-space:nowrap">
    <?php if ($customTable->isContentTable()): ?>
    <?php echo $this->BcTime->format($entity->published, 'yyyy/MM/dd HH:mm') ?>
    <br>
    <?php endif ?>
    <?php echo (isset($creators[$entity->creator_id]))? $creators[$entity->creator_id] : ''  ?>
  </td>

  <?php if ($customTable->isContentTable()): ?>
  <td class="bca-table-listup__tbody-td"><?php echo $this->BcText->booleanMark($entity->status) ?></td>
  <?php endif ?>

  <?php echo $this->BcListTable->dispatchShowRow($entity) ?>

  <td class="bca-table-listup__tbody-td" style="white-space:nowrap">
    <?php echo $this->BcTime->format($entity->created) ?>
    <br>
    <?php echo $this->BcTime->format($entity->modified) ?>
  </td>
  <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php if(!empty($customTable->custom_content)): ?>
      <?php if($isAllowPublish): ?>
        <?php $this->BcBaser->link('', $fullUrl, [
          'title' => __d('baser_core', '確認'),
          'class' => 'btn-check bca-btn-icon',
          'data-bca-btn-type' => 'preview',
          'data-bca-btn-size' => 'lg',
          'target' => '_blank'
        ]) ?>
      <?php else: ?>
        <?php $this->BcBaser->link('', [], [
          'title' => __d('baser_core', '確認'),
          'class' => 'btn-check bca-btn-icon',
          'data-bca-btn-type' => 'preview',
          'data-bca-btn-size' => 'lg',
          'data-bca-btn-status' => 'gray',
          'target' => '_blank'
        ]) ?>
      <?php endif ?>
    <?php endif ?>
    <?php $this->BcBaser->link('',
      ['action' => 'edit', $tableId, $entity->id], [
        'title' => __d('baser_core', '編集'),
        'class' => ' bca-btn-icon',
        'data-bca-btn-type' => 'edit',
        'data-bca-btn-size' => 'lg'
      ]
    ) ?>
    <?php if($customTable->has_child): ?>
    <?php if($this->CustomContentAdmin->isEnabledMoveUpEntry($entities, $entity)): ?>
      <?php echo $this->BcAdminForm->postLink('',
        ['controller' => 'CustomEntries', 'action' => 'move_up', $tableId, $entity->id], [
          'title' => __d('baser_core', '上へ移動'),
          'class' => ' bca-btn-icon',
          'data-bca-btn-type' => 'arrow-up',
          'data-bca-btn-size' => 'lg'
        ]
      ) ?>
    <?php else: ?>
      <?php $this->BcBaser->link('',
        [], [
          'title' => __d('baser_core', '上へ移動'),
          'class' => ' bca-btn-icon',
          'data-bca-btn-type' => 'arrow-up',
          'data-bca-btn-size' => 'lg',
          'data-bca-btn-status' => 'gray'
        ]
      ) ?>
    <?php endif ?>
    <?php if($this->CustomContentAdmin->isEnabledMoveDownEntry($entities, $entity)): ?>
      <?php echo $this->BcAdminForm->postLink('',
        ['controller' => 'CustomEntries', 'action' => 'move_down', $tableId, $entity->id], [
          'title' => __d('baser_core', '下へ移動'),
          'class' => ' bca-btn-icon',
          'data-bca-btn-type' => 'arrow-down',
          'data-bca-btn-size' => 'lg'
        ]
      ) ?>
    <?php else: ?>
      <?php $this->BcBaser->link('',
        [], [
          'title' => __d('baser_core', '下へ移動'),
          'class' => ' bca-btn-icon',
          'data-bca-btn-type' => 'arrow-down',
          'data-bca-btn-size' => 'lg',
          'data-bca-btn-status' => 'gray'
        ]
      ) ?>
    <?php endif ?>
    <?php endif ?>
  </td>
</tr>
