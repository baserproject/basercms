<?php
/**
 * ブログコメント一覧
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


<?php if ($blogContent->comment_use): ?>
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
      <?php echo $this->BcForm->create(null, ['url' => '/blog/blog_comments/add/' . $blogContent->id . '/' . $post->id, 'id' => 'BlogCommentAddForm']) ?>
      <?php echo $this->BcForm->control('BlogComment.captcha_id', ['type' => 'hidden', 'value' => $captchaId]) ?>

      <table class="row-table-01">
        <tbody>
        <tr>
          <th><?php echo $this->BcForm->label('BlogComment.name', __('お名前') . '・' . __('ニックネーム')) ?>
            <span style="color:red">＊</span></th>
          <td><?php echo $this->BcForm->control('BlogComment.name', ['type' => 'text', 'class' => 'form-m']) ?></td>
        </tr>
        <tr>
          <th><?php echo $this->BcForm->label('BlogComment.email', __('Eメール')) ?><span style="color:red">＊</span></th>
          <td>
            <?php echo $this->BcForm->control('BlogComment.email', ['type' => 'text', 'size' => 30, 'class' => 'form-m']) ?>
            <br>
            <small>※ <?php echo __('Eメールは公開されません') ?></small>
          </td>
        </tr>
        <tr>
          <th><?php echo $this->BcForm->label('BlogComment.url', 'URL') ?></th>
          <td><?php echo $this->BcForm->control('BlogComment.url', ['type' => 'text', 'size' => 30, 'class' => 'form-l']) ?></td>
        </tr>
        <tr>
          <th><?php echo $this->BcForm->label('BlogComment.message', __('コメント')) ?><span style="color:red">＊</span></th>
          <td><?php echo $this->BcForm->control('BlogComment.message', ['type' => 'textarea', 'rows' => 10, 'cols' => 52, 'class' => 'form-l']) ?></td>
        </tr>
        </tbody>
      </table>

      <?php if ($blogContent->auth_captcha): ?>
        <div class="auth-captcha clearfix">
          <img src="" alt="<?php echo __('認証画象') ?>" class="auth-captcha-image" id="AuthCaptchaImage" style="display:none"/>
          <?php $this->BcBaser->img('admin/captcha_loader.gif', ['alt' => 'Loading...', 'class' => 'auth-captcha-image', 'id' => 'CaptchaLoader']) ?>
          <?php echo $this->BcForm->text('BlogComment.auth_captcha') ?><br/>
          &nbsp;<?php echo __('画像の文字を入力してください') ?><br/>
        </div>
      <?php endif ?>

      <?php echo $this->BcForm->end(['label' => __('送信する'), 'id' => 'BlogCommentAddButton', 'class' => 'button']) ?>
      <div id="ResultMessage" class="message" style="display:none;text-align:center">&nbsp;</div>
    </div>
  </div>
<?php endif ?>
