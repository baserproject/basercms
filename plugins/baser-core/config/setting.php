<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
use Cake\Core\Configure;

// TODO Cake4 での prefixes の設定を調べる
// >>>
//$prefixes = Configure::read('Routing.prefixes');
$prefixes = ['admin'];
// <<<

$adminPrefix = $prefixes[0];
return [
    'BcAuthPrefix' => [
        // 管理画面
	    'admin' => [
            // 認証タイプ
            'type' => 'Form',
            // 認証設定名
            'name' => __d('baser', '管理システム'),
            // URLにおけるエイリアス
            'alias' => $adminPrefix,
            // 認証後リダイレクト先
            'loginRedirect' => '/' . $adminPrefix,
            // ログイン画面タイトル
            'loginTitle' => __d('baser', '管理システムログイン'),
            // ログインページURL
            'loginAction' => '/' . $adminPrefix . '/users/login',
            // ログアウトページURL
            'logoutAction'=> '/' . $adminPrefix . '/users/logout',
            // ツールバー利用
            'toolbar' => true,
            // モデル
            'userModel' => 'User',
            // セッションキー
            'sessionKey' => 'Admin',
            // preview及びforce指定時に管理画面へログインしていない状況下での挙動判別
            // true：ログイン画面へリダイレクト
            // false：ログイン画面へリダイレクトしない
            // @see /lib/Baser/Routing/Route/BcContentsRoute.php
            'previewRedirect' => true
	    ]
        // フロント（例）
        /* 'front' => [
          'name'			=> __d('baser', 'フロント'),
          'loginRedirect'	=> '/',
          'userModel'		=> 'User',
          'loginAction'	=> '/users/login',
          'logoutAction'=> '/users/logout',
          'toolbar'		=> true,
          'sessionKey'	=> 'User'
        ], */
        // マイページ（例）
        /* 'mypage' => [
          'name'			=> __d('baser', 'マイページ'),
          'alias'			=> 'mypage',
          'loginRedirect'	=> '/mypage/members/index',
          'loginTitle'	=> __d('baser', 'マイページログイン'),
          'userModel'		=> 'Member',
          'loginAction'	=> '/mypage/members/login',
          'logoutAction'=> '/mypage/members/logout',
          'toolbar'		=> false,
          'sessionKey'	=> 'User'
        ] */
    ]
];
