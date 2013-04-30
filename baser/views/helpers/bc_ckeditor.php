<?php
/* SVN FILE: $Id$ */
/**
 * CKEditorヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class BcCkeditorHelper extends AppHelper {
/**
 * ヘルパー
 * @var array
 * @access public
 */
	var $helpers = array('Javascript', 'Form');
/**
 * スクリプト
 * 既にjavascriptが読み込まれている場合はfalse
 * 
 * @var boolean
 * @access public
 */
	var $_script = false;
/**
 * 初期化状態
 * 複数のCKEditorを設置する場合、一つ目を設置した時点で true となる
 *
 * @var boolean
 * @access public
 */
	var $inited = false;
/**
 * スタイル初期化判定
 * 
 * @var boolean
 * @access protected 
 */
	var $_initedStyles = false;
/**
 * 初期設定スタイル
 * StyleSet 名 basercms
 *
 * @var array
 * @access public
 */
	var $style = array(
					array(	'name' => '青見出し(h3)',
							'element' => 'h3',
							'styles' => array('color'=>'Blue')),
					array(	'name' => '赤見出し(h3)',
							'element' => 'h3',
							'styles' => array('color' => 'Red')),
					array(	'name' => '黄マーカー(span)',
							'element' => 'span',
							'styles' => array('background-color' => 'Yellow')),
					array(	'name' => '緑マーカー(span)',
							'element' => 'span',
							'styles' => array('background-color' => 'Lime')),
					array(	'name' => '大文字(big)',
							'element' => 'big'),
					array(	'name' => '小文字(small)',
							'element' => 'small'),
					array( 	'name' => 'コード(code)',
							'element' => 'code'),
					array( 	'name' => '削除文(del)',
							'element' => 'del'),
					array( 	'name' => '挿入文(ins)',
							'element' => 'ins'),
					array(	'name' => '引用(cite)',
							'element' => 'cite'),
					array( 	'name' => 'インライン(q)',
							'element' => 'q')
			);
	var $toolbars = array(
			'simple' => array(
				array(	'Bold', 'Underline', '-',
						'NumberedList', 'BulletedList', '-', 
						'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
						'Format', 'FontSize', 'TextColor', 'BGColor', 'Link', 'Image'),
				array(	'Maximize', 'ShowBlocks', 'Source')
			),
			'normal' => array(
				array(	'Cut', 'Copy', 'Paste', '-','Undo', 'Redo', '-', 'Bold', 'Italic', 'Underline', 'Strike', '-',
						'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', '-', 
						'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
						'Smiley', 'Table', 'HorizontalRule', '-'),
				array(	'Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor', '-', 'Link', 'Unlink', '-', 'Image'),
				array(	'Maximize', 'ShowBlocks', 'Source')
			)
		);
	function __construct() {
		parent::__construct();

	}
/**
 * CKEditor のスクリプトを構築する
 * 【ボタン一覧】
 * Source			- ソース
 * Save				- 保存
 * NewPage			- 新しいページ
 * Preview			- プレビュー
 * Templates		- テンプレート
 * Cut				- 切り取り
 * Copy				- コピー
 * Paste			- 貼り付け
 * PasteText		- プレーンテキスト貼り付け
 * PasteFromWord	- ワードから貼り付け
 * Print			- 印刷
 * SpellChecker		- スペルチェック
 * Scayt			- スペルチェック設定
 * Undo				- 元に戻す
 * Redo				- やり直し
 * Find				- 検索
 * Replace			- 置き換え
 * SelectAll		- すべて選択
 * RemoveFormat		- フォーマット削除
 * Form				- フォーム
 * Checkbox			- チェックボックス
 * Radio			- ラジオボタン
 * TextField		- 1行テキスト
 * Textarea			- テキストエリア
 * Select			- 選択フィールド
 * Button			- ボタン
 * ImageButton		- 画像ボタン
 * HiddenField		- 不可視フィールド
 * Bold				- 太字
 * Italic			- 斜体
 * Underline		- 下線
 * Strike			- 打ち消し線
 * Subscript		- 添え字
 * Superscript		- 上付き文字
 * NumberedList		- 段落番号
 * BulletedList		- 箇条書き
 * Outdent			- インデント解除
 * Indent			- インデント
 * Blockquote		- ブロック引用
 * JustifyLeft		- 左揃え
 * JustifyCenter	- 中央揃え
 * JustifyRight		- 右揃え
 * JustifyBlock		- 両端揃え
 * Link				- リンク挿入／編集
 * Unlink			- リンク解除
 * Anchor			- アンカー挿入／編集
 * Image			- イメージ
 * Flash			- FLASH
 * Table			- テーブル
 * HorizontalRule	- 横罫線
 * Smiley			- 絵文字
 * SpecialChar		- 特殊文字
 * PageBreak		- 改ページ挿入
 * Styles			- スタイル
 * Format			- フォーマット
 * Font				- フォント
 * FontSize			- フォントサイズ
 * TextColor		- テキスト色
 * BGColor			- 背景色
 * Maximize			- 最大化
 * ShowBlocks		- ブロック表示
 * About			- CKEditorバージョン情報
 * Publish			- 本稿に切り替え
 * Draft			- 草稿に切り替え
 * CopyPublish		- 本稿を草稿にコピー
 * CopyDraft		- 草稿を本稿にコピー
 *
 * @param string $fieldName
 * @param array $ckoptions
 * @return string
 * @access protected
 */
	function _build($fieldName, $ckoptions = array(), $styles = array()) {

		$ckoptions = array_merge(array(
			'language'			=> 'ja',		// 言語
			'type'				=> 'normal',	// ツールバータイプ
			'skin'				=> 'kama',		// スキン
			'width'				=> '600px',		// エディタサイズ
			'height'			=> '300px',		// エディタ高さ
			'collapser'			=> false,		// 
			'baseFloatZIndex'	=> 900,			//
			'stylesSet'			=> 'basercms',	// スタイルセット
			'useDraft'			=> false,		// 草稿利用
			'draftField'		=> false,		// 草稿用フィールド
			'disablePublish'	=> false,		// 本稿利用不可
			'disableDraft'		=> true,		// 草稿利用不可
			'disableCopyDraft'	=> false,		// 草稿へコピー利用不可
			'disableCopyPublish'=> false,		// 本稿へコピー利用不可
			'readOnlyPublish'	=> false,		// 本稿読み込みのみ許可
			'useTemplates'		=> true,		// テンプレート利用
			'enterBr'			=> false		// エンター時に改行を入れる
		), $ckoptions);
		
		extract($ckoptions);
		
		if(empty($ckoptions['toolbar'])) {
			$ckoptions['toolbar'] = $this->toolbars[$ckoptions['type']];
			if($useTemplates) {
				switch ($ckoptions['type']) {
					case 'simple':
						$ckoptions['toolbar'][0][] = 'Templates';
						break;
					case 'normal':
						$ckoptions['toolbar'][1][] = 'Templates';
						break;
				}
			}
		}
		
		if(isset($ckoptions['stylesSet'])) unset($ckoptions['stylesSet']);
		if(isset($ckoptions['useDraft'])) unset($ckoptions['useDraft']);
		if(isset($ckoptions['draftField'])) unset($ckoptions['draftField']);
		if(isset($ckoptions['disablePublish'])) unset($ckoptions['disablePublish']);
		if(isset($ckoptions['disableDraft'])) unset($ckoptions['disableDraft']);
		if(isset($ckoptions['disableCopyDraft'])) unset($ckoptions['disableCopyDraft']);
		if(isset($ckoptions['disableCopyPublish'])) unset($ckoptions['disableCopyPublish']);
		if(isset($ckoptions['readOnlyPublish'])) unset($ckoptions['readOnlyPublish']);
		if(isset($ckoptions['useTemplates'])) unset($ckoptions['useTemplates']);
		
		$jscode = $model = $domId = '';
		if(strpos($fieldName, '.')) {
			list($model, $field) = explode('.', $fieldName);
		}else {
			$field = $fieldName;
		}
		if($useDraft) {
			$publishAreaId = Inflector::camelize($model . '_' . $field);
			$draftAreaId = Inflector::camelize($model . '_' . $draftField);
			$field .= '_tmp';
			$fieldName .= '_tmp';
		}

		$domId = $this->domId($fieldName);
		
		if (!$this->_script) {
			$this->_script = true;
			$this->Javascript->link('/js/ckeditor/ckeditor.js', false);
		}

		if($useDraft) {			
			$lastBar = $ckoptions['toolbar'][count($ckoptions['toolbar'])-1];
			$lastBar = am($lastBar , array( '-', 'Publish', '-', 'Draft'));
			if(!$disableCopyDraft) {
				$lastBar = am($lastBar , array('-', 'CopyDraft'));
			}
			if(!$disableCopyPublish) {
				$lastBar = am($lastBar , array('-', 'CopyPublish'));
			}
			$ckoptions['toolbar'][count($ckoptions['toolbar'])-1] = $lastBar;
		}
		
		$this->Javascript->codeBlock("var editor_" . $field . ";", array("inline" => false));
		$jscode = "$(window).load(function(){";
		if(!$this->inited) {
			$jscode .= "CKEDITOR.addStylesSet('basercms',".$this->Javascript->object($this->style).");";
			$this->inited = true;
		} else {
			$jscode .= '';
		}
		if(!$this->_initedStyles && $styles) {
			foreach($styles as $key => $style) {
				$jscode .= "CKEDITOR.addStylesSet('".$key."',".$this->Javascript->object($style).");";
			}
			$this->_initedStyles = true;
		}

		if($useTemplates) {
			$jscode .= "CKEDITOR.config.templates_files = [ '" . $this->url(array('admin' => true, 'plugin' => null, 'controller' => 'editor_templates', 'action' => 'js')) . "' ];";
		}
		$jscode .= "CKEDITOR.config.extraPlugins = 'draft';";
		$jscode .= "CKEDITOR.config.stylesCombo_stylesSet = '".$stylesSet."';";
		$jscode .= "CKEDITOR.config.protectedSource.push( /<\?[\s\S]*?\?>/g );";
		
		if($enterBr) {
			$jscode .= "CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;";
		}
		
		// $this->webroot で、フロントテーマのURLを取得できるようにするため、
		// 一旦テーマをフロントのテーマに切り替える
		$themeWeb = $this->themeWeb;
		$theme = Configure::read('BcSite.theme');
		if($theme) {
			$this->themeWeb = 'themed/'. $theme .'/';
		}
		
		$themeEditorCsses = array(
			array(
				'path'	=> BASER_THEMES . Configure::read('BcSite.theme') . DS . 'css' . DS . 'editor.css',
				'url'	=> $this->webroot('/css/editor.css')
			),
			array(
				'path'	=> BASER_VENDORS . 'css' . DS . 'ckeditor' . DS . 'contents.css',
				'url'	=> $this->webroot('/css/ckeditor/contents.css')
			)
		);
		
		if(isset($this->data['Page']['page_type'])) {
			$agentPrefix = '';
			if($this->data['Page']['page_type'] == 2) {
				$agentPrefix = Configure::read('BcAgent.mobile.prefix');
			} elseif($this->data['Page']['page_type'] == 3) {
				$agentPrefix = Configure::read('BcAgent.smartphone.prefix');
			}
			if($agentPrefix) {
				array_unshift($themeEditorCsses, array(
					'path'	=> BASER_THEMES . Configure::read('BcSite.theme') . DS . 'css' . DS . $agentPrefix . DS . 'editor.css',
					'url'	=> $this->webroot('/css/' . $agentPrefix . '/editor.css')
				));
			}
		}
		
		$this->themeWeb = $themeWeb;
		
		foreach($themeEditorCsses as $themeEditorCss) {
			if(file_exists($themeEditorCss['path'])) {
				$jscode .= "CKEDITOR.config.contentsCss = ['" . $themeEditorCss['url'] . "'];";
				break;
			}
		}
		
		$jscode .= "editor_" . $field ." = CKEDITOR.replace('" . $domId ."',". $this->Javascript->object($ckoptions) .");";
		$jscode .= "editor_{$field}.on('pluginsLoaded', function(event) {";
		if($useDraft) {
			if($draftAreaId) {
				$jscode .= "editor_{$field}.draftDraftAreaId = '{$draftAreaId}';";
			}
			if($publishAreaId) {
				$jscode .= "editor_{$field}.draftPublishAreaId = '{$publishAreaId}';";
			}
			if($readOnlyPublish) {
				$jscode .= "editor_{$field}.draftReadOnlyPublish = true;";
			}
		}
		$jscode .= " });";
		if($useDraft) {
			$jscode .= "editor_{$field}.on('instanceReady', function(event) {";
			if($disableDraft) {
				$jscode .= "editor_{$field}.execCommand('changePublish');";
				$jscode .= "editor_{$field}.execCommand('disableDraft');";
			}
			if($disablePublish) {
				$jscode .= "editor_{$field}.execCommand('changeDraft');";
				$jscode .= "editor_{$field}.execCommand('disablePublish');";
			}
			$jscode .= " });";
		}
		$jscode .= "});";
		return $this->Javascript->codeBlock($jscode);
		
	}
/**
 * CKEditorのテキストエリアを出力する（textarea）
 *
 * @param string $fieldName
 * @param array $options
 * @param array $options
 * @return string
 */
	function textarea($fieldName, $options = array(), $editorOptions = array(), $styles = array(), $form = null) {
		
		if(!$form){
			$form = $this->Form;
		}
		if(!empty($editorOptions['useDraft']) && !empty($editorOptions['draftField']) && strpos($fieldName,'.')){
			list($model,$field) = explode('.',$fieldName);
			$inputFieldName = $fieldName.'_tmp';
			$hidden = $form->hidden($fieldName).$form->hidden($model.'.'.$editorOptions['draftField']);
		} else {
			$inputFieldName = $fieldName;
			$hidden = '';
		}
		return $form->textarea($inputFieldName, $options) . $hidden . $this->_build($fieldName, $editorOptions, $styles);
		
	}
/**
 * CKEditorのテキストエリアを出力する（input）
 *
 * @param string $fieldName
 * @param array $options
 * @param array $tinyoptions
 * @return string
 */
	function input($fieldName, $options = array(), $editorOptions = array(), $styles = array(), $form = null) {
		
		if(!$form){
			$form = $this->Form;
		}
		if(!empty($editorOptions['useDraft']) && !empty($editorOptions['draftField']) && strpos($fieldName,'.')){
			list($model,$field) = explode('.',$fieldName);
			$inputFieldName = $fieldName.'_tmp';
			$hidden = $form->hidden($fieldName).$form->hidden($model.'.'.$editorOptions['draftField']);
		} else {
			$inputFieldName = $fieldName;
			$hidden = '';
		}
		$options['type'] = 'textarea';
		return $form->input($inputFieldName, $options) . $hidden . $this->_build($fieldName, $editorOptions, $styles, $form);
		
	}
	
}
