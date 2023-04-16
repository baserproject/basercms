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
 * index
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcUploader\Model\Entity\UploaderConfig $uploaderConfigs
 * @var int $listId
 * @var bool $isAjax
 * @checked
 * @noTodo
 * @unitTest
 */
echo $this->BcBaser->i18nScript([
  'uploaderCancel' => __d('baser_core', 'キャンセル'),
  'uploaderSave' => __d('baser_core', '保存'),
  'uploaderEdit' => __d('baser_core', '編集'),
  'uploaderDelete' => __d('baser_core', '削除'),
  'uploaderAlertMessage1' => __d('baser_core', '更新に失敗しました。入力内容を見直してください。'),
  'uploaderAlertMessage2' => __d('baser_core', 'アップロードに失敗しました。ファイルサイズが大きいか、許可されていない形式です。'),
  'uploaderAlertMessage3' => __d('baser_core', 'このファイルの編集・削除はできません。'),
  'uploaderAlertMessage4' => __d('baser_core', 'サーバーでの処理に失敗しました。'),
  'uploaderConfirmMessage1' => __d('baser_core', '本当に削除してもよろしいですか？')
], ['block' => false]);
$this->BcBaser->js(['BcUploader.admin/uploader_files/index_panel.bundle']);
?>


<?php $this->BcBaser->link('ListUrl', [
  'action' => 'ajax_list',
  $listId,
  'num' => $this->getRequest()->getQuery('num')
], ['id' => 'ListUrl' . $listId, 'style' => 'display:none']) ?>


<!-- JS用設定値 -->
<div style="display:none">
  <div id="ListId"><?php echo $listId ?></div>
  <div id="LoginUserId"><?php echo \BaserCore\Utility\BcUtil::loginUser()->id ?></div>
  <div id="LoginUserGroupId"><?php echo \BaserCore\Utility\BcUtil::loginUser()->user_groups[0]->id ?></div>
  <div id="AdminPrefix" style="display:none;"><?php echo \BaserCore\Utility\BcUtil::getAdminPrefix() ?></div>
  <div id="UsePermission"><?php echo $uploaderConfigs->use_permission ?></div>
</div>


<!-- ファイルリスト -->
<div id="FileList<?php echo $listId ?>" class="file-list"></div>

<!-- 編集ダイアログ -->
<div id="EditDialog" title="<?php echo __d('baser_core', 'ファイル情報編集') ?>">
    <?php $this->BcBaser->element('UploaderFiles/form', ['listId', $listId, 'popup' => true]) ?>
</div>
