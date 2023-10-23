<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @var int $listId
 * @var string $installMessage
 * @var \Cake\ORM\ResultSet $uploaderFiles
 * @checked
 * @noTodo
 * @unitTest
 */
// IE文字化け対策
header('Content-type: text/html; charset=utf-8');
$users = $this->BcAdminForm->getControlSource("UploaderFiles.user_id");
$uploaderCategories = $this->BcAdminForm->getControlSource("UploaderFiles.uploader_category_id");
//==============================================================================
// Ajaxで呼び出される事が前提のためインラインで呼び出し
//==============================================================================
$this->BcBaser->js([
  'BcUploader.admin/uploader_files/index_table.bundle'
]);
?>


<div id="DivTableList">

  <?php if ($listId): ?>
    <div id="UploaderForm">
      <?php if (!$installMessage): ?>
        <div>
          <?php if ($uploaderCategories): ?>
            <?php echo $this->BcAdminForm->control('uploader_category_id', [
              'type' => 'select',
              'options' => $uploaderCategories,
              'empty' => __d('baser_core', 'カテゴリ指定なし'),
              'id' => 'UploaderFileUploaderCategoryId' . $listId,
              'style' => 'width:100px'
            ]) ?>&nbsp;
          <?php endif ?>
          <span id="SpanUploadFile<?= h($listId) ?>">
            <?php echo $this->BcAdminForm->control('file', [
              'type' => 'file',
              'id' => 'UploaderFileFile' . $listId,
              'class' => 'uploader-file-file',
              'div' => false
            ]) ?>
		      </span>
        </div>
      <?php endif ?>
    </div>
  <?php endif ?>


  <?php if ($installMessage): ?>
    <p style="color:#C00;font-weight:bold"><?php echo $installMessage ?></p>
  <?php endif ?>

  <div class="bca-data-list__top bca-list-head">
    <?php if (!$listId): ?>
      <div id="UploaderForm">
        <?php if (!$installMessage): ?>
          <div>
            <label for="UploaderFileUploaderCategoryId"><?php echo __d('baser_core', 'アップロード') ?></label>
            <?php if ($uploaderCategories): ?>
              <?php echo $this->BcAdminForm->control('uploader_category_id', [
                'type' => 'select',
                'options' => $uploaderCategories,
                'empty' => __d('baser_core', 'カテゴリ指定なし'),
                'id' => 'UploaderFileUploaderCategoryId' . $listId
              ]) ?>
            <?php endif ?>
            <span id="SpanUploadFile<?= h($listId) ?>">
              <?php echo $this->BcAdminForm->control('file', [
                'type' => 'file',
                'id' => 'UploaderFileFile' . $listId,
                'class' => 'uploader-file-file',
                'div' => false
              ]) ?>
				    </span>
          </div>
        <?php endif ?>
      </div>
    <?php endif ?>

		<div class="bca-data-list__sub">
			<!-- pagination -->
			<?php $this->BcBaser->element('pagination') ?>
		</div>

  </div>

  <div class="file-list-body clearfix corner5">

    <table class="bca-table-listup">

      <thead class="bca-table-listup__thead">
      <tr>
        <th class="bca-table-listup__thead-th">No</th>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'イメージ') ?></th>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'カテゴリ') ?></th>
        <th class="bca-table-listup__thead-th">
          <?php echo __d('baser_core', 'ファイル名') ?><br>
          <?php echo __d('baser_core', '説明文') ?>
        </th>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '公開状態') ?></th>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '投稿者') ?></th>
        <th class="bca-table-listup__thead-th">
          <?php echo __d('baser_core', '投稿日') ?><br>
          <?php echo __d('baser_core', '編集日') ?>
        </th>
        <?php if (!$listId): ?>
          <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'アクション') ?></th>
        <?php endif ?>
      </tr>
      </thead>
      <?php if ($uploaderFiles->count()): ?>
        <tbody>
        <?php foreach($uploaderFiles as $uploaderFile): ?>
          <?php $this->BcBaser->element('UploaderFiles/index_row_table', [
            'uploaderFile' => $uploaderFile,
            'users' => $users,
            'uploaderCategories' => $uploaderCategories
          ]) ?>
        <?php endforeach ?>
        </tbody>
      <?php else: ?>
        <tbody>
        <tr>
          <td colspan="8" class="no-data"><?php echo __d('baser_core', 'ファイルが存在しません') ?></td>
        </tr>
        </tbody>
      <?php endif ?>
    </table>

  </div>

	<div class="bca-data-list__bottom">
		<div class="bca-data-list__sub">
			<!-- pagination -->
			<?php $this->BcBaser->element('pagination') ?>
			<!-- list-num -->
			<?php $this->BcBaser->element('list_num') ?>
		</div>
	</div>

</div>
