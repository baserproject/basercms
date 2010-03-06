<?php
/* SVN FILE: $Id$ */
/**
 * CKEditorヘルパー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
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
     * Adds the tiny_mce.js file and constructs the options
     * 'Source' //ソース
     * 'Save' //保存
     * 'NewPage' //新しいページ
     * 'Preview' //プレビュー
     * 'Templates' //テンプレート
     * 'Cut' //切り取り
     * 'Copy' //コピー
     * 'Paste' //貼り付け
     * 'PasteText' //プレーンテキスト貼り付け
     * 'PasteFromWord' //ワードから貼り付け
     * 'Print' //印刷
     * 'SpellChecker' //スペルチェック
     * 'Scayt' //スペルチェック設定
     * 'Undo' //元に戻す
     * 'Redo' //やり直し
     * 'Find' //検索
     * 'Replace' //置き換え
     * 'SelectAll' //すべて選択
     * 'RemoveFormat' //フォーマット削除
     * 'Form' //フォーム
     * 'Checkbox' //チェックボックス
     * 'Radio' //ラジオボタン
     * 'TextField' //1行テキスト
     * 'Textarea' //テキストエリア
     * 'Select' //選択フィールド
     * 'Button' //ボタン
     * 'ImageButton' //画像ボタン
     * 'HiddenField' //不可視フィールド
     * 'Bold' //太字
     * 'Italic' //斜体
     * 'Underline' //下線
     * 'Strike' //打ち消し線
     * 'Subscript' //添え字
     * 'Superscript' //上付き文字
     * 'NumberedList' //段落番号
     * 'BulletedList' //箇条書き
     * 'Outdent' //インデント解除
     * 'Indent' //インデント
     * 'Blockquote' //ブロック引用
     * 'JustifyLeft' //左揃え
     * 'JustifyCenter' //中央揃え
     * 'JustifyRight' //右揃え
     * 'JustifyBlock' //両端揃え
     * 'Link' //リンク挿入／編集
     * 'Unlink' //リンク解除
     * 'Anchor' //アンカー挿入／編集
     * 'Image' //イメージ
     * 'Flash' //FLASH
     * 'Table' //テーブル
     * 'HorizontalRule' //横罫線
     * 'Smiley' //絵文字
     * 'SpecialChar' //特殊文字
     * 'PageBreak' //改ページ挿入
     * 'Styles' //スタイル
     * 'Format' //フォーマット
     * 'Font' //フォント
     * 'FontSize' //フォントサイズ
     * 'TextColor' //テキスト色
     * 'BGColor' //背景色
     * 'Maximize' //最大化
     * 'ShowBlocks' //ブロック表示
     * 'About' //CKEditorバージョン情報
     * @param string $fieldName Name of a field, like this "Modelname.fieldname", "Modelname/fieldname" is deprecated
     * @param array $tinyoptions Array of TinyMCE attributes for this textarea
     * @return string JavaScript code to initialise the TinyMCE area
     */
    function _build($fieldName, $ckoptions = array()) {

        if(strpos($fieldName,'.')){
            list($model,$field) = explode('.',$fieldName);
        }else{
            $field = $fieldName;
        }

        if (!$this->_script) {
            $this->_script = true;
            $this->Javascript->link('/js/ckeditor/ckeditor.js', false);
        }
        $_ckoptions = array('language' => 'ja',
                            'skin' => 'kama',
                            'width' => '600px',
                            'height' => '300px',
                            'collapser' => false,
                            'baseFloatZIndex' => 900,
                            'toolbar'=>array(array('Cut', 'Copy', 'Paste', '-',
                                                    'Undo', 'Redo', '-',
                                                    'Bold', 'Italic', 'Underline', 'Strike', '-',
                                                    'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', '-',
                                                    'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
                                                    'Smiley', 'Table', 'HorizontalRule', '-'),
                                             array( 'Styles', 'Format', 'Font', 'FontSize',
                                                    'TextColor', 'BGColor', '-', 
                                                    'Link', 'Unlink', '-',
                                                    'Image'),
                                             array( 'Maximize', 'ShowBlocks','Source')
                                             ));
        $ckoptions = array_merge($_ckoptions,$ckoptions);
        return $this->Javascript->codeBlock("var editor_" . $field ." = CKEDITOR.replace('" . $this->__name($fieldName) ."',". $this->Javascript->object($ckoptions) .");CKEDITOR.config.protectedSource.push( /<\?[\s\S]*?\?>/g );");
    }
    /**
     * CKEditorのテキストエリアを出力する（textarea）
     * @param string $fieldName
     * @param array $options
     * @param array $options
     * @return string 
     */
    function textarea($fieldName, $options = array(), $editorOptions = array()) {
        return $this->Form->textarea($fieldName, $options) . $this->_build($fieldName, $editorOptions);
    }
    /**
     * CKEditorのテキストエリアを出力する（input）
     * @param string $fieldName
     * @param array $options
     * @param array $tinyoptions
     * @return string
     */
    function input($fieldName, $options = array(), $editorOptions = array()) {
        $options['type'] = 'textarea';
        return $this->Form->input($fieldName, $options) . $this->_build($fieldName, $editorOptions);
    }
}
?>