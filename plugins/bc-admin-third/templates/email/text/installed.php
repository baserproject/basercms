<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
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
<?php echo __d('baser', '管理ページ')?>： <?php echo $siteUrl . $adminPrefix . '/users/login' ?>　
<?php echo __d('baser', 'アカウント')?>： <?php echo $name ?>　
<?php echo __d('baser', 'パスワード')?>： <?php echo $password ?>　
※ <?php echo __d('baser', 'パスワードはユーザー管理より変更する事ができます。')?>　


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser', 'baserCMSでどんな事ができるの？')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
<?php echo __d('baser', 'baserCMS を初めてご利用される場合は次のページをご覧ください。')?>　

■ <?php echo __d('baser', 'baserCMS公式サイト')?>　
https://basercms.net/

■ <?php echo __d('baser', 'baserCMSとは？')?>　
https://basercms.net/about/index.html

■ <?php echo __d('baser', '機能一覧')?>　
https://basercms.net/about/feature.html

■ <?php echo __d('baser', 'はじめてガイド')?>　
https://basercms.net/about/guide.html

■ <?php echo __d('baser', 'baserCMS公式ガイド')?>　
https://wiki.basercms.net/

■ <?php echo __d('baser', 'baserCMSメールマガジン')?>　
https://member.basercms.net/magazine/regist


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser', '事例掲載について')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
<?php echo __d('baser', 'あなたが作った baserCMS の Webサイトを、公式サイトでアピールしませんか？')?>　

■ <?php echo __d('baser', 'baserCMS導入事例')?>　
https://basercms.net/works/index

■ <?php echo __d('baser', '事例掲載依頼フォーム')?>　
https://basercms.net/postworks/index


━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser', 'ユーザーズコミュニティについて')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
■ <?php echo __d('baser', 'baserCMSユーザー会')?>　
https://basercms.net/community
<?php echo __d('baser', 'baserCMSコミュニティの紹介ページです。Let\'s Join！')?>　

■ <?php echo __d('baser', 'baserCMS公式Facebook')?>　
https://www.facebook.com/basercms
<?php echo __d('baser', 'いいね！をクリックして普及にご協力ください。')?>　

■ <?php echo __d('baser', 'baserCMS公式Twitter')?>　
https://twitter.com/basercms
<?php echo __d('baser', 'TIPSや最新情報をつぶやいてます。Follow Me！')?>　

■ <?php echo __d('baser', 'baserCMSの雑談広場（Facebook）')?>　
https://www.facebook.com/groups/308200669249993/
<?php echo __d('baser', 'ちょっとした雑談やつぶやきにどうぞ。初心者歓迎！')?>　

■ <?php echo __d('baser', 'baserCMSユーザーズフォーラム')?>　
https://forum.basercms.net/
<?php echo __d('baser', '不具合や改善報告はこちらよりどうぞ。')?>　

■ <?php echo __d('baser', 'baserCMS GitHub')?>　
https://github.com/baserproject/basercms
<?php echo __d('baser', 'baserCMSの開発状況や、今後の開発タスクなどが確認できます。')?>　
　
　
　
