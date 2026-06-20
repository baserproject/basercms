<?php
/**
 * ブロックに適用されるのCSS変更機能設定
 *	- 機能拡張により利用される設定が追加される可能性があります
 *
 * [ルール]
 * "class名" => '表示名' という形式で設定可能です
 *  class名は「bgb-opt--」という名称から開始される必要があります。
 *
 * [注意事項]
 * 初期値はbge_style.cssにてclassが設定されています。
 * 変更する場合は bge_style.cssを編集してください。
 * 追加する場合は、独自にclassが適用される用変更してください。
 *
 * [拡張]
 * free-settingを利用にすると、独自設定のクラス選択が設定可能です。
 * 利用するにはコメントアウトしている free-settingの項目を有効化して確認してください。
 *
 * デフォルト項目を利用しない場合はコメントアウトすることで非表示になります。
 */
$bgBlockConfig = [
	// 自由設定枠 - 「設定」
//	'free-setting' => array(
//		'bgb-opt--yourclass1' => 'サンプル1',
//		'bgb-opt--yourclass2' => 'サンプル2',
//		'bgb-opt--yourclass3' => 'サンプル3',
//		'bgb-opt--yourclass4' => 'サンプル4',
//	),
	// 下余白
	'margin-bottom' => [
		"bgb-opt--mb-large"	 => "広い",
		"" => "標準",
		"bgb-opt--mb-small"	 => "狭い",
		"bgb-opt--mb-none"	 => "無し",
	],

	// 背景色
	'background-color' => [
		"" => "指定なし",
		"bgb-opt--bg-gray"	 => "グレー",
		"bgb-opt--bg-blue"	 => "ブルー",
		"bgb-opt--bg-pink"	 => "ピンク",
	],

	// 枠線 - スタイル
	'border-style' => [
		"" => "指定なし",
		"bgb-opt--border-none"		 => "無し",
		"bgb-opt--border-bold"		 => "太い",
		"bgb-opt--border-thin"		 => "細い",
		"bgb-opt--border-dotted"	 => "点線",
	],
	// 枠線 - 適用箇所
	'border-type' => [
		"" => "指定なし",
		"bgb-opt--border-trbl"	 => "上下左右",
		"bgb-opt--border-tb"	 => "上下",
		"bgb-opt--border-lr"	 => "左右",
		"bgb-opt--border-trl"	 => "下抜け",
		"bgb-opt--border-rbl"	 => "上抜け",
	],
];