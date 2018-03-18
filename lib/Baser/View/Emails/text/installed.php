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
　　　　　　◆◇　<?php echo __d('baser', 'baserCMSのインストールが完了しました')?>　◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　<?php echo __d('baser', 'おめでとうございます！')?>　
　<?php echo __d('baser', '次のURLへbaserCMSのインストールが完了しました。')?>　
　<?php echo $siteUrl ?>　


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser', 'ログイン情報')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
<?php echo __d('baser', '管理ページ')?>： <?php echo topLevelUrl(false) . Configure::read('App.baseUrl').'/' . $adminPrefix . '/users/login' ?>　
<?php echo __d('baser', 'アカウント')?>： <?php echo $name ?>　
<?php echo __d('baser', 'パスワード')?>： <?php echo $password ?>　
※ <?php echo __d('baser', 'パスワードはユーザー管理より変更する事ができます。')?>　


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser', 'baserCMSでどんな事ができるの？')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
<?php echo __d('baser', 'baserCMS を初めてご利用される場合は次のページをご覧ください。')?>　

■ <?php echo __d('baser', 'baserCMS公式サイト')?>　
http://basercms.net/

■ <?php echo __d('baser', 'baserCMSとは？')?>　
http://basercms.net/about/index.html

■ <?php echo __d('baser', '機能一覧')?>　
http://basercms.net/about/feature.html

■ <?php echo __d('baser', 'はじめてガイド')?>　
http://basercms.net/about/guide.html

■ <?php echo __d('baser', 'baserCMS公式ガイド')?>　
http://wiki.basercms.net/

■ <?php echo __d('baser', 'baserCMSメールマガジン')?>　
https://member.basercms.net/magazine/regist


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser', '事例掲載について')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
<?php echo __d('baser', 'あなたが作った baserCMS の Webサイトを、公式サイトでアピールしませんか？')?>　

■ <?php echo __d('baser', 'baserCMS導入事例')?>　
http://basercms.net/works/index

■ <?php echo __d('baser', '事例掲載依頼フォーム')?>　
http://basercms.net/postworks/index


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser', 'ユーザーズコミュニティについて')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
■ <?php echo __d('baser', 'baserCMSユーザー会')?>　
http://basercms.net/community
<?php echo __d('baser', 'baserCMSコミュニティの紹介ページです。Let\'s Join！')?>　

■ <?php echo __d('baser', 'baserCMS公式Facebook')?>　
http://www.facebook.com/basercms
<?php echo __d('baser', 'いいね！をクリックして普及にご協力ください。')?>　

■ <?php echo __d('baser', 'baserCMS公式Twitter')?>　
http://twitter.com/#!/basercms
<?php echo __d('baser', 'TIPSや最新情報をつぶやいてます。Follow Me！')?>　

■ <?php echo __d('baser', 'baserCMSの雑談広場（Facebook）')?>　
http://www.facebook.com/groups/308200669249993/
<?php echo __d('baser', 'ちょっとした雑談やつぶやきにどうぞ。初心者歓迎！')?>　

■ <?php echo __d('baser', '各地域のユーザーグループ（Facebook）')?>　
<?php echo __d('baser', 'ご自分のエリアのユーザーグループに参加して情報交換しましょう。')?>　
《<?php echo __d('baser', 'baserCMS UG 北海道')?>》
https://www.facebook.com/groups/921458584560227/
《<?php echo __d('baser', 'baserCMS UG 東北')?>》
https://www.facebook.com/groups/baser.ug.tohoku/
《<?php echo __d('baser', 'baserCMS UG 関東')?>》
https://www.facebook.com/groups/1421256191506609/
《<?php echo __d('baser', 'baserCMS UG 近畿・中部')?>》
https://www.facebook.com/groups/576635385808915/
《<?php echo __d('baser', 'baserCMS UG 中国・四国')?>》
https://www.facebook.com/groups/996045227085786/
《<?php echo __d('baser', 'baserCMS UG 九州北部')?>》
https://www.facebook.com/groups/785098234940587/
《<?php echo __d('baser', 'baserCMS UG 九州南部')?>》
https://www.facebook.com/groups/318497551997358/

■ <?php echo __d('baser', 'baserCMSユーザーズフォーラム')?>　
http://forum.basercms.net/
<?php echo __d('baser', '不具合や改善報告はこちらよりどうぞ。')?>　

■ <?php echo __d('baser', 'baserCMS開発プロジェクト')?>　
http://project.e-catchup.jp/projects/basercms
<?php echo __d('baser', 'baserCMSの開発状況や、今後の開発タスクなどが確認できます。')?>　
　
　
　
