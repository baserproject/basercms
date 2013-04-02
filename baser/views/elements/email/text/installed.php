<?php
/* SVN FILE: $Id$ */
/**
 * [EMAIL] インストール完了メール
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
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
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
管理ページ： <?php echo topLevelUrl(false).Configure::read('App.baseUrl').'/admin/users/login' ?> 
アカウント： <?php echo $name ?>　
パスワード： <?php echo $password ?>　
※ パスワードはユーザー管理より変更する事ができます。


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ baserCMSでどんな事ができるの？
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
baserCMS を初めてご利用される場合は次のページをご覧ください。

■ baserCMSとは？
http://basercms.net/about/index.html

■ 機能一覧
http://basercms.net/about/feature.html

■ はじめてガイド
http://basercms.net/about/guide.html


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ スマートURLについて
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
スマートURL機能は、サイトのURLを短くわかりやすいURL表示に変更する機能です。
この機能を利用する事で、短くスマートなURLを実現する事ができます。
また、表示速度改善にも役立ちます。

スマートURLを実現するにはApache Rewriteモジュールと.htaccessの利用許可が必要です。
スマートURLの設定は、管理画面のシステム設定より行えます。
　（例）
　　・スマートURLオフ：http://localhost/index.php/contact/index
　　・スマートURLオン：http://localhost/contact/index

スマートURLの設定はサーバー環境に深く依存します。
「オン」に変更した場合、サーバーエラーとなり画面にアクセスできなくなる可能性もありますのでご注意ください。
こちらより各種レンタルサーバーの動作状況を確認できます。
http://basercms.net/hosting/index

----------------------------------------
スマートURLがうまく動作しない場合は、
下記２ヶ所の.htaccessファイルのコメントを確認してください。
/.htaccess
/app/webroot/.htaccess
----------------------------------------


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ 事例掲載について
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
あなたが作った baserCMS の Webサイトを、公式サイトでアピールしませんか？

■ baserCMS導入事例
http://basercms.net/works/index

■ 事例掲載依頼フォーム
http://basercms.net/postworks/index


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ ユーザーズコミュニティについて
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
■ baserCMS公式サイト
http://basercms.net/
最新版のダウンロード、利用方法やカスタマイズ方法はこちらから！

■ baserCMS公式Facebook
http://www.facebook.com/basercms
いいね！をクリックして普及にご協力ください！

■ baserCMS公式Twitter
http://twitter.com/#!/basercms
TIPSや最新情報をつぶやいてます！ Follow Me！

■ baserCMSの雑談広場（Facebook）
http://www.facebook.com/groups/308200669249993/
ちょっとした雑談やつぶやきに！初心者歓迎！

■ baserCMSユーザーズフォーラム
http://forum.basercms.net/
不具合や改善報告はこちらよりどうぞ！

■ baserCMS勉強会サイト
http://study.basercms.net/
勉強会の資料などを配布しています。

■ baserCMSユーザー会
http://basercms.net/
baserCMSコミュニティの公式サイトです。

■ baserCMSユーザー会メーリングリスト
https://groups.google.com/forum/?fromgroups#!forum/basercms
baserCMSの最新情報などを発信しています！まずはメーリングリストへ参加！

■ baserCMSサポーターズ（Facebook）
http://www.facebook.com/groups/331974406865338/
baserCMSプロジェクトの運営に参加されませんか？baserCMSを盛り上げよう！

■ baserCMS開発プロジェクト
http://project.e-catchup.jp/projects/basercms
baserCMSの開発状況や、今後の開発タスクなどが確認できます。