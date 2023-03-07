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
 * ブログコメント一覧
 * 呼出箇所：ブログ記事詳細
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogContent $blogContent ブログコンテンツデータ
 */
if (!$blogContent->comment_use) return;
$captchaId = mt_rand(0, 99999999);
$this->BcBaser->i18nScript([
  'alertMessageName' => __d('baser_core', 'お名前を入力してください'),
  'alertMessageComment' => __d('baser_core', 'コメントを入力してください'),
  'alertMessageAuthImage' => __d('baser_core', '画像の文字を入力してください'),
  'alertMessageAuthComplete' => __d('baser_core', '送信が完了しました。送信された内容は確認後公開させて頂きます。'),
  'alertMessageError' => __d('baser_core', 'コメントの送信に失敗しました。入力内容を見なおしてください。'),
]);
$this->BcBaser->js('BcBlog.blog_comment.bundle.js', true, [
  'defer' => 'defer',
  'id' => 'BlogCommentsScripts',
  'data-captchaId' => $captchaId,
  'data-commentApprove' => $blogContent->comment_approve,
  'data-authCaptcha' => $blogContent->auth_captcha,
  'data-authCaptchaImageBaseUrl' => $this->BcBaser->getUrl([
    'plugin' => 'BcBlog',
    'controller' => 'Blog',
    'action' => 'captcha'
  ])
]);
?>


<div class="bs-blog-comment">

  <h4 class="bs-blog-comment__head"><?php echo __d('baser_core', 'この記事へのコメント') ?></h4>

  <div id="BlogCommentList" class="bs-blog-comment__list">
    <?php if (!empty($post->blog_comments)): ?>
      <?php foreach($post->blog_comments as $comment): ?>
        <!-- /Elements/blog_comment.php -->
        <?php $this->BcBaser->element('blog_comment', ['blogComment' => $comment]) ?>
      <?php endforeach ?>
    <?php else: ?>
      <p>コメントはまだありません。</p>
    <?php endif ?>
  </div>

  <h4 class="bs-blog-comment__head"><?php echo __d('baser_core', 'コメントを送る') ?></h4>

  <?php echo $this->BcForm->create(null, ['id' => 'BlogCommentAddForm']) ?>
  <?php echo $this->BcForm->control('captcha_id', ['type' => 'hidden', 'value' => $captchaId]) ?>
  <?php echo $this->BcForm->control('blog_content_id', ['type' => 'hidden', 'value' => $blogContent->id]) ?>
  <?php echo $this->BcForm->control('blog_post_id', ['type' => 'hidden', 'value' => $post->id]) ?>

  <table class="bs-blog-comment__form">
    <tr>
      <th><?php echo $this->BcForm->label('name', __d('baser_core', 'お名前') . ' / ' . __d('baser_core', 'ニックネーム')) ?>
        <span class="required"><?php echo __d('baser_core', '必須') ?></span></th>
      <td><?php echo $this->BcForm->control('name', ['type' => 'text', 'required' => false]) ?></td>
    </tr>
    <tr>
      <th><?php echo $this->BcForm->label('email', __d('baser_core', 'メールアドレス')) ?>
        <span class="required"><?php echo __d('baser_core', '必須') ?></span></th>
      <td>
        <?php echo $this->BcForm->control('email', ['type' => 'text', 'size' => 30, 'required' => false]) ?>
        &nbsp;
        <br><small>※ <?php echo __d('baser_core', 'メールアドレスは公開されません') ?></small>
      </td>
    </tr>
    <tr>
      <th><?php echo $this->BcForm->label('url', 'URL') ?>
        <span class="normal"><?php echo __d('baser_core', '任意') ?></span></th>
      <td><?php echo $this->BcForm->control('url', ['type' => 'text', 'size' => 30, 'required' => false]) ?></td>
    </tr>
    <tr>
      <th><?php echo $this->BcForm->label('message', __d('baser_core', 'コメント')) ?>
        <span class="required"><?php echo __d('baser_core', '必須') ?></span></th>
      <td><?php echo $this->BcForm->control('message', ['type' => 'textarea', 'rows' => 10, 'cols' => 52, 'required' => false]) ?></td>
    </tr>
  </table>

  <?php if ($blogContent->auth_captcha): ?>
    <div class="bs-blog-comment__auth-captcha">
      <img
        src=""
        alt="<?php echo __d('baser_core', '認証画象') ?>"
        class="auth-captcha-image"
        id="AuthCaptchaImage"
        style="display:none;margin-left:3px;"
      >
      <?php $this->BcBaser->img('admin/captcha_loader.gif', [
        'alt' => 'Loading...',
        'class' => 'auth-captcha-image',
        'id' => 'CaptchaLoader',
      ]) ?>
      <?php echo $this->BcForm->control('auth_captcha', ['type' => 'text', 'size' => 25]) ?>
      &nbsp;<?php echo __d('baser_core', '画像の文字を入力してください') ?>
    </div>
  <?php endif ?>

  <div class="bs-blog-comment__submit">
    <?php echo $this->BcForm->button(__d('baser_core', '送信する'), ['id' => 'BlogCommentAddButton', 'class' => 'button']) ?>
  </div>

  <?php echo $this->BcForm->end() ?>

  <div id="ResultMessage" class="message" style="display:none;text-align:center">&nbsp;</div>

</div>

