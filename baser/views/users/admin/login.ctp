<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ログイン
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if ( $session->check('Message.auth') ) {
    $session->flash('auth');
}
$userModel = Configure::read('AuthPrefix.'.$this->params['prefix'].'.userModel');
?>

<h2>
	<?php $baser->contentsTitle() ?>
</h2>

<div id="login">
	<div class="box-01">
		<div class="box-head">
			<h3>アカウント／パスワードを入力してください</h3>
		</div>
		<div class="box-body">
			<?php echo $formEx->create($userModel, array('action' => 'login', 'url' => array($this->params['prefix'] => true, 'controller' => 'users'))) ?>
			<table border="0">
				<tr>
					<td style="text-align:right">アカウント&nbsp;</td>
					<td style="text-align:left"><?php echo $formEx->input($userModel.'.name', array('type' => 'text', 'size'=>20)) ?></td>
				</tr>
				<tr>
					<td style="text-align:right">パスワード&nbsp;</td>
					<td style="text-align:left"><?php echo $formEx->input($userModel.'.password',array('type' => 'password', 'size'=>20)) ?></td>
				</tr>
			</table>
			<br />
			<br />
			<p><?php echo $formEx->input($userModel.'.saved', array('type' => 'checkbox')) ?>&nbsp;
				<?php echo $formEx->label($userModel.'.saved', '<small>次回から自動的にログイン</small>') ?>　
				<?php echo $formEx->submit('　ログインする　', array('div' => false, 'class' => 'btn-red button')) ?> </p>
			<p><small><?php $baser->link('パスワードを忘れた？', array('action' => 'reset_password', $this->params['prefix'] => true), array('rel' => 'popup')) ?></small></p>
			<?php echo $formEx->end() ?>
		</div>
		<div class="box-foot">&nbsp;</div>
	</div>
</div>
