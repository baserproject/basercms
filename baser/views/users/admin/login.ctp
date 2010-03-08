<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ログイン
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php echo $html->css('login',null,null,false) ?>
<?php if ( $session->check('Message.auth') ) {
    $session->flash('auth');
} ?>
<div id="login">
<div class="box-01">
<div class="box-head">
<h3>アカウント／パスワードを入力して下さい</h3>
</div>
<div class="box-body">
<?php echo $form->create('User',array('action'=>'login')) ?>

<table border="0">
<tr><td style="text-align:right">アカウント&nbsp;</td><td style="text-align:left"><?php echo $form->text('User.name',array('size'=>20)) ?></td></tr>
<tr><td style="text-align:right">パスワード&nbsp;</td><td style="text-align:left"><?php echo $form->password('User.password',array('size'=>20)) ?></td></tr>
</table>
<br /><br />
<p>
<?php echo $form->checkbox('User.saved') ?> <?php echo $form->label('User.saved','<small>次回から自動的にログイン</small>') ?>　
<?php echo $form->end(array('label'=>'　ログインする　', 'div'=>false, 'class'=>'btn-red button')) ?>
</p>
</div>
<div class="box-foot">
&nbsp;
</div>
</div>
</div>

