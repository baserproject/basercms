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
 * メールフォーム送信完了ページ
 *
 * 呼出箇所：メールフォーム
 *
 * @var \BcMail\View\MailFrontAppView $this
 * @var array $mailContent メールコンテンツデータ
 * @checked
 * @noTodo
 * @unitTest
 */

use Cake\Core\Configure;

if (Configure::read('debug') == 0 && $mailContent->redirect_url) {
  $this->BcBaser->meta(['http-equiv' => 'Refresh'], null, ['content' => '5;url=' . $mailContent->redirect_url, 'block' => true]);
}
?>


<h2 class="bs-mail-title"><?php $this->BcBaser->contentsTitle() ?></h2>

<h3 class="bs-mail-title-sub"><?php echo __d('baser_core', 'メール送信完了') ?></h3>

<div class="bs-mail-form">
  <?php if ($this->BcBaser->mailFormThanksExists()): ?>
    <?php $this->BcBaser->mailFormThanks() ?>
  <?php else: ?>
    <p><?php echo __d('baser_core', 'お問い合わせ頂きありがとうございました。') ?>
      <?php echo __d('baser_core', '確認次第、ご連絡させて頂きます。') ?></p>
  <?php endif ?>
  <?php if (!$this->BcBaser->isDebug() && $mailContent->redirect_url): ?>
    <p>※<?php echo __d('baser_core', '{0} 秒後にトップページへ自動的に移動します。', 5) ?></p>
    <p><a href="<?php echo $mailContent->redirect_url; ?>"><?php echo __d('baser_core', '移動しない場合はコチラをクリックしてください。') ?></a></p>
  <?php endif; ?>
</div>
