<?php
/* SVN FILE: $Id$ */
/**
 * CKEditorヘルパー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class CkeditorHelper extends AppHelper {
/**
 * ヘルパー
 * @var array
 */
	var $helpers = array('Javascript', 'Form');
/**
 * スクリプト
 * 既にjavascriptが読み込まれている場合はfalse
 * @var boolean
 */
	var $_script = false;
/**
 * CKEditor のスクリプトを構築する
 *
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
 * @param	string	$fieldName
 * @param	array	$ckoptions
 * @return	string
 * @access	protected
 */
	function _build($fieldName, $ckoptions = array(), $styles = array()) {

		if(isset($ckoptions['publishAreaId'])) {
			$publishAreaId = $ckoptions['publishAreaId'];
			unset($ckoptions['publishAreaId']);
		} else {
			$publishAreaId = '';
		}

		if(isset($ckoptions['draftAreaId'])) {
			$draftAreaId = $ckoptions['draftAreaId'];
			unset($ckoptions['draftAreaId']);
		} else {
			$draftAreaId = '';
		}
		if(isset($ckoptions['disablePublish'])) {
			$disablePublish = $ckoptions['disablePublish'];
			unset($ckoptions['disablePublish']);
		} else {
			$disablePublish = false;
		}
		if(isset($ckoptions['disableDraft'])) {
			$disableDraft = $ckoptions['disableDraft'];
			unset($ckoptions['disableDraft']);
		} else {
			$disableDraft = true;
		}
		if(isset($ckoptions['disableCopyDraft'])) {
			$disableCopyDraft = $ckoptions['disableCopyDraft'];
			unset($ckoptions['disableCopyDraft']);
		} else {
			$disableCopyDraft = false;
		}
		if(isset($ckoptions['disableCopyPublish'])) {
			$disableCopyPublish = $ckoptions['disableCopyPublish'];
			unset($ckoptions['disableCopyPublish']);
		} else {
			$disableCopyPublish = false;
		}
		if(isset($ckoptions['readOnlyPublish'])) {
			$readOnlyPublish = $ckoptions['readOnlyPublish'];
			unset($ckoptions['readOnlyPublish']);
		} else {
			$readOnlyPublish = false;
		}
		
		$jscode = '';
		if(strpos($fieldName,'.')) {
			list($model,$field) = explode('.',$fieldName);
		}else {
			$field = $fieldName;
		}

		if (!$this->_script) {
			$this->_script = true;
			$this->Javascript->link('/js/ckeditor/ckeditor.js', false);
		}
		$toolbar1 = array('Cut', 'Copy', 'Paste', '-',
							'Undo', 'Redo', '-',
							'Bold', 'Italic', 'Underline', 'Strike', '-',
							'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', '-',
							'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
							'Smiley', 'Table', 'HorizontalRule', '-');
		$toolbar2 = array( 'Styles', 'Format', 'Font', 'FontSize',
							'TextColor', 'BGColor', '-',
							'Link', 'Unlink', '-',
							'Image');
		$toolbar3 = array( 'Maximize', 'ShowBlocks','Source', '-', 'Publish', '-', 'Draft');
		if(!$disableCopyDraft) {
			$toolbar3 = am($toolbar3 , array('-', 'CopyDraft'));
		}
		if(!$disableCopyPublish) {
			$toolbar3 = am($toolbar3 , array('-', 'CopyPublish'));
		}
		$_ckoptions = array('language' => 'ja',
				'skin' => 'kama',
				'width' => '600px',
				'height' => '300px',
				'collapser' => false,
				'baseFloatZIndex' => 900,
				'toolbar'=> array($toolbar1, $toolbar2, $toolbar3)
		);
		$ckoptions = array_merge($_ckoptions,$ckoptions);

		if(!$styles) {
			$styles = array(
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
		}
		$jscode .= "CKEDITOR.addStylesSet('basercms',".$this->Javascript->object($styles).");";
		$jscode .= "CKEDITOR.config.extraPlugins = 'draft,readonly';";
		$jscode .= "CKEDITOR.config.stylesCombo_stylesSet = 'basercms';";
		$jscode .= "var editor_" . $field ." = CKEDITOR.replace('" . $this->__name($fieldName) ."',". $this->Javascript->object($ckoptions) .");";
		$jscode .= "CKEDITOR.config.protectedSource.push( /<\?[\s\S]*?\?>/g );";
		$jscode .= "editor_{$field}.on('pluginsLoaded', function(event) {";
		if($draftAreaId) {
			$jscode .= "editor_{$field}.draftDraftAreaId = '{$draftAreaId}';";
		}
		if($publishAreaId) {
			$jscode .= "editor_{$field}.draftPublishAreaId = '{$publishAreaId}';";
		}
		if($readOnlyPublish) {
			$jscode .= "editor_{$field}.draftReadOnlyPublish = true;";
		}
		$jscode .= " });";
		$jscode .= "editor_{$field}.on('instanceReady', function(event) {";
		if($disableDraft) {
			$jscode .= "editor_{$field}.execCommand('disableDraft');";
		}
		if($disablePublish) {
			$jscode .= "editor_{$field}.execCommand('disablePublish');";
		}
		$jscode .= " });";
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
		return $form->textarea($fieldName, $options) . $this->_build($fieldName, $editorOptions, $styles);
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
		$options['type'] = 'textarea';
		return $form->input($fieldName, $options) . $this->_build($fieldName, $editorOptions, $styles, $form);
	}
}
?>
