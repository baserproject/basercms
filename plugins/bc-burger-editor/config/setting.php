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
		// システム管理者によるアップロードでいかなる拡張子も許可する
		'allowedAdmin' => false,
		// システム管理者グループ以外のユーザーがアップロード可能なファイル（拡張子をカンマ区切りで指定する）
		'allowedExt' => 'gif,jpg,jpeg,png,ico,pdf,zip,doc,docx,xls,xlsx,ppt,pptx,txt',
		// 保存対象のターゲットとなるフィールド
		'targetColumns' => ['content', 'content_draft', 'detail', 'detail_draft']
	]
];

return $config;
