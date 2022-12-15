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
 * [ADMIN] メールコンテンツ フォーム
 * @var \BcMail\View\MailAdminAppView $this
 * @var \BcMail\Model\Entity\MailContent $mailContent
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser', 'メールフォーム設定を保存して、レイアウトテンプレート %s の編集画面に移動します。よろしいですか？'),
  'confirmMessage2' => __d('baser', 'メールフォーム設定を保存して、メールフォームテンプレート %s の編集画面に移動します。よろしいですか？'),
  'confirmMessage3' => __d('baser', 'メールフォーム設定を保存して、送信メールテンプレート %s の編集画面に移動します。よろしいですか？')
]);
$this->BcBaser->js('BcMail.admin/mail_contents/form.bundle', false);
$this->BcAdmin->setTitle(__d('baser', 'メールフォーム設定編集'));
$this->BcAdmin->setHelp('mail_contents_form');
?>


<?php echo $this->BcAdminForm->create($mailContent, ['novalidate' => true, 'id' => 'MailContentAdminEditForm']) ?>
<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('MailContents/form') ?>

<!-- button -->
<div class="submit">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'id' => 'BtnSave',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
