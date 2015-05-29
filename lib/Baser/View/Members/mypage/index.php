<?php
/**
 * [MYPAGE] メンバー編集（デモ用）
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.layout
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$authPrefix = Configure::read('BcAuthPrefix.' . $currentPrefix);
?>


<h1><?php $this->BcBaser->contentsTitle() ?></h1>

<p><?php $this->BcBaser->link('ログアウト', $authPrefix['logoutAction']) ?>