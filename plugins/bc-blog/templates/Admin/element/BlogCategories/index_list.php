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
 * [ADMIN] ブログカテゴリ 一覧　テーブル
 * @var BcBlog\View\BlogAdminAppView $this
 * @var array $blogCategories
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcListTable->setColumnNumber(6);
?>


<div class="bca-data-list__top">
  <!-- 一括処理 -->
  <?php if ($this->BcBaser->isAdminUser()): ?>
    <div class="bca-action-table-listup">
      <?php echo $this->BcAdminForm->control('batch', [
          'type' => 'select',
          'options' => ['delete' => __d('baser', '削除')],
          'empty' => __d('baser', '一括処理'),
          'data-bca-select-size' => 'lg']
      ) ?>
      <?php echo $this->BcAdminForm->button(__d('baser', '適用'), [
        'id' => 'BtnApplyBatch',
        'disabled' => 'disabled',
        'class' => 'bca-btn',
        'data-bca-btn-size' => 'lg'
      ]) ?>
    </div>
  <?php endif ?>
</div>


<!-- list -->
<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select">
      <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => __d('baser', '一括選択')]) ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'No') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'カテゴリ名') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'カテゴリタイトル') ?></th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', '登録日') ?><br/><?php echo __d('baser', '更新日') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody class="bca-table-listup__tbody">
  <?php if (!empty($blogCategories)): ?>
    <?php $currentDepth = 0 ?>
    <?php foreach($blogCategories as $blogCategory): ?>
      <?php
      $rowIdTmps[$blogCategory->depth] = $blogCategory->id;
      // 階層が上がったタイミングで同階層よりしたのIDを削除
      if ($currentDepth > $blogCategory->depth) {
        $i = $blogCategory->depth + 1;
        while(isset($rowIdTmps[$i])) {
          unset($rowIdTmps[$i]);
          $i++;
        }
      }
      $currentDepth = $blogCategory->depth;
      $rowGroupId = [];
      foreach($rowIdTmps as $rowIdTmp) {
        $rowGroupId[] = 'row-group-' . $rowIdTmp;
      }
      $rowGroupClass = ' class="depth-' . $blogCategory->depth . ' ' . implode(' ', $rowGroupId) . '"';
      ?>
      <?php $currentDepth = $blogCategory->depth ?>
      <?php $this->BcBaser->element('BlogCategories/index_row', ['blogCategory' => $blogCategory, 'rowGroupClass' => $rowGroupClass]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td">
        <p class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
