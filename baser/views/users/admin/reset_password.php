<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] パスワードリセット画面
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$userModel = Configure::read('BcAuthPrefix.'.$currentPrefix.'.userModel');
if(!$userModel) {
	$userModel = 'User';
}
?>


<div class="section">
<p>パスワードを忘れた方は、登録されているメールアドレスを送信してください。<br />
新しいパスワードをメールでお知らせします。</p>
<?php if($currentPrefix == 'front'): ?>
<?php echo $bcForm->create($userModel, array('action' => 'reset_password')) ?>
<?php else: ?>
<?php echo $bcForm->create($userModel, array('action' => 'reset_password', 'url' => array($this->params['prefix'] => true))) ?>
<?php endif ?>
<div class="submit">
<?php echo $bcForm->input($userModel . '.email', array('type' => 'text', 'size' => 60)) ?>
<?php echo $bcForm->submit('送信', array('div' => false, 'class' => 'btn-red button')) ?>
</div>
<?php echo $bcForm->end() ?>
</div>