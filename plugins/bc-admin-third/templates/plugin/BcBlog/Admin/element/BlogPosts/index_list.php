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

/**
 * [ADMIN] ブログ記事 一覧　テーブル
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @var \Cake\Datasource\QueryInterface $posts
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js(['BcBlog.admin/blog_posts/index.bundle'], false);
$this->BcListTable->setColumnNumber(9);
?>


<div class="bca-data-list__top">
  <!-- 一括処理 -->
  <?php if ($this->BcBaser->isAdminUser()): ?>
    <div class="bca-action-table-listup">
      <?php echo $this->BcAdminForm->control('batch', [
        'type' => 'select',
        'options' => [
          'publish' => __d('baser_core', '公開'),
          'unpublish' => __d('baser_core', '非公開'),
          'delete' => __d('baser_core', '削除')
        ],
        'empty' => __d('baser_core', '一括処理'),
        'data-bca-select-size' => 'lg'
      ]) ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '適用'), [
        'id' => 'BtnApplyBatch',
        'disabled' => 'disabled',
        'class' => 'bca-btn',
        'data-bca-btn-size' => 'lg'
      ]) ?>
    </div>
  <?php endif ?>
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>

<!-- list -->
<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select" title="<?php echo __d('baser_core', '一括選択') ?>">
      <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => ' ', 'title' => __d('baser_core', '一括選択')]) ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php // No ?>
      <?php echo $this->Paginator->sort('no', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'No'),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'No')
      ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php // タイトル＋アイキャッチ ?>
      <?php echo $this->Paginator->sort('name', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'タイトル'),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'タイトル')
      ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php // カテゴリ ?>
      <?php echo $this->Paginator->sort('BlogCategories.name', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'カテゴリ'),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'カテゴリ')
      ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <?php if ($blogContent->tag_use): ?>
      <th class="bca-table-listup__thead-th"><?php // タグ ?>
        <?php echo __d('baser_core', 'タグ') ?>
      </th>
    <?php endif ?>
    <?php if ($blogContent->comment_use): ?>
      <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'コメント') ?></th>
    <?php endif ?>
    <th class="bca-table-listup__thead-th"><?php // 作成者 ?>
      <?php echo $this->Paginator->sort('user_id', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '作成者'),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '作成者')
      ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php // 投稿日 ?>
      <?php echo $this->Paginator->sort('posted', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '投稿日'),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '投稿日')
      ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th"><?php // アクション ?>
      <?php echo __d('baser_core', 'アクション') ?>
    </th>
  </tr>
  </thead>
  <tbody class="bca-table-listup__tbody">
  <?php if ($posts->count()): ?>
    <?php foreach($posts as $post): ?>
      <?php $this->BcBaser->element('BlogPosts/index_row', ['post' => $post]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td">
        <p class="no-data"><?php echo __d('baser_core', 'データが見つかりませんでした。') ?></p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
<?= $this->fetch('postLink') ?>

<div class="bca-data-list__bottom">
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
    <!-- list-num -->
    <?php $this->BcBaser->element('list_num') ?>
  </div>
</div>
