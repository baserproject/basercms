<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcCkeditorHelper
 *
 * @package Baser.View.Helper
 */
class BcCkeditorHelper extends AppHelper
{

	/**
	 * ヘルパー
	 * @var array
	 */
	public $helpers = ['BcHtml', 'BcForm', 'JqueryEngine'];

	/**
	 * スクリプト
	 * 既にjavascriptが読み込まれている場合はfalse
	 *
	 * @var boolean
	 */
	protected $_script = false;

	/**
	 * 初期化状態
	 * 複数のCKEditorを設置する場合、一つ目を設置した時点で true となる
	 *
	 * @var boolean
	 */
	public $inited = false;

	/**
	 * スタイル初期化判定
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_initedStyles = false;

	/**
	 * 初期設定スタイル
	 * StyleSet 名 basercms
	 * 翻訳がある為、コンストラクタで初期化
	 *
	 * @var array
	 */
	public $style = [];

	/**
	 * ツールバー
	 *
	 * @var array
	 */
	public $toolbars = [
		'simple' => [
			['Bold', 'Underline', '-',
				'NumberedList', 'BulletedList', '-',
				'JustifyLeft', 'JustifyCenter', 'JustifyRight',
				'Format', 'FontSize', 'TextColor', 'BGColor', '-', 'Link', 'Unlink', '-', 'Image'],
			['Maximize', 'ShowBlocks', 'Source']
		],
		'normal' => [
			['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo', '-', 'Bold', 'Italic', 'Underline', 'Strike', '-',
				'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', '-',
				'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
				'Smiley', 'Table', 'HorizontalRule', '-'],
			['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor', '-', 'Link', 'Unlink', 'Anchor', '-', 'Image'],
			['Maximize', 'ShowBlocks', 'Source']
		]
	];

	/**
	 * BcCkeditorHelper constructor.
	 * @param View $View
	 * @param array $settings
	 */
	public function __construct(View $View, $settings = [])
	{
		parent::__construct($View, $settings);
		$this->style = [
			['name' => __d('baser', '青見出し') . '(h3)',
				'element' => 'h3',
				'styles' => ['color' => 'Blue']],
			['name' => __d('baser', '赤見出し') . '(h3)',
				'element' => 'h3',
				'styles' => ['color' => 'Red']],
			['name' => __d('baser', '黄マーカー') . '(span)',
				'element' => 'span',
				'styles' => ['background-color' => 'Yellow']],
			['name' => __d('baser', '緑マーカー') . '(span)',
				'element' => 'span',
				'styles' => ['background-color' => 'Lime']],
			['name' => __d('baser', '大文字') . '(big)',
				'element' => 'big'],
			['name' => __d('baser', '小文字') . '(small)',
				'element' => 'small'],
			['name' => __d('baser', 'コード') . '(code)',
				'element' => 'code'],
			['name' => __d('baser', '削除文') . '(del)',
				'element' => 'del'],
			['name' => __d('baser', '挿入文') . '(ins)',
				'element' => 'ins'],
			['name' => __d('baser', '引用') . '(cite)',
				'element' => 'cite'],
			['name' => __d('baser', 'インライン') . '(q)',
				'element' => 'q']
		];
	}

	/**
	 * CKEditor のスクリプトを構築する
	 * 【ボタン一覧】
	 * Source            - ソース
	 * Save                - 保存
	 * NewPage            - 新しいページ
	 * Preview            - プレビュー
	 * Templates        - テンプレート
	 * Cut                - 切り取り
	 * Copy                - コピー
	 * Paste            - 貼り付け
	 * PasteText        - プレーンテキスト貼り付け
	 * PasteFromWord    - ワードから貼り付け
	 * Print            - 印刷
	 * SpellChecker        - スペルチェック
	 * Scayt            - スペルチェック設定
	 * Undo                - 元に戻す
	 * Redo                - やり直し
	 * Find                - 検索
	 * Replace            - 置き換え
	 * SelectAll        - すべて選択
	 * RemoveFormat        - フォーマット削除
	 * Form                - フォーム
	 * Checkbox            - チェックボックス
	 * Radio            - ラジオボタン
	 * TextField        - 1行テキスト
	 * Textarea            - テキストエリア
	 * Select            - 選択フィールド
	 * Button            - ボタン
	 * ImageButton        - 画像ボタン
	 * HiddenField        - 不可視フィールド
	 * Bold                - 太字
	 * Italic            - 斜体
	 * Underline        - 下線
	 * Strike            - 打ち消し線
	 * Subscript        - 添え字
	 * Superscript        - 上付き文字
	 * NumberedList        - 段落番号
	 * BulletedList        - 箇条書き
	 * Outdent            - インデント解除
	 * Indent            - インデント
	 * Blockquote        - ブロック引用
	 * JustifyLeft        - 左揃え
	 * JustifyCenter    - 中央揃え
	 * JustifyRight        - 右揃え
	 * JustifyBlock        - 両端揃え
	 * Link                - リンク挿入／編集
	 * Unlink            - リンク解除
	 * Anchor            - アンカー挿入／編集
	 * Image            - イメージ
	 * Flash            - FLASH
	 * Table            - テーブル
	 * HorizontalRule    - 横罫線
	 * Smiley            - 絵文字
	 * SpecialChar        - 特殊文字
	 * PageBreak        - 改ページ挿入
	 * Styles            - スタイル
	 * Format            - フォーマット
	 * Font                - フォント
	 * FontSize            - フォントサイズ
	 * TextColor        - テキスト色
	 * BGColor            - 背景色
	 * Maximize            - 最大化
	 * ShowBlocks        - ブロック表示
	 * About            - CKEditorバージョン情報
	 * Publish            - 本稿に切り替え
	 * Draft            - 草稿に切り替え
	 * CopyPublish        - 本稿を草稿にコピー
	 * CopyDraft        - 草稿を本稿にコピー
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	function _build($fieldName, $options = [])
	{

		$options = array_merge([
			'editorLanguage' => 'ja', // 言語
			'editorSkin' => 'moono', // スキン
			'editorToolType' => 'normal', // ツールバータイプ
			'editorToolbar' => [], // ツールバータイプ
			'editorWidth' => '600px', // エディタサイズ
			'editorHeight' => '300px', // エディタ高さ
			'editorCollapser' => false, //
			'editorBaseFloatZIndex' => 900, //
			'editorStylesSet' => 'basercms', // スタイルセット
			'editorUseDraft' => false, // 草稿利用
			'editorDraftField' => false, // 草稿用フィールド
			'editorDisablePublish' => false, // 本稿利用不可
			'editorDisableDraft' => true, // 草稿利用不可
			'editorDisableCopyDraft' => false, // 草稿へコピー利用不可
			'editorDisableCopyPublish' => false, // 本稿へコピー利用不可
			'editorReadOnlyPublish' => false, // 本稿読み込みのみ許可
			'editorUseTemplates' => true, // テンプレート利用
			'editorEnterBr' => false, // エンター時に改行を入れる
			'editorStyles' => []  // スタイル
		], $options);

		extract($options);
		if (empty($editorToolbar)) {
			$options['editorToolbar'] = $this->toolbars[$editorToolType];
			if ($editorUseTemplates) {
				switch($editorToolType) {
					case 'simple':
						$options['editorToolbar'][0][] = 'Templates';
						break;
					case 'normal':
						$options['editorToolbar'][1][] = 'Templates';
						break;
				}
			}
		}

		if (isset($options['editorStylesSet']))
			unset($options['editorStylesSet']);
		if (isset($options['editorUseDraft']))
			unset($options['editorUseDraft']);
		if (isset($options['editorDraftField']))
			unset($options['editorDraftField']);
		if (isset($options['editorDisablePublish']))
			unset($options['editorDisablePublish']);
		if (isset($options['editorDisableDraft']))
			unset($options['editorDisableDraft']);
		if (isset($options['editorDisableCopyDraft']))
			unset($options['editorDisableCopyDraft']);
		if (isset($options['editorDisableCopyPublish']))
			unset($options['editorDisableCopyPublish']);
		if (isset($options['editorReadOnlyPublish']))
			unset($options['editorReadOnlyPublish']);
		if (isset($options['editorUseTemplates']))
			unset($options['editorUseTemplates']);
		if (isset($options['editorEnterBr']))
			unset($options['editorEnterBr']);
		if (isset($options['editorToolType']))
			unset($options['editorToolType']);

		$_options = [];
		foreach($options as $key => $option) {
			$key = preg_replace('/^editor/', '', $key);
			$key = Inflector::variable($key);
			$_options[$key] = $option;
		}
		$options = $_options;

		$jscode = $model = $domId = '';
		if (strpos($fieldName, '.')) {
			list($model, $field) = explode('.', $fieldName);
		} else {
			$field = $fieldName;
		}
		if ($editorUseDraft) {
			$publishAreaId = Inflector::camelize($model . '_' . $field);
			$draftAreaId = Inflector::camelize($model . '_' . $editorDraftField);
			$field .= '_tmp';
			$fieldName .= '_tmp';
		}

		$domId = $this->domId($fieldName);

		if (!$this->_script) {
			$this->_script = true;
			$this->BcHtml->script('admin/vendors/ckeditor/ckeditor.js', ["inline" => false]);
		}

		if ($editorUseDraft) {
			$lastBar = $options['toolbar'][count($options['toolbar']) - 1];
			$lastBar = am($lastBar, ['-', 'Publish', '-', 'Draft']);
			if (!$editorDisableCopyDraft) {
				$lastBar = am($lastBar, ['-', 'CopyDraft']);
			}
			if (!$editorDisableCopyPublish) {
				$lastBar = am($lastBar, ['-', 'CopyPublish']);
			}
			$options['toolbar'][count($options['toolbar']) - 1] = $lastBar;
		}

		$this->BcHtml->scriptBlock("var editor_" . $field . ";", ["inline" => false]);
		$jscode = "$(window).load(function(){";
		if (!$this->inited) {
			$jscode .= "CKEDITOR.addStylesSet('basercms'," . $this->JqueryEngine->object($this->style) . ");";
			$this->inited = true;
		} else {
			$jscode .= '';
		}
		if (!$this->_initedStyles && $editorStyles) {
			foreach($editorStyles as $key => $style) {
				$jscode .= "CKEDITOR.addStylesSet('" . $key . "'," . $this->JqueryEngine->object($style) . ");";
			}
			$this->_initedStyles = true;
		}

		if ($editorUseTemplates) {
			$jscode .= "CKEDITOR.config.templates_files = [ '" . $this->url(['admin' => true, 'plugin' => null, 'controller' => 'editor_templates', 'action' => 'js']) . "' ];";
		}
		$jscode .= "CKEDITOR.config.allowedContent = true;";
		$jscode .= "CKEDITOR.config.extraPlugins = 'draft,showprotected';";
		$jscode .= "CKEDITOR.config.stylesCombo_stylesSet = '" . $editorStylesSet . "';";
		$jscode .= "CKEDITOR.config.protectedSource.push( /<\?[\s\S]*?\?>/g );";
		$dtd = Configure::read('CkeditorConfig.dtd');
		if (!empty($dtd['allowIntoA'])) { // aタグ内に入れることを許可するブロック要素を出力
			foreach ($dtd['allowIntoA'] as $allowIntoA) {
				$jscode .= is_string($allowIntoA) ? 'CKEDITOR.dtd.a.'. $allowIntoA.' = 1;' : '';
			}
		}
		if (!empty($dtd['allowEmpty'])) { // 空を許可する要素を出力
			foreach ($dtd['allowEmpty'] as $allowEmpty) {
				$jscode .= is_string($allowEmpty) ? 'CKEDITOR.dtd.$removeEmpty["'.$allowEmpty .'"] = false;' : '';
			}
		}

		if ($editorEnterBr) {
			$jscode .= "CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;";
		}

		// $this->webroot で、フロントテーマのURLを取得できるようにするため、
		// 一旦テーマをフロントのテーマに切り替える
		$theme = $this->theme;
		$theme = Configure::read('BcSite.theme');
		if ($theme) {
			$this->theme = $theme;
		}

		$themeEditorCsses = [];
		if ($theme) {
			$themeEditorCsses[] = [
				'path' => BASER_THEMES . Configure::read('BcSite.theme') . DS . 'css' . DS . 'editor.css',
				'url' => $this->webroot('/css/editor.css')
			];
		}
		$themeEditorCsses[] = [
			'path' => BASER_VIEWS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'ckeditor' . DS . 'contents.css',
			'url' => $this->webroot('/css/admin/ckeditor/contents.css')
		];

		if ($theme) {
			$sitePrefix = '';
			if (!empty($this->request->data['Site']['name'])) {
				$sitePrefix = $this->request->data['Site']['name'];
			}
			if ($sitePrefix) {
				array_unshift($themeEditorCsses, [
					'path' => BASER_THEMES . Configure::read('BcSite.theme') . DS . 'css' . DS . $sitePrefix . DS . 'editor.css',
					'url' => $this->webroot('/css/' . $sitePrefix . '/editor.css')
				]);
			}
		}

		$this->theme = $theme;

		foreach($themeEditorCsses as $themeEditorCss) {
			if (file_exists($themeEditorCss['path'])) {
				$jscode .= "CKEDITOR.config.contentsCss = ['" . $themeEditorCss['url'] . "'];";
				break;
			}
		}

		$jscode .= "editor_" . $field . " = CKEDITOR.replace('" . $domId . "'," . $this->JqueryEngine->object($options) . ");";
		$jscode .= "editor_{$field}.on('pluginsLoaded', function(event) {";
		if ($editorUseDraft) {
			if ($draftAreaId) {
				$jscode .= "editor_{$field}.draftDraftAreaId = '{$draftAreaId}';";
			}
			if ($publishAreaId) {
				$jscode .= "editor_{$field}.draftPublishAreaId = '{$publishAreaId}';";
			}
			if ($editorReadOnlyPublish) {
				$jscode .= "editor_{$field}.draftReadOnlyPublish = true;";
			}
		}

		$jscode .= " });";
		$draftMode = 'publish';
		$fieldCamelize = Inflector::camelize($field);
		if ($editorUseDraft) {
			$jscode .= "editor_{$field}.on('instanceReady', function(event) {";
			if ($editorDisableDraft) {
				$jscode .= "editor_{$field}.execCommand('changePublish');";
				$jscode .= "editor_{$field}.execCommand('disableDraft');";
			}
			if ($editorDisablePublish) {
				$jscode .= "editor_{$field}.execCommand('changeDraft');";
				$jscode .= "editor_{$field}.execCommand('disablePublish');";
				$draftMode = 'draft';
			}
			$jscode .= <<< EOL
    editor_{$field}.on( 'beforeCommandExec', function( ev ){
    	if(ev.data.name === 'changePublish' || ev.data.name === 'copyPublish') {
    		$("#DraftMode{$fieldCamelize}").val('publish');
    	} else if(ev.data.name === 'changeDraft' || ev.data.name === 'copyDraft') {
    		$("#DraftMode{$fieldCamelize}").val('draft');
    	}
    });
EOL;
			$jscode .= " });";
		}

		$jscode .= "editor_{$field}.on('instanceReady', function(event) {";
		$jscode .= "if(editor_{$field}.getCommand('maximize').uiItems.length > 0) {";

		// ツールバーの表示を切り替え
		$jscode .= <<< EOL
editor_{$field}.getCommand('maximize').on( 'state' , function( e )
    {
        if(this.state == 1) {
			$("#ToolBar").hide();
		} else {
			$("#ToolBar").show();
		}
    });
EOL;

		$jscode .= "}";
		$jscode .= " });";
		$jscode .= "});";

		return $this->BcHtml->scriptBlock($jscode) . '<input type="hidden" id="DraftMode' . $fieldCamelize . '" value="' . $draftMode . '">';
	}

	/**
	 * CKEditorのテキストエリアを出力する
	 *
	 * @param string $fieldName エディタのid, nameなどの名前を指定
	 * @param array $options
	 * @return string
	 */
	public function editor($fieldName, $options = [])
	{

		if (!empty($options['editorUseDraft']) && !empty($options['editorDraftField']) && strpos($fieldName, '.')) {
			list($model) = explode('.', $fieldName);
			$inputFieldName = $fieldName . '_tmp';
			$hidden = $this->BcForm->hidden($fieldName) . $this->BcForm->hidden($model . '.' . $options['editorDraftField']);
		} else {
			$inputFieldName = $fieldName;
			$hidden = '';
		}
		$options['type'] = 'textarea';
		$_options = [];
		foreach($options as $key => $option) {
			if (!preg_match('/^editor/', $key)) {
				$_options[$key] = $option;
			}
		}
		return $this->BcForm->input($inputFieldName, $_options) . $hidden . $this->_build($fieldName, $options);
	}

}
