<h2><?php $baser->contentsTitle() ?></h2>
<p>パスワードを忘れた方は、登録されているメールアドレスを送信してください。<br />
新しいパスワードをメールでお知らせします。</p>
<?php echo $formEx->create('User', array('action'=>'reset_password')) ?>
<?php echo $formEx->text('User.email', array('size'=>36)) ?>
<?php echo $formEx->end(array('label'=>'送　信', 'div'=>false, 'class'=>'btn-red button')) ?>