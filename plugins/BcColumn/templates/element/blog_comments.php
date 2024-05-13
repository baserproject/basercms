<?php
/**
 * ブログコメント一覧
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogContent $blogContent ブログコンテンツデータ
 */
if (!$blogContent->comment_use) return;
$captchaId = mt_rand(0, 99999999);
$this->BcBaser->i18nScript([
  'alertMessageName' => __d('baser_core', 'お名前を入力してください'),
  'alertMessageEmail' => __d('baser_core', 'メールアドレスを入力してください'),
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


  <div id="BlogComment">
    <div id="BlogCommentList">
      <?php if (!empty($post['BlogComment'])): ?>
        <h3>コメント一覧</h3>
        <?php foreach($post['BlogComment'] as $comment): ?>
          <?php $this->BcBaser->element('blog_comment', ['dbData' => $comment]) ?>
        <?php endforeach ?>
      <?php endif ?>
    </div>

    <div id="CommentForm">
      <h3>コメント送信フォーム</h3>
      <?php echo $this->BcForm->create(null, ['id' => 'BlogCommentAddForm']) ?>
      <?php echo $this->BcForm->control('blog_content_id', ['type' => 'hidden', 'value' => $blogContent->id]) ?>
      <?php echo $this->BcForm->control('captcha_id', ['type' => 'hidden', 'value' => $captchaId]) ?>
      <?php echo $this->BcForm->control('blog_post_id', ['type' => 'hidden', 'value' => $post->id]) ?>

      <table class="row-table-01">
        <tbody>
        <tr>
          <th><?php echo $this->BcForm->label('name', __d('baser_core', 'お名前') . '・' . __d('baser_core', 'ニックネーム')) ?>
            <span style="color:red">＊</span></th>
          <td><?php echo $this->BcForm->control('name', ['type' => 'text', 'class' => 'form-m']) ?></td>
        </tr>
        <tr>
          <th><?php echo $this->BcForm->label('email', __d('baser_core', 'Eメール')) ?><span style="color:red">＊</span></th>
          <td>
            <?php echo $this->BcForm->control('email', ['type' => 'text', 'size' => 30, 'class' => 'form-m']) ?>
            <br>
            <small>※ <?php echo __d('baser_core', 'Eメールは公開されません') ?></small>
          </td>
        </tr>
        <tr>
          <th><?php echo $this->BcForm->label('url', 'URL') ?></th>
          <td><?php echo $this->BcForm->control('url', ['type' => 'text', 'size' => 30, 'class' => 'form-l']) ?></td>
        </tr>
        <tr>
          <th><?php echo $this->BcForm->label('message', __d('baser_core', 'コメント')) ?><span style="color:red">＊</span></th>
          <td><?php echo $this->BcForm->control('message', ['type' => 'textarea', 'rows' => 10, 'cols' => 52, 'class' => 'form-l']) ?></td>
        </tr>
        </tbody>
      </table>

      <?php if ($blogContent->auth_captcha): ?>
        <div class="auth-captcha clearfix">
          <img src="" alt="<?php echo __d('baser_core', '認証画象') ?>" class="auth-captcha-image" id="AuthCaptchaImage" style="display:none"/>
          <?php $this->BcBaser->img('admin/captcha_loader.gif', ['alt' => 'Loading...', 'class' => 'auth-captcha-image', 'id' => 'CaptchaLoader']) ?>
          <?php echo $this->BcForm->control('auth_captcha', ['type' => 'text', 'size' => 25]) ?><br/>
          &nbsp;<?php echo __d('baser_core', '画像の文字を入力してください') ?><br/>
        </div>
      <?php endif ?>

      <div style="text-align: center">
      <?php echo $this->BcForm->button(__d('baser_core', '送信する'), ['id' => 'BlogCommentAddButton', 'class' => 'bs-button']) ?>
      </div>

      <?php echo $this->BcForm->end() ?>

      <div id="ResultMessage" class="message" style="display:none;text-align:center">&nbsp;</div>
    </div>
  </div>
