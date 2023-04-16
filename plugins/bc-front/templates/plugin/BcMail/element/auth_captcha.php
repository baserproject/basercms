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
 * 画像認証
 *
 * MailformHelper::authCaptcha() より呼び出される
 *
 * @var \BcMail\View\MailFrontAppView $this
 * @var string $fieldName
 * @var bool $freezed
 * @var string $captchaUrl
 * @var array $options
 * @var string $captchaId
 */
?>


<?php if (!$freezed): ?>
  <div class="bs-mail-form-auth-captcha">
    <div>
      <?php $this->BcBaser->img($captchaUrl, ['alt' => __d('baser_core', '認証画像'), 'class' => $options['class']]) ?>
      <?php echo h($options['separate']) ?>
      <?php echo $this->Mailform->control($fieldName, ['type' => 'text', 'size' => 25]) ?>
      <?php echo $this->Mailform->control('captcha_id', ['type' => 'hidden', 'value' => $captchaId]) ?>
    </div>
    <div><?php echo __d('baser_core', '画像の文字を入力してください') ?></div>
    <?php echo $this->Mailform->error($fieldName) ?>
  </div>
<?php else: ?>
  <?php echo $this->Mailform->hidden($fieldName) ?>
  <?php echo $this->Mailform->hidden('captcha_id') ?>
<?php endif ?>
