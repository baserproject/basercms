<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */

use Cake\Utility\Hash;

$config = [
    'BcApp' => [
        'editors' => ['BcBurgerEditor.BurgerEditor' => 'BurgerEditor']
    ],
    'BcShortCode' => [
        'BcBurgerEditor' => [
            'BurgerEditor.preventLoadingStyle'
        ]
    ],
    'Bge' => [
        // ログインユーザに関わらずアップロードファイルを共有する
        'fileShare' => true,
        // 自動的に bge-contentsクラスを付与する
        'autoWrapper' => true,
        // autoWrapper で付与するクラス名
        'wrapperClass' => 'bge-contents',
        // ブロックに設定された公開日時・非公開日時による出し分けを行う
        'publishTimer' => false,
        // プラグイン側のCSSを自動的に読み込む
        // テーマ側で読み込みを制御したい場合は false を指定する
        'loadCSS' => [
            // プラグイン標準のスタイル
            'bge_style_default' => true,
            // テーマ、webroot/css、プラグインの順に探索して読み込むスタイル
            'bge_style' => true,
            // 画像ポップアップ（colorbox）用のスタイル
            'colorbox' => true,
        ],
        // 画像タイプのポップアップ選択設定を初期値onにする
        'defaultImagePopup' => true,
        // リサイズしない拡張子指定
        'noResizeExtension' => [
            'gif'
        ],
        'uploadImageSize' => [
            'imgSizeWidthMax' => 2400,
            'imgSizeWidthDefault' => 1200,
            'imgSizeWidthSmall' => 600,
        ],
        // (1024 * 1024 * 10)アップロード可能な最大サイズ10MB
        'uploadImageDataSize' => 10485760,
        // 画像以外のアップロード可能な最大サイズ10MB
        'uploadFileDataSize' => 10485760,
        // 画像リサイズ時の圧縮レベル
        'uploadImageQuality' => [
            IMAGETYPE_JPEG => 90, // JPEG: 0 から 100 を指定
            IMAGETYPE_PNG => 6, // PNG:  0 から   9 を指定
        ],
        // cssに対するサフィックスを付与
        'enableStaticFileSuffix' => false,
        // サフィックスに追加する文字列
        'staticFileSuffix' => '',
        // Addon を提供するプラグインを配列で指定
        // プラグインの直下に「BurgerAddon」というフォルダに Addon を配置する
        'enableAddonPlugin' => [],
        // システム管理者によるアップロードで allowedExt 以外の拡張子も許可する
        // ただし php, phtml, phar, cgi 等のサーバーサイドで実行され得る拡張子は、
        // 本設定を true にしても常に拒否される
        'allowedAdmin' => false,
        // システム管理者グループ以外のユーザーがアップロード可能なファイル（拡張子をカンマ区切りで指定する）
        'allowedExt' => 'gif,jpg,jpeg,png,ico,pdf,zip,doc,docx,xls,xlsx,ppt,pptx,txt',
        // アップロードを常に拒否する拡張子（配列で指定する）
        // allowedAdmin や allowedExt の設定にかかわらず拒否される。
        // アップロードディレクトリに設置する .htaccess もこの一覧から生成される。
        'deniedExtension' => [
            'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'php8',
            'phtml', 'phtm', 'pht', 'phps', 'phar',
            'shtml', 'shtm',
            'cgi', 'pl', 'py', 'rb', 'sh', 'bash',
            'jsp', 'jspx', 'asp', 'aspx', 'ashx',
            'htaccess', 'htpasswd',
        ],
        // 保存対象のターゲットとなるフィールド
        'targetColumns' => ['content', 'content_draft', 'detail', 'detail_draft']
    ]
];

return $config;
