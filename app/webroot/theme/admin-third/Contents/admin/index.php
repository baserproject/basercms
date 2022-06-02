<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] 統合コンテンツ一覧
 * @var BcAppView $this
 * @var bool $editInIndexDisabled
 */
$currentUser = BcUtil::loginUser('admin');
$this->BcBaser->js('admin/vendors/jquery.jstree-3.3.8/jstree.min', false);
$this->BcBaser->i18nScript([
	'confirmMessage1' => __d('baser', 'コンテンツをゴミ箱に移動してもよろしいですか？'),
	'confirmMessage2' => __d('baser', "選択したデータを全てゴミ箱に移動します。よろしいですか？\n※ エイリアスは直接削除します。"),
	'infoMessage1' => __d('baser', 'ターゲットと同じフォルダにコピー「%s」を作成しました。一覧に表示されていない場合は検索してください。'),
	'bcTreeCheck' => __d('baser', '確認'),
	'bcTreePublish' => __d('baser', '公開'),
	'bcTreeUnpublish' => __d('baser', '非公開'),
	'bcTreeManage' => __d('baser', '管理'),
	'bcTreeRename' => __d('baser', '名称変更'),
	'bcTreeEdit' => __d('baser', '編集'),
	'bcTreeCopy' => __d('baser', 'コピー'),
	'bcTreeDelete' => __d('baser', '削除'),
	'bcTreeToTrash' => __d('baser', 'ゴミ箱に入れる'),
	'bcTreeEmptyTrash' => __d('baser', 'ゴミ箱を空にする'),
	'bcTreeUndo' => __d('baser', '戻す'),
	'bcTreeConfirmMessage1' => __d('baser', "ゴミ箱にある項目を完全に消去してもよろしいですか？\nこの操作は取り消せません。"),
	'bcTreeConfirmToTrash' => __d('baser', 'コンテンツをゴミ箱に移動してもよろしいですか？'),
	'bcTreeConfirmDeleteAlias' => __d('baser', "エイリアスを削除してもよろしいですか？\nエイリアスはゴミ箱に入らず完全に削除されます。"),
	'bcTreeAlertMessage1' => __d('baser', 'エイリアスの元コンテンツを先に戻してください。'),
	'bcTreeAlertMessage2' => __d('baser', 'ゴミ箱を空にする事に失敗しました。'),
	'bcTreeAlertMessage3' => __d('baser', 'ゴミ箱から戻す事に失敗しました。'),
	'bcTreeAlertMessage4' => __d('baser', 'ゴミ箱に移動しようとして失敗しました。'),
	'bcTreeAlertMessage5' => __d('baser', '名称変更に失敗しました。'),
	'bcTreeAlertMessage6' => __d('baser', '追加に失敗しました。'),
	'bcTreeInfoMessage1' => __d('baser', 'ゴミ箱は空です'),
	'bcTreeInfoMessage2' => __d('baser', 'ゴミ箱より戻しました。一覧に遷移しますのでしばらくお待ち下さい。'),
	'bcTreeCopyTitle' => __d('baser', '%s のコピー'),
	'bcTreeAliasTitle' => __d('baser', '%s のエイリアス'),
	'bcTreeUnNamedTitle' => __d('baser', '名称未設定'),
	'bcTreeNewTitle' => __d('baser', '新しい %s'),

]);
$this->BcBaser->js('admin/contents/index', false, [
	'id' => 'AdminContentsIndexScript',
	'data-isAdmin' => BcUtil::isAdminUser(),
	'data-isUseMoveContents' => (bool)$currentUser['UserGroup']['use_move_contents'],
	'data-adminPrefix' => BcUtil::getAdminPrefix(),
	'data-editInIndexDisabled' => (bool)$editInIndexDisabled
]);
$this->BcBaser->js('admin/libs/jquery.bcTree', false);
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/jquery.baser_ajax_batch',
	'admin/libs/baser_ajax_data_list_config',
	'admin/libs/baser_ajax_batch_config'
]);
echo $this->BcForm->input('BcManageContent', ['type' => 'hidden', 'value' => $this->BcContents->getJsonSettings()]);
?>


<script type="text/javascript">

</script>

<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>

<?php $this->BcBaser->element('contents/index_view_setting') ?>

<div id="DataList" class="bca-data-list">&nbsp;</div>


