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
 * @var string $installMessage
 * @var \Cake\ORM\ResultSet $uploaderFiles
 * @var int $listId
 * @checked
 * @noTodo
 * @unitTest
 */
// IE文字化け対策
header('Content-type: text/html; charset=utf-8');
$users = $this->BcAdminForm->getControlSource("UploaderFiles.user_id");
$uploaderCategories = $this->BcAdminForm->getControlSource("UploaderFiles.uploader_category_id");
?>


<div id="DivPanelList">

  <div class="bca-list-head">
    <!-- form -->
    <?php if (!$installMessage): ?>
      <div id="UploaderForm" class="clearfix">
        <div>
          <?php echo $this->BcAdminForm->label('uploader_category_id', __d('baser_core', 'アップロード')) ?>
          &nbsp;
          <?php if ($uploaderCategories): ?>
            <?php echo $this->BcAdminForm->control('uploader_category_id', [
              'type' => 'select',
              'options' => $uploaderCategories,
              'empty' => __d('baser_core', 'カテゴリ指定なし'),
              'id' => 'UploaderFileUploaderCategoryId' . $listId
            ]) ?>&nbsp;
          <?php endif ?>
          <span id="SpanUploadFile<?php echo $listId ?>">
				<?php echo $this->BcAdminForm->control('file', [
          'type' => 'file',
          'id' => 'UploaderFileFile' . $listId,
          'class' => 'uploader-file-file',
          'div' => false
        ]) ?>
			</span>
        </div>
      </div>
    <?php else: ?>
      <p style="color:#C00;font-weight:bold"><?php echo $installMessage ?></p>
    <?php endif ?>

    <?php $this->BcBaser->element('pagination') ?>

  </div>

  <div class="file-list-body clearfix corner5 bca-file-list">
    <?php if ($uploaderFiles->count()): ?>
      <?php foreach($uploaderFiles as $uploaderFile): ?>
        <?php $this->BcBaser->element('UploaderFiles/index_row_panel', ['uploaderFile' => $uploaderFile, 'users' => $users]) ?>
      <?php endforeach ?>
    <?php else: ?>
      <p class="no-data bca-file-list__no-data"><?php echo __d('baser_core', 'ファイルが存在しません') ?></p>
    <?php endif ?>
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
