<?php
/* SVN FILE: $Id$ */
/**
 * [EMAIL] インストール完了メール
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$adminPrefix = Configure::read('Routing.admin');
?>

                                           <?php echo date('Y-m-d H:i:s') ?> 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　◆◇　baserCMSのインストールが完了しました　◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　おめでとうございます！
　次のURLへbaserCMSのインストールが完了しました。
　<?php echo $siteUrl ?> 

━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ ログイン情報
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
管理ページ： <?php echo topLevelUrl(false).Configure::read('App.baseUrl').'/'.$adminPrefix.'/users/login' ?> 
アカウント： <?php echo $name ?>　
パスワード： <?php echo $password ?>　

※ パスワードはユーザー管理より変更する事ができます。

 