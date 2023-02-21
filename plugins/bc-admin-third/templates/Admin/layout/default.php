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

use BaserCore\Utility\BcUtil;
use BaserCore\View\BcAdminAppView;
use Cake\Utility\Inflector;

/**
 * @var BcAdminAppView $this
 * @var string $title
 * @checked
 * @noTodo
 * @unitTest
 */

$request = $this->getRequest();
$attributes = $request->getAttributes();
$base = $attributes['base'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex,nofollow"/>
  <title><?= h($this->fetch('title')) ?></title>
  <?= $this->fetch('meta') ?>
  <?php $this->BcBaser->css([
    'vendor/bootstrap-4.1.3/bootstrap',
    'vendor/jquery-ui/jquery-ui.min',
    'vendor/jquery.timepicker',
    '../js/vendor/jquery-contextMenu-2.2.0/jquery.contextMenu.min.css',
    'admin/style'
  ]) ?>
  <?= $this->fetch('css') ?>
  <?= $this->BcBaser->declarationI18n() ?>
  <?= $this->BcBaser->i18nScript([
    'commonCancel' => __d('baser', 'キャンセル'),
    'commonSave' => __d('baser', '保存'),
    'commonExecCompletedMessage' => __d('baser', '処理が完了しました。'),
    'commonSaveFailedMessage' => __d('baser', '保存に失敗しました。'),
    'commonExecFailedMessage' => __d('baser', '処理に失敗しました。'),
    'commonBatchExecFailedMessage' => __d('baser', '一括処理に失敗しました。'),
    'commonGetDataFailedMessage' => __d('baser', 'データ取得に失敗しました。'),
    'commonSortSaveFailedMessage' => __d('baser', '並び替えの保存に失敗しました。'),
    'commonSortSaveConfirmMessage' => __d('baser', 'コンテンツを移動します。よろしいですか？'),
    'commonNotFoundProgramMessage' => __d('baser', '送信先のプログラムが見つかりません。'),
    'commonSelectDataFailedMessage' => __d('baser', 'データが選択されていません。'),
    'commonConfirmDeleteMessage' => __d('baser', '本当に削除してもよろしいですか？'),
    'commonConfirmHardDeleteMessage' => __d('baser', "このデータを本当に削除してもよろしいですか？\n※ 削除したデータは元に戻すことができません。"),
    'commonPublishFailedMessage' => __d('baser', '公開処理に失敗しました。'),
    'commonChangePublishFailedMessage' => __d('baser', '公開状態の変更に失敗しました。'),
    'commonUnpublishFailedMessage' => __d('baser', '非公開処理に失敗しました。'),
    'commonCopyFailedMessage' => __d('baser', 'コピーに失敗しました。'),
    'commonDeleteFailedMessage' => __d('baser', '削除に失敗しました。'),
    'batchConfirmMessage' => __d('baser', '選択したデータの一括処理を行います。よろしいですか？'),
    'batchListConfirmDeleteMessage' => __d('baser', "選択したデータを全て削除します。よろしいですか？\n※ 削除したデータは元に戻すことができません。"),
    'batchListConfirmPublishMessage' => __d('baser', '選択したデータを全て公開状態に変更します。よろしいですか？'),
    'batchListConfirmUnpublishMessage' => __d('baser', '選択したデータを全て非公開状態に変更します。よろしいですか？'),
    'bcConfirmTitle1' => __d('baser', 'ダイアログ'),
    'bcConfirmAlertMessage1' => __d('baser', 'メッセージを指定してください。'),
    'bcConfirmAlertMessage2' => __d('baser', 'コールバック処理が登録されていません。')
  ], ['block' => false]) ?>
  <?php $this->BcBaser->js([
    'admin/vendor.bundle',
    'vendor/vue.min',
    'vendor/jquery-3.5.1.min',
    'vendor/jquery.bt.min',
    'vendor/jquery-contextMenu-2.2.0/jquery.contextMenu.min',
    'vendor/jquery-ui-1.13.0.min',
    'vendor/i18n/ui.datepicker-ja',
    'vendor/jquery.validate.1.19.3.min',
    'vendor/jquery.validate_ja',
    'vendor/jquery.form-2.94',
    'vendor/jquery.timepicker',
    'vendor/bootstrap-4.1.3/bootstrap.bundle.min'
  ]) ?>
  <?php $this->BcBaser->js('admin/common.bundle', true, [
    'id' => 'AdminScript',
    'data-baseUrl' => h($base),
    'data-adminPrefix' => BcUtil::getAdminPrefix(),
    'data-baserCorePrefix' => Inflector::underscore(BcUtil::getBaserCorePrefix()),
    'data-ajaxLoaderPath' => $this->Html->Url->image('admin/ajax-loader.gif'),
    'data-ajaxLoaderSmallPath' => $this->Html->Url->image('admin/ajax-loader-s.gif'),
    'data-frontFullUrl' => (!empty($publishLink))? h($publishLink) : '',
  ]) ?>
  <?php $this->BcBaser->js([
    'admin/startup.bundle'
  ], true, ['defer' => true]) ?>
  <?php $this->BcBaser->scripts() ?>
</head>

<body id="<?php $this->BcBaser->contentsName(true) ?>" class="normal">

<div class="bca-data">
  <div id="Waiting" class="waiting-box bca-waiting-box" hidden>
    <div class="corner10">
      <?php echo $this->Html->image('admin/ajax-loader.gif') ?>
    </div>
  </div>
</div>

<div id="Page" class="bca-app">

  <?php $this->BcBaser->element('header') ?>

  <div id="Wrap" class="bca-container">

    <?php if ($this->BcAdmin->isAvailableSideBar()): ?>
      <?php $this->BcBaser->element('sidebar') ?>
    <?php endif ?>

    <main class="bca-main">

      <article id="ContentsBody" class="contents-body bca-main__body">

        <div class="bca-main__header">

          <h1 class="bca-main__header-title"><?php $this->BcAdmin->title() ?></h1>

          <div class="bca-main__header-actions">
            <?php $this->BcBaser->element('main_body_header_links'); ?>
          </div>

          <div class="bca-main__header-menu">
            <?php $this->BcAdmin->contentsMenu() ?>
          </div>

        </div>

        <?php $this->BcAdmin->help() ?>

        <?php $this->BcAdmin->search() ?>

        <?php $this->BcBaser->flash() ?>

        <div id="BcMessageBox">
          <div id="BcSystemMessage" class="notice-message"></div>
        </div>

        <div class="bca-main__contents clearfix">
          <?= $this->fetch('content') ?>
        </div>

        <!-- / bca-main__body --></article>

      <!-- / .bca-main --></main>

    <!-- / #Wrap --></div>

  <?php $this->BcBaser->element('footer') ?>

  <!-- / #Page --></div>

</body>

</html>
