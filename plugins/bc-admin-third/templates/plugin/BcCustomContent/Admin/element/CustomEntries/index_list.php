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
 * @var \Cake\ORM\ResultSet $entities
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcListTable->setColumnNumber($this->CustomContentAdmin->getEntryColumnsNum($customTable->custom_links));
$creators = $this->BcAdminForm->getControlSource('BcCustomContent.CustomEntries.creator_id');
?>


<?php if (!$customTable->has_child): ?>
  <div class="bca-data-list__top">
    <div class="bca-data-list__sub">
      <!-- pagination -->
      <?php $this->BcBaser->element('pagination') ?>
    </div>
  </div>
<?php endif ?>

<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>

    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select">
      <?php if ($customTable->has_child): ?>
        <?php echo __d('baser', 'No') ?>
      <?php else: ?>
        <?php echo $this->Paginator->sort('id', [
          'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', __d('baser', 'No')),
          'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', __d('baser', 'No'))
        ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
      <?php endif ?>
    </th>

    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select">
      <?php if ($customTable->has_child): ?>
        <?php echo __d('baser', 'タイトル') ?>
      <?php else: ?>
        <?php echo $this->Paginator->sort('title', [
          'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', __d('baser', 'タイトル')),
          'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', __d('baser', 'タイトル'))
        ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
        <?php if ($customTable->isContentTable()): ?>
          （<?php echo $this->Paginator->sort('name', [
            'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', __d('baser', 'スラッグ')),
            'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', __d('baser', 'スラッグ'))
          ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>）
        <?php endif ?>
      <?php endif ?>
    </th>

    <?php if ($customTable->custom_links): ?>
      <?php foreach($customTable->custom_links as $customLink): ?>
        <?php if (!$this->CustomContentAdmin->isDisplayEntryList($customLink)) continue ?>
        <?php $this->BcBaser->element('CustomEntries/index_list_column', ['customLink' => $customLink]) ?>
      <?php endforeach ?>
    <?php endif ?>

    <?php echo $this->BcListTable->dispatchShowHead() ?>

    <th class="bca-table-listup__thead-th">
      <?php if ($customTable->has_child): ?>
        <?php echo __d('baser', '作成者') ?>
      <?php else: ?>
        <?php if ($customTable->isContentTable()): ?>
          <?php echo $this->Paginator->sort('published', [
            'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '公開日時'),
            'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '公開日時')
          ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?><br>
        <?php endif ?>
        <?php echo $this->Paginator->sort('published', [
          'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '作成者'),
          'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '作成者')
        ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
      <?php endif ?>
    </th>

    <?php if ($customTable->isContentTable()): ?>
    <th class="bca-table-listup__thead-th">
        <?php echo $this->Paginator->sort('status', [
          'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '利用状況'),
          'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '利用状況')
        ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <?php endif ?>

    <th class="bca-table-listup__thead-th">
      <?php if ($customTable->has_child): ?>
         <?php echo __d('baser', '登録日') ?><br> <?php echo __d('baser', '更新日') ?>
      <?php else: ?>
      <?php echo $this->Paginator->sort('created', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', __d('baser', '登録日')),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', __d('baser', '登録日'))
      ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?><br>
      <?php echo $this->Paginator->sort('modified', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', __d('baser', '更新日')),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', __d('baser', '更新日'))
      ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
      <?php endif ?>
    </th>

    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>

  </tr>
  </thead>
  <tbody>
  <?php if ($entities->count()): ?>
    <?php foreach($entities as $entity): ?>
      <?php $this->BcBaser->element('CustomEntries/index_row', [
        'entries' => $entities,
        'entity' => $entity,
        'creators' => $creators
      ]) ?>
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

<?php if (!$customTable->has_child): ?>
  <div class="bca-data-list__bottom">
    <div class="bca-data-list__sub">
      <!-- pagination -->
      <?php $this->BcBaser->element('pagination') ?>
      <!-- list-num -->
      <?php $this->BcBaser->element('list_num') ?>
    </div>
  </div>
<?php endif ?>
