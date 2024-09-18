<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\View\BcAdminAppView;
use BcSearchIndex\Model\Entity\SearchIndex;

/**
 * [ADMIN] メールフィールド 一覧　行
 *
 * @var BcAdminAppView $this
 * @var SearchIndex $searchIndex
 * @var int $count
 */

$priorities = [
  '0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', '0.4' => '0.4', '0.5' => '0.5',
  '0.6' => '0.6', '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', '1.0' => '1.0'
];
?>


<tr id="Row<?php echo $count + 1 ?>"<?php $this->BcListTable->rowClass($this->BcSearchIndex->allowPublish($searchIndex->toArray()), $searchIndex) ?>>
  <td class="row-tools bca-table-listup__tbody-td">
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('batch_targets.' . $searchIndex->id, [
        'type' => 'checkbox',
        'label' => '<span class="bca-visually-hidden">' . __d('baser_core', 'チェックする') . '</span>',
        'class' => 'batch-targets bca-checkbox__input',
        'value' => $searchIndex->id,
        'escape' => false
      ]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $searchIndex->id ?></td>
  <td class="bca-table-listup__tbody-td" style="width:15%">
    <?php echo $searchIndex->type ?><br>
    <?php $this->BcBaser->link(
      $this->BcText->noValue($searchIndex->title, __d('baser_core', '設定なし')),
      \BaserCore\Utility\BcUtil::siteUrl() . preg_replace('/^\//', '', $searchIndex->url), ['target' => '_blank', 'escape' => true]
    ) ?>
  </td>
  <td class="bca-table-listup__tbody-td">
    <?php echo h($this->Text->truncate($searchIndex->detail?? '', 50)) ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:10%;text-align:center">
    <?php echo $this->BcText->booleanMark($searchIndex->status); ?><br>
  </td>
  <td class="bca-table-listup__tbody-td" nowrap>
    <?php echo $this->BcTime->format($searchIndex->publish_begin, 'yyyy-MM-dd') ?><br>
    <?php echo $this->BcTime->format($searchIndex->publish_end, 'yyyy-MM-dd') ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:10%;white-space: nowrap">
    <?php echo $this->BcTime->format($searchIndex->created, 'yyyy-MM-dd') ?><br>
    <?php echo $this->BcTime->format($searchIndex->modified, 'yyyy-MM-dd') ?>
  </td>
  <?php echo $this->BcListTable->dispatchShowRow($searchIndex) ?>
  <td class="bca-table-listup__tbody-td" nowrap>
    <?php echo $this->BcAdminForm->control('SearchIndex.priority' . '_' . $searchIndex->id, [
      'type' => 'select',
      'options' => $priorities,
      'empty' => false,
      'class' => 'priority bca-select__select',
      'value' => $searchIndex->priority
    ]) ?>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?= $this->BcAdminForm->postLink(
    '',
    ['action' => 'delete', $searchIndex->id],
    ['confirm' => __d('baser_core', "検索インデックス No.{0} を本当に削除してもいいですか？", $searchIndex->id),
      'title' => __d('baser_core', '削除'),
      'class' => 'btn-delete bca-btn-icon',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'lg']
    ) ?>
  </td>
</tr>
