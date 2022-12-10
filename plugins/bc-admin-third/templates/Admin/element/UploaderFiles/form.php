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
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
if (!isset($uploaderCategories))$uploaderCategories = $this->BcAdminForm->getControlSource("uploader_category_id");
if (!isset($listId)) $listId = '';
if (empty($popup)) $users = $this->BcAdminForm->getControlSource("user_id");
?>


<?php if (empty($popup)): ?>
  <?php $url = $this->BcBaser->getUri($this->Uploader->getFileUrl($this->request->getData('name'))) ?>
  <div id="AdminPrefix" style="display:none;"><?php echo \BaserCore\Utility\BcUtil::getAdminPrefix() ?></div>
  <script type="text/javascript">
    $(function () {
      var name = $("#UploaderFileName").val();
      var imgUrl = $.baseUrl + '/' + $("#AdminPrefix").html() + '/uploader/uploader_files/ajax_image/' + name + '/midium';
      $.get(imgUrl, function (res) {
        $("#UploaderFileImage").html(res);
      });
    });
  </script>

  <div class="bca-section bca-section__post-top">
  <span class="bca-post__url">
	  <a href="<?php echo $url ?>"
	    class="bca-text-url"
	    target="_blank"
	    data-toggle="tooltip"
	    data-placement="top"
	    title="" data-original-title="<?php echo __d('baser', '公開URLを開きます') ?>">
	  <i class="bca-icon--globe"></i><?php echo $url ?></a>
  </span>
  </div>

<?php endif ?>


<?php if (!empty($popup)): ?>
  <?php echo $this->BcAdminForm->create(null, [
    'url' => ['action' => 'edit'],
    'id' => 'UploaderFileEditForm' . $listId
  ]) ?>
<?php else: ?>
  <?php echo $this->BcAdminForm->create(null, [
    'url' => ['action' => 'edit', $this->request->getData('id'), $listId],
    'id' => 'UploaderFileEditForm' . $listId,
    'type' => 'file'
  ]) ?>
<?php endif ?>


<table class="form-table bca-form-table">
  <tr>
    <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('id', 'No') ?></th>
    <td class="col-input bca-form-table__input">
      <?php if (empty($popup)): ?>
        <?php echo $this->BcAdminForm->getSourceValue('id') ?>
        <?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>
      <?php else: ?>
        <?php echo $this->BcAdminForm->text('id', [
          'size' => 30, 'maxlength' => 255,
          'readonly' => 'readonly',
          'id' => 'UploaderFileId' . $listId,
          'class' => 'uploader-file-id'
        ]) ?>&nbsp;
      <?php endif ?>
    </td>
    <?php if (!empty($popup)): ?>
      <td rowspan="5" id="UploaderFileImage<?php echo $listId ?>"
          class="uploader-file-image"><?php echo $this->Html->image('admin/ajax-loader.gif') ?></td>
    <?php endif ?>
  </tr>
  <?php if (empty($popup)): ?>
    <tr>
      <th class="bca-form-table__label">
        <?php echo $this->BcAdminForm->label('name', __d('baser', 'アップロードファイル')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', [
          'type' => 'file',
          'delCheck' => false,
          'imgsize' => 'midium',
          'force' => 'true'
        ]) ?></td>
    </tr>
  <?php else: ?>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('name', __d('baser', 'ファイル名')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', [
          'type' => 'text', 'size' => 30,
          'maxlength' => 255,
          'readonly' => 'readonly',
          'id' => 'UploaderFileName' . $listId,
          'class' => 'uploader-file-name'
        ]) ?>
        <?php echo $this->BcAdminForm->error('name', __d('baser', 'ファイル名を入力して下さい')) ?>&nbsp;
      </td>
    </tr>
  <?php endif ?>
  <tr>
    <th class="col-head bca-form-table__label">
      <?php echo $this->BcAdminForm->label('alt', __d('baser', '説明文')) ?>
    </th>
    <td class="col-input bca-form-table__input">
      <?php echo $this->BcAdminForm->control('alt', [
        'type' => 'text',
        'size' => 51, 'maxlength' => 255,
        'id' => 'UploaderFileAlt' . $listId,
        'class' => 'uploader-file-alt bca-textbox__input'
      ]) ?>
      &nbsp;
    </td>
  </tr>
  <tr>
    <th class="col-head bca-form-table__label">
      <?php echo $this->BcAdminForm->label('publish_begin_date', __d('baser', '公開期間')) ?>
    </th>
    <td class="col-input bca-form-table__input">
      <?php echo $this->BcAdminForm->control('publish_begin', [
        'type' => 'dateTimePicker',
        'size' => 12,
        'maxlength' => 10,
        'dateLabel' => ['text' => __d('baser', '開始日付')],
        'timeLabel' => ['text' => __d('baser', '開始時間')]
      ]) ?>
      &nbsp;〜&nbsp;
      <?php echo $this->BcAdminForm->control('publish_end', [
        'type' => 'dateTimePicker',
        'size' => 12,
        'maxlength' => 10,
        'dateLabel' => ['text' => __d('baser', '終了日付')],
        'timeLabel' => ['text' => __d('baser', '終了時間')]
      ]) ?>
      <?php echo $this->BcAdminForm->error('publish_begin') ?>
      <?php echo $this->BcAdminForm->error('publish_end') ?>
    </td>
  </tr>
  <?php if ($uploaderCategories): ?>
    <tr>
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('uploader_category_id', __d('baser', 'カテゴリ')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('uploader_category_id', [
          'type' => 'select',
          'options' => $uploaderCategories,
          'empty' => __d('baser', '指定なし'),
          'id' => '_UploaderFileUploaderCategoryId' . $listId
        ]) ?>
      </td>
    </tr>
  <?php endif ?>
  <tr>
    <th class="col-head bca-form-table__label"><?php echo __d('baser', '保存者') ?></th>
    <td class="col-input bca-form-table__input">
			<span id="UploaderFileUserName<?php echo $listId ?>">
			<?php if (empty($popup)): ?>
        <?php echo $this->BcText->arrayValue($this->request->getData('user_id'), $users) ?>
      <?Php endif ?>
			</span>
      <?php echo $this->BcAdminForm->control('user_id', [
        'type' => 'hidden', 'id' =>
          'UploaderFileUserId' . $listId
      ]) ?>
    </td>
  </tr>
</table>


<?php if (empty($popup)): ?>
  <div class="submit bca-actions">
    <div class="bca-actions__main">
      <?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
        'div' => false,
        'class' => 'bca-btn',
        'data-bca-btn-type' => 'add',
        'data-bca-btn-size' => 'xl',
        'id' => 'BtnSave'
      ]) ?>
    </div>
    <div class="bca-actions__sub">
      <?php $this->BcBaser->link(__d('baser', '削除'),
        ['action' => 'delete', $this->BcAdminForm->getSourceValue('id')],
        ['class' => 'bca-submit-token button bca-btn', 'data-bca-btn-type' => 'delete'],
        sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcAdminForm->getSourceValue('name')),
        false); ?>
    </div>
  </div>
<?php endif; ?>

<?php echo $this->BcAdminForm->end() ?>
