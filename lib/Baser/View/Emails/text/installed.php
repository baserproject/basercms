<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [EMAIL] インストール完了メール
 */
$adminPrefix = BcUtil::getAdminPrefix();
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
管理ページ： <?php echo topLevelUrl(false) . Configure::read('App.baseUrl').'/' . $adminPrefix . '/users/login' ?>　
アカウント： <?php echo $name ?>　
パスワード： <?php echo $password ?>　
※ パスワードはユーザー管理より変更する事ができます。


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ baserCMSでどんな事ができるの？
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
baserCMS を初めてご利用される場合は次のページをご覧ください。

■ baserCMS公式サイト
http://basercms.net/

■ baserCMSとは？
http://basercms.net/about/index.html

■ 機能一覧
http://basercms.net/about/feature.html

■ はじめてガイド
http://basercms.net/about/guide.html

■ baserCMS公式ガイド
http://wiki.basercms.net/

■ baserCMSメールマガジン
https://groups.google.com/forum/?fromgroups#!forum/basercms


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
■ baserCMSユーザー会
http://basercms.net/community
baserCMSコミュニティの紹介ページです。Let's Join！

■ baserCMS公式Facebook
http://www.facebook.com/basercms
いいね！をクリックして普及にご協力ください。

■ baserCMS公式Twitter
http://twitter.com/#!/basercms
TIPSや最新情報をつぶやいてます。Follow Me！

■ baserCMSの雑談広場（Facebook）
http://www.facebook.com/groups/308200669249993/
ちょっとした雑談やつぶやきにどうぞ。初心者歓迎！

■ 各地域のユーザーグループ（Facebook）
ご自分のエリアのユーザーグループに参加して情報交換しましょう。
《baserCMS UG 北海道》
https://www.facebook.com/groups/921458584560227/
《baserCMS UG 東北》
https://www.facebook.com/groups/baser.ug.tohoku/
《baserCMS UG 関東》
https://www.facebook.com/groups/1421256191506609/
《baserCMS UG 近畿・中部》
https://www.facebook.com/groups/576635385808915/
《baserCMS UG 中国・四国》
https://www.facebook.com/groups/996045227085786/
《baserCMS UG 九州北部》
https://www.facebook.com/groups/785098234940587/
《baserCMS UG 九州南部》
https://www.facebook.com/groups/318497551997358/

■ baserCMSユーザーズフォーラム
http://forum.basercms.net/
不具合や改善報告はこちらよりどうぞ。

■ baserCMS開発プロジェクト
http://project.e-catchup.jp/projects/basercms
baserCMSの開発状況や、今後の開発タスクなどが確認できます。
