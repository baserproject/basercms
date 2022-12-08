<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事コメント 一覧　テーブル
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcListTable->setColumnNumber(7);
$this->BcBaser->js('BcBlog.admin/blog_comments/index.bundle', false);
?>


<!-- 一括処理 -->
<div class="bca-data-list__top">
  <?php if ($this->BcBaser->isAdminUser()): ?>
    <div class="bca-action-table-listup">
      <?php echo $this->BcAdminForm->control('batch', [
        'type' => 'select',
        'options' => ['publish' => '公開', 'unpublish' => __d('baser', '非公開'), 'delete' => __d('baser', '削除')],
        'empty' => __d('baser', '一括処理'),
        'data-bca-select-size' => 'lg'
      ]) ?>
      <?php echo $this->BcAdminForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled', 'class' => 'bca-btn', 'data-bca-btn-size' => 'lg']) ?>
    </div>
  <?php endif ?>
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>
<!-- list -->
<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead ">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select"><?php // 一括選択 ?>
      <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => __d('baser', '一括選択')]) ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('no',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'No'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'No')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php echo $this->Paginator->sort('name',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '投稿者'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '投稿者')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('email',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'メール'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'メール')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
      <br/>
      <?php
      echo $this->Paginator->sort('url',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'URL'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'URL')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('message',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'メッセージ'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'メッセージ')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('created',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '投稿日'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '投稿日')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
      <br/>
      <?php
      echo $this->Paginator->sort('modified',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '更新日'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '更新日')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php // アクション ?>
      <?php echo __d('baser', 'アクション') ?>
    </th>
  </tr>
  </thead>
  <tbody class="bca-table-listup__tbody">
  <?php if (!empty($blogComments)): ?>
    <?php foreach($blogComments as $blogComment): ?>
      <?php $this->BcBaser->element('BlogComments/index_row', ['blogComment' => $blogComment]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td"><p
          class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>

<div class="bca-data-list__bottom">
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
    <!-- list-num -->
    <?php $this->BcBaser->element('list_num') ?>
  </div>
</div>

