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
 * [ADMIN] ツリーのセットアップ
 * @var \BaserCore\View\BcAdminAppView $this
 * @var bool $editInIndexDisabled
 * @var bool $isUseMoveContents
 * @checked
 * @unitTest
 * @noTodo
 */

$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser_core', 'コンテンツをゴミ箱に移動してもよろしいですか？'),
  'confirmMessage2' => __d('baser_core', "選択したデータを全てゴミ箱に移動します。よろしいですか？\n※ エイリアスは直接削除します。"),
  'infoMessage1' => __d('baser_core', 'ターゲットと同じフォルダにコピー「%s」を作成しました。一覧に表示されていない場合は検索してください。'),
  'bcTreeCheck' => __d('baser_core', '確認'),
  'bcTreePublish' => __d('baser_core', '公開'),
  'bcTreeUnpublish' => __d('baser_core', '非公開'),
  'bcTreeManage' => __d('baser_core', '管理'),
  'bcTreeRename' => __d('baser_core', '名称変更'),
  'bcTreeEdit' => __d('baser_core', '編集'),
  'bcTreeCopy' => __d('baser_core', 'コピー'),
  'bcTreeDelete' => __d('baser_core', '削除'),
  'bcTreeToTrash' => __d('baser_core', 'ゴミ箱に入れる'),
  'bcTreeUndo' => __d('baser_core', '元に戻す'),
  'bcTreeEmptyTrash' => __d('baser_core', 'ゴミ箱を空にする'),
  'bcTreeConfirmMessage1' => __d('baser_core', "ゴミ箱にある項目を完全に消去してもよろしいですか？\nこの操作は取り消せません。"),
  'bcTreeConfirmToTrash' => __d('baser_core', 'コンテンツをゴミ箱に移動してもよろしいですか？'),
  'bcTreeConfirmDeleteAlias' => __d('baser_core', "エイリアスを削除してもよろしいですか？\nエイリアスはゴミ箱に入らず完全に削除されます。"),
  'bcTreeAlertMessage1' => __d('baser_core', 'エイリアスの元コンテンツを先に戻してください。'),
  'bcTreeAlertMessage2' => __d('baser_core', 'ゴミ箱を空にする事に失敗しました。'),
  'bcTreeAlertMessage3' => __d('baser_core', 'ゴミ箱から戻す事に失敗しました。'),
  'bcTreeAlertMessage4' => __d('baser_core', 'ゴミ箱に移動しようとして失敗しました。'),
  'bcTreeAlertMessage5' => __d('baser_core', '名称変更に失敗しました。'),
  'bcTreeAlertMessage6' => __d('baser_core', '追加に失敗しました。'),
  'bcTreeInfoMessage1' => __d('baser_core', 'ゴミ箱は空です'),
  'bcTreeInfoMessage2' => __d('baser_core', 'ゴミ箱より戻しました。一覧に遷移しますのでしばらくお待ち下さい。'),
  'bcTreeAliasTitle' => __d('baser_core', '%s のエイリアス'),
  'bcTreeUnNamedTitle' => __d('baser_core', '名称未設定'),
  'bcTreeNewTitle' => __d('baser_core', '新しい %s'),
]);
$this->BcBaser->js(['vendor/jquery.jstree-3.3.8/jstree.min'], false);
$this->BcBaser->js('admin/contents/index.bundle', false, [
  'id' => 'AdminContentsIndexScript',
  'data-isAdmin' => \BaserCore\Utility\BcUtil::isAdminUser(),
  'data-isUseMoveContents' => $isUseMoveContents,
  'data-adminPrefix' => \BaserCore\Utility\BcUtil::getAdminPrefix(),
  'data-baserCorePrefix' => \Cake\Utility\Inflector::underscore(\BaserCore\Utility\BcUtil::getBaserCorePrefix()),
  'data-editInIndexDisabled' => $editInIndexDisabled
]);
$this->BcBaser->css('../js/vendor/jquery.jstree-3.3.8/themes/proton/style.min', false);
echo $this->BcAdminForm->control('BcManageContent', ['type' => 'hidden', 'value' => $this->BcContents->getJsonItems()]);
