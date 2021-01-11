<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.View
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */
/**
 * index
 *
 * @var \BcAppView $this
 */
$this->BcBaser->i18nScript([
	'uploaderCancel' => __d('baser', 'キャンセル'),
	'uploaderSave' => __d('baser', '保存'),
	'uploaderEdit' => __d('baser', '編集'),
	'uploaderDelete' => __d('baser', '削除'),
	'uploaderAlertMessage1' => __d('baser', '更新に失敗しました。入力内容を見直してください。'),
	'uploaderAlertMessage2' => __d('baser', 'アップロードに失敗しました。ファイルサイズが大きいか、許可されていない形式です。'),
	'uploaderAlertMessage3' => __d('baser', 'このファイルの編集・削除はできません。'),
	'uploaderAlertMessage4' => __d('baser', 'サーバーでの処理に失敗しました。'),
	'uploaderConfirmMessage1' => __d('baser', '本当に削除してもよろしいですか？')
]);
$this->BcBaser->js(['Uploader.admin/uploader_files/uploader_list']);
if (!isset($listId)) {
	$listId = '';
}
?>


<?php $this->BcBaser->link('ListUrl', ['action' => 'ajax_list', $listId, 'num' => $this->passedArgs['num']], ['id' => 'ListUrl' . $listId, 'style' => 'display:none']) ?>


<!-- JS用設定値 -->
<div style="display:none">
	<div id="ListId"><?php echo $listId ?></div>
	<div
		id="UploaderImageSettings"><?php if (isset($imageSettings)) : ?><?php echo $this->Js->object($imageSettings) ?><?php endif ?></div>
	<div id="LoginUserId"><?php echo $user['id'] ?></div>
	<div id="LoginUserGroupId"><?php echo $user['user_group_id'] ?></div>
	<div id="AdminPrefix" style="display:none;"><?php echo Configure::read('Routing.prefixes.0'); ?></div>
	<div id="UsePermission"><?php echo $uploaderConfigs['use_permission'] ?></div>
</div>


<!-- ファイルリスト -->
<div id="FileList<?php echo $listId ?>" class="corner5 file-list"></div>


<!-- list-num -->
<?php if (empty($this->params['isAjax'])): ?>
	<?php $this->BcBaser->element('list_num') ?>
<?php endif ?>

<!-- 編集ダイアログ -->
<div id="EditDialog" title="<?php echo __d('baser', 'ファイル情報編集') ?>">
	<?php $this->BcBaser->element('uploader_files/form', ['listId', $listId, 'popup' => true]) ?>
</div>
