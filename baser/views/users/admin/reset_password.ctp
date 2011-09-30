<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] パスワードリセット画面
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
?>
<h2><?php $baser->contentsTitle() ?></h2>
<p>パスワードを忘れた方は、登録されているメールアドレスを送信してください。<br />
新しいパスワードをメールでお知らせします。</p>
<?php echo $formEx->create('User', array('action' => 'reset_password')) ?>
<?php echo $formEx->input('User.email', array('type' => 'text', 'size' => 36)) ?>
<?php echo $formEx->end(array('label' => '送　信', 'div' => false, 'class' => 'btn-red button')) ?>